<?php

namespace App\Http\Controllers;
use App\Models\Website;
use App\Models\Scan;
use App\Models\UrlScan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Services\Rabbitmq\Client;

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
        foreach ($website->urls as $url) {
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

        return Inertia::render('Sites/Show', [
            'website' => $website,
            'latestScan' => $latestScan,
            'violations' => $latestScan->urlScanAccessibilityResults->count()
        ]);
    }
}
