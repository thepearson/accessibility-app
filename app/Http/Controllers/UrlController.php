<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\Job;
use App\Models\Website;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Services\Rabbitmq\Client;

class UrlController extends Controller
{
    /**
     * List all URLS
     */
    public function index(Request $request, $id) {
        $website = Website::find($id);

        $job = Job::where('website_id', $id)
            ->where('type', 'crawl')
            ->whereIn('status', ['queued', 'processing'])->first();
    
        return Inertia::render('Sites/Urls', [
            'website' => $website,
            'urls' => $website->urls,
            'active_job' => $job
        ]);
    }

    /**
     * Web route for adding a URL
     */
    public function add(Request $request, $id) {
        $request->validate([
            'url' => 'required|string|min:1',
            'autoscan' => 'sometimes|boolean'
        ]);

        $website = Website::find($id);
        
        $url = new Url;
        $url->url = $request->input('url');
        $url->website_id = $website->id;
        $url->save();

        return Redirect::route('sites.urls.list', [ 'id' => $website->id ]);
    }


    /**
     * Web route for deleting a URL
     */
    public function delete(Request $request, $id, $url_id) {
        Url::find($url_id)->delete();
        return Redirect::route('sites.urls.list', [ 'id' => $id ]);
    }

    /**
     * 
     */
    public function scan(Request $request, Client $client, $id) {

        Url::where('website_id', $id)->delete();

        $website = Website::find($id);

        $type = 'crawl';
        $job = Job::create($type);
        
        $message = [
            'base_url' => $website->base_url
        ];

        // Update base job
        $job->data = json_encode($message);
        $job->website_id = $website->id;

        $message['meta'] = [
            'token' => $job->token,
            'hostname' => env('CALLBACK_HOST', 'http://localhost'),
            'status' => '/api/job/update',
            'data' => "/api/sites/{$website->id}/urls"
        ];

        // Queue the AMPQ reqquest
        $client->connect();
        $client->message(json_encode($message), $type);

        // Save the job
        $job->save();

        return Redirect::route('sites.urls.list', [ 'id' => $website->id ]);
    }

    public function addUrls(Request $request, $id) {
        // Get JSON data from body 
        $data = $request->json()->all();

        // Set some validation rules for the submitted data
        $rules = [
            'urls' => [
                'required',
                'array'
            ]
        ];

        // Validate the request
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                "error" => "Invalid request", 
                "messages" => $validator->getMessageBag()
            ], 400);
        }

        // Create the URLS
        $website = Website::find($id);
        foreach ($data['urls'] as $u) {
            if (strlen(trim($u)) > 0) {
                $url = Url::firstOrNew(['url' => $u, 'website_id' => $website->id]);
                $url->save();
            }
        }

        // Respond
        return response()->json(["message" => "Successfully added urls"]);
    }
}
