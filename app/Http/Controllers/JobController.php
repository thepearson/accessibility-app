<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    public function index(Request $request) {

    }

    /**
     * Allows a worker to update the current status of a job
     * 
     * This should be passed through the EnsureJobTokenIsValid middleware,
     * it expects that the Request attribute 'job' is present and valid. 
     */
    public function update(Request $request) {
        
        // Get JSON data from body 
        $data = $request->json()->all();
        
        // Set some validation rules for the submitted data
        $rules = [
            'status' => [
                'required',
                Rule::in(['processing', 'success', 'failed'])
            ]
        ];

        // Validate the body of the request
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                "error" => "Invalid request", 
                "messages" => $validator->getMessageBag()
            ], 400);
        }

        // Update the job
        $job = $request->get('job');
        $job->status = $data['status'];
        $job->save();

        return response()->json($job);
    }
}
