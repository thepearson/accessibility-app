<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\Website;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
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
        $url = new Url();
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
}
