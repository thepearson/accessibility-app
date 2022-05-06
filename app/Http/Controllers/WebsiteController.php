<?php

namespace App\Http\Controllers;
use App\Models\Website;
use App\Models\Scan;
use App\Models\Url;
use App\Models\UrlScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Services\Rabbitmq\Client;
use Illuminate\Support\Facades\DB;

use function Psy\debug;

class WebsiteController extends Controller
{
    public function index(Request $request) {
        return Inertia::render('Sites/List', [
            'websites' => $request->user()->currentTeam->websites,
        ]);
    }

    public function add(Request $request) {
        $request->validate([
            'name' => 'required|string|min:4',
            'base_url' => 'required|url|min:12',
            'autoscan' => 'sometimes|boolean'
        ]);

        $website = new Website;
        $website->name = $request->input('name');
        $website->base_url = $request->input('base_url');
        $request->user()->currentTeam->websites()->save($website);

        return Redirect::route('sites.list');
    }

    public function delete(Request $request, $id)
    {
        Website::find($id)->delete();
        return Redirect::route('sites.list');
    }

    public function scan(Request $request, Client $client, $id) 
    {
        $website = Website::find($id);

        // Create the new scan
        $scan = new Scan;
        $scan->website_id = $website->id;
        $scan->save();

        // Lets add the URLS to the scan
        foreach ($website->urls->where('enabled', true) as $url) {
            $urlScan = UrlScan::create($scan, $url);
            
            $canonicalUrl = rtrim($website->base_url, '/') . $url->url;
            $message = [
                'url' => $canonicalUrl,
                'meta' => [
                    'token' => $urlScan->token,
                    'hostname' => env('CALLBACK_HOST', 'http://localhost'),
                    'update' => '/api/url_scan/update',
                    'results' => '/api/url_scan/results',
                ],
            ];

            // Queue the AMPQ reqquest
            $client->connect();
            $client->message(json_encode($message), 'scan');
            
            $urlScan->save();
        }

        return Redirect::route('sites.list');
    }

