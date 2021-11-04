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
        $urlScan->save();

        return response()->json($urlScan);
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

        foreach ($data['metrics']['responses'] as $response) {
            $urlScanResponse = new UrlScanRequest;
            $urlScanResponse->url_scan_id = $urlScan->id;
            $urlScanResponse->scan_id = $urlScan->scan_id;
            $urlScanResponse->url_id = $urlScan->url_id;
            $urlScanResponse->status = $response['status'];
            $urlScanResponse->size = $response['size'];
            $urlScanResponse->mime = $response['mime'];
            $urlScanResponse->uri = $response['uri'];
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
