<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\Website;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function index(Request $request, $id) {
        $website = Website::find($id);
        return Inertia::render('Sites/Urls', [
            'website' => $website,
            'urls' => $website->urls
        ]);
    }

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

    public function delete(Request $request, $id, $url_id) {
        Url::find($url_id)->delete();
        return Redirect::route('sites.urls.list', [ 'id' => $id ]);
    }

    public function scan(Request $request, $id) {
        
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