    public function show(Request $request, $id) 
    {
        $website = Website::find($id);
        $latestScan = $website->latestScan;

        // top 10 accessibility violations
        $top_voilations = DB::table('url_scan_accessibility_results')
            ->join('rules', 'url_scan_accessibility_results.rule_id', '=', 'rules.id')
            ->select(
                DB::raw('COUNT(url_scan_accessibility_results.id) AS pages'),
                'rules.axe_id',
                'url_scan_accessibility_results.result',
                'url_scan_accessibility_results.impact',
                'url_scan_accessibility_results.html'
            )
            ->where('url_scan_accessibility_results.scan_id', $latestScan->id)
            ->groupBy('rules.axe_id', 'url_scan_accessibility_results.result', 'url_scan_accessibility_results.impact', 'url_scan_accessibility_results.html')
            ->orderBy('pages', 'desc')
            ->limit(10)
            ->get();

        // Order site pages by total size
        $_largest_pages = DB::table('url_scan_requests')
            ->join('urls', 'url_scan_requests.url_id', '=', 'urls.id')
            ->select(
                'urls.id',
                DB::raw('SUM(url_scan_requests.size) AS size')
            )
            ->where('url_scan_requests.scan_id', $latestScan->id)
            ->where('url_scan_requests.status', 200)
            ->groupBy('urls.id')
            ->orderBy('size', 'desc')
            ->limit(10)
            ->get();
        
        $largest_pages = [];
        foreach ($_largest_pages as $page) {
            $url = Url::find($page->id);
            $url->size = $page->size;
            $largest_pages[] = $url;
        }

        //dd($largest_pages->toArray());
        /*
        // Group wb requests by size and mime when given a scan_id.
            SELECT 
                urls.url, 
                sr.mime, 
                SUM(sr.size) 
            FROM url_scan_requests AS sr 
            INNER JOIN urls 
                ON sr.url_id = urls.id 
            WHERE sr.scan_id=1 
                AND sr.status=200 
            GROUP BY sr.url_id, sr.mime;
        */

        // Show the top 10 files used on the most pages.
        $largest_files = DB::table('url_scan_requests')
            ->select(
                'uri', 
                'category', 
                'size', 
                DB::raw('COUNT(id) AS number')
            )
            ->where('scan_id', $latestScan->id)
            ->groupBy('uri', 'category', 'size')
            ->orderBy('number', 'desc')
            ->orderBy('size', 'desc')
            ->limit(10)
            ->get();

        // select scan_id, SUM(size)/COUNT(DISTINCT url_id) AS avg_size from url_scan_requests WHERE scan_id=3 GROUP BY scan_id;
        $average_page_size = DB::table('url_scan_requests')
            ->select(
                'scan_id', 
                DB::raw('SUM(size)/COUNT(DISTINCT url_id) AS avg_size')
            )
            ->where('scan_id', $latestScan->id)
            ->groupBy('scan_id')
            ->get()->first();

        // select scan_id, SUM(size)/COUNT(DISTINCT url_id) AS avg_size from url_scan_requests WHERE scan_id=3 GROUP BY scan_id;
        $average_page_duration = DB::table('url_scan_requests')
            ->select(
                'scan_id', 
                DB::raw('SUM(duration)/COUNT(DISTINCT url_id) AS avg_duration')
            )
            ->where('scan_id', $latestScan->id)
            ->groupBy('scan_id')
            ->get()->first();

        // SELECT COUNT(*) AS pages FROM url_scans WHERE scan_id=2
        $scanned_pages = DB::table('url_scans')
            ->select(
                DB::raw('COUNT(*) AS pages')
            )->where('scan_id', $latestScan->id)
            ->get()->first();

        // SELECT COUNT(*) AS violations FROM url_scan_accessibility_results WHERE result='violations' AND impact IN ('serious', 'critical');
        $acc_violations = DB::table('url_scan_accessibility_results')
            ->select(
                DB::raw('COUNT(*) AS violations')
            )
            ->where('scan_id', $latestScan->id)
            ->where('result', 'violations')
            ->whereIn('impact', ['serious', 'critical'])
            ->get()->first();

        return Inertia::render('Sites/Show', [
            'title' => 'Overview',
            'data' => [
                'stats' => [
                    'acc_violations' => $acc_violations,
                    'scanned_pages' => $scanned_pages,
                    'average_page_size' => $average_page_size,
                    'average_page_duration' => $average_page_duration,
                ],
                'largest_files' => $largest_files,
                'largest_pages' => $largest_pages,
                'top_voilations' => $top_voilations,
            ],
            'website' => $website,
            'latestScan' => $latestScan,
            'violations' => ($latestScan) ? $latestScan->urlScanAccessibilityResults->count() : null
        ]);
    }

    public function accessibility(Request $request, $id) 
    {
        $website = Website::find($id);
        $latestScan = $website->latestScan;

        return Inertia::render('Sites/Accessibility', [
            'website' => $website,
            'latestScan' => $latestScan,
            'violations' => ($latestScan) ? $latestScan->urlScanAccessibilityResults->count() : null
        ]);
    }

    public function performance(Request $request, $id) 
    {
        $website = Website::find($id);
        $latestScan = $website->latestScan;

        return Inertia::render('Sites/Performance', [
            'website' => $website,
            'latestScan' => $latestScan,
            'violations' => ($latestScan) ? $latestScan->urlScanAccessibilityResults->count() : null
        ]);
    }

    public function scans(Request $request, $id) 
    {
        $website = Website::find($id);
        $latestScan = $website->latestScan;
        if ($latestScan) {
            $latestScan = $latestScan->first();
        }

        return Inertia::render('Sites/Scans', [
            'website' => $website,
            'active_scan' => $website->hasActiveScan(),
            'scans' => $website->scans,
            'latestScan' => $latestScan
        ]);
    }
}
