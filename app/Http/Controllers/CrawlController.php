<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CrawlController extends Controller
{
    public function index(Request $request) {

    }

    /**
     * Allows a worker to update the current status of a crawl
     * 
     * This should be passed through the EnsureCrawlTokenIsValid middleware,
     * it expects that the Request attribute 'crawl' is present and valid. 
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
            'total' => [
                'numeric',
                'nullable',
            ],
            'complete' => [
                'numeric',
                'nullable',
            ],
            // 'message' => [
            //     'string',
            //     'nullable',
            // ]
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
        $crawl = $request->get('crawl');
        $crawl->status = $data['status'];
        $crawl->total = $data['total'];
        $crawl->complete = $data['complete'];
        $crawl->save();

        return response()->json($crawl);
    }
}
