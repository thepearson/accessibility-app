<?php

namespace App\Http\Controllers;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

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
        ]);

        $website = new Website;
        $website->name = $request->input('name');
        $website->base_url = $request->input('base_url');
        $request->user()->currentTeam->websites()->save($website);

        return Redirect::route('sites.list');
    }

    public function delete(Request $request, $id) {
        Website::find($id)->delete();
        return Redirect::route('sites.list');
    }

    public function settings(Request $request) {
        
    }

    public function urls(Request $request, $id) {
        $website = Website::find($id)->first();
        return Inertia::render('Sites/Urls', [
            'website' => $website,
            'urls' => $website->urls()
        ]);
    }

    public function show(Request $request, $id) {
        return Inertia::render('Sites/List', [
            'website' => Website::find($id)->get(),
        ]);
    }
}
