<?php

namespace App\Http\Middleware;

use App\Models\UrlScan;
use Closure;
use Illuminate\Http\Request;

class EnsureScanTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get Authorization header
        $token = $request->bearerToken();

        // Ensure that there is a Crawl task with a valid Token that isn't in the 
        // failed or success state.
        $urlScan = UrlScan::where('token', $token)
            ->whereNotIn('status', ['failed', 'success'])->first();

        // Abort if the Crawl task is invalid
        if (!$urlScan) return abort(401);

        // Add the Crawl task to the request
        $request->attributes->add(['url_scan' => $urlScan]);

        return $next($request);
    }
}
