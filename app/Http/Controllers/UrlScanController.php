<?php

namespace App\Http\Controllers;

use App\Models\Rule as RuleModel;
use App\Models\UrlScanResult;
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

        foreach ($data['results'] as $result) {
            $rule = RuleModel::where('axe_id', $result['rule_id'])->first();
            if ($rule) {
                $urlScanResult = new UrlScanResult;
                $urlScanResult->url_scan_id = $urlScan->id;
                $urlScanResult->rule_id = $rule->id;
                $urlScanResult->result = $result['result'];
                $urlScanResult->impact = $result['impact'];
                $urlScanResult->html = $result['html'];
                $urlScanResult->message = $result['message'];
                $urlScanResult->save();
                $results[] = $urlScanResult;
            } else {
                // TODO: Dont bomb here For all results
                return response()->json([
                    "error" => "Invalid request", 
                    "messages" => "aXe rule not found `{$result['rule_id']}`"
                ], 400);
            }
        }
        
        // Set some validation rules for the submitted data
        return response()->json($results);
    }
}
