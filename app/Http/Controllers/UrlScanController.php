<?php

namespace App\Http\Controllers;

use App\Models\Rule as RuleModel;
use App\Models\UrlScanAccessibilityResult;
use App\Models\UrlScanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UrlScanController extends Controller
{
    /**
     * Allows a worker to update the current status of a url scan
     * 
     * This should be passed through the EnsureScanTokenIsValid middleware,
     * it expects that the Request attribute 'url_scan' is present and valid. 
     */
    public function update(Request $request) {
        
        // Get JSON data from body 
        $data = $request->json()->all();
        
        // Set some validation rules for the submitted data
        $rules = [
            'status' => [
                'required',
                Rule::in(['processing', 'success', 'failed'])
            ],
            'message' => 'nullable|string',
        ];

        // Validate the body of the request
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                "error" => "Invalid request", 
                "messages" => $validator->getMessageBag()
            ], 400);
        }

        // Update the crawl
        $urlScan = $request->get('url_scan');
        $urlScan->status = $data['status'];
        $urlScan->messages = $data['messages'] ?: null;
        $urlScan->save();

        return response()->json($urlScan);
    }

    /**
     * Parses a given mime into a db category
     */
    public function parseMimeToCategory($mime) {
        switch ($mime) {
            case 'text/html':
            case 'text/xml':
                return 'document';
            case 'text/css':
                return 'style';
            case 'application/javascript':
                return 'script';
            case 'image/png':
            case 'image/svg+xml':
            case 'image/gif':
            case 'image/jpeg':
            case 'image/webp':
            case 'image/bmp':
            case 'image/tiff':
            case 'image/x-icon':
                return 'image';
            case 'font/ttf':
            case 'font/woff':
            case 'font/woff2':
                return 'font';
            case 'audio/ac3':
            case 'audio/aac':
            case 'audio/aiff':
            case 'audio/basic':
            case 'audio/flac':
            case 'audio/midi':
            case 'audio/mpeg':
            case 'audio/mp4':
            case 'audio/mpeg3':
            case 'audio/ogg':
            case 'audio/vorbis':
            case 'audio/wav':
            case 'audio/webm':
            case 'audio/mp3':
                return 'audio';
            case 'video/h261':
            case 'video/h263':
            case 'video/h264':
            case 'video/h265':
            case 'video/mp4':
                return 'video';
            default:
                return 'other';
        }
    }

    /**
     * Allows a worker to update the current status of a url scan
     * 
     * This should be passed through the EnsureScanTokenIsValid middleware,
     * it expects that the Request attribute 'url_scan' is present and valid. 
     */
    public function results(Request $request) {
        
        // Get JSON data from body 
        $data = $request->json()->all();

        // URL Scan
        $urlScan = $request->get('url_scan');

        $results = [];

        foreach ($data['accessibility'] as $result) {
            $rule = RuleModel::where('axe_id', $result['rule_id'])->first();
            if ($rule) {
                $urlScanAccResult = new UrlScanAccessibilityResult;
                $urlScanAccResult->url_scan_id = $urlScan->id;
                $urlScanAccResult->rule_id = $rule->id;
                $urlScanAccResult->scan_id = $urlScan->scan_id;
                $urlScanAccResult->url_id = $urlScan->url_id;
                $urlScanAccResult->result = $result['result'];
                $urlScanAccResult->impact = $result['impact'];
                $urlScanAccResult->html = $result['html'];
                $urlScanAccResult->message = $result['message'];
                $urlScanAccResult->save();
                $results[] = $urlScanAccResult;
            } else {
                // TODO: Dont bomb here For all results
                return response()->json([
                    "error" => "Invalid request", 
                    "messages" => "aXe rule not found `{$result['rule_id']}`"
                ], 400);
            }
        }

        // $table->foreignId('scan_id')->constrained('scans')->cascadeOnDelete(); 
        // $table->foreignId('url_scan_id')->constrained('url_scans')->cascadeOnDelete();
        // $table->foreignId('url_id')->constrained('urls')->cascadeOnDelete(); 
        // $table->string('uri', 4096);
        // $table->string('mime');
        // $table->enum('category', ['image', 'document', 'script', 'style', 'font', 'video', 'audio', 'other'])->default('other');
        // $table->integer('status')->default(200);
        // $table->float('duration', 8, 2)->default(0.00);
        // $table->bigInteger('size')->default(0);
        // $table->string('protocol');

        /*  name: metric.name,
            url: {
                protocol: url.protocol,
                hostname: url.hostname,
                path: url.pathname,
                query: url.search,
                mime: mime.lookup(url.pathname),
            },
            initiatorType: metric.initiatorType,
            duration: metric.duration.toFixed(2),
            transferSize: metric.transferSize
        */

        foreach ($data['metrics'] as $metric) {
            $urlScanResponse = new UrlScanRequest;
            $urlScanResponse->url_scan_id = $urlScan->id;
            $urlScanResponse->scan_id = $urlScan->scan_id;
            $urlScanResponse->url_id = $urlScan->url_id;
            $urlScanResponse->uri = $metric['name'];
            $urlScanResponse->mime = $metric['url']['mime'];
            $urlScanResponse->category = $this->parseMimeToCategory($metric['url']['mime']);
            $urlScanResponse->status = 200;
            $urlScanResponse->duration = $metric['duration'];
            $urlScanResponse->size = $metric['transferSize'];
            $urlScanResponse->protocol = $metric['url']['protocol'];
            
            $urlScanResponse->save();
        }

        // TODO: Save the request metrics here if needed??!?!!
        // $data['metrics']['stats'] = {
        // "metrics": {
        //     "Timestamp": 31262.91123,
        //     "Documents": 11,
        //     "Frames": 5,
        //     "JSEventListeners": 27,
        //     "Nodes": 3067,
        //     "LayoutCount": 6,
        //     "RecalcStyleCount": 5,
        //     "LayoutDuration": 0.284671,
        //     "RecalcStyleDuration": 0.069218,
        //     "ScriptDuration": 0.237569,
        //     "TaskDuration": 1.846727,
        //     "JSHeapUsedSize": 13796744,
        //     "JSHeapTotalSize": 20647936
        //   },
        // }
        
        // Set some validation rules for the submitted data
        return response()->json($results);
    }
}
