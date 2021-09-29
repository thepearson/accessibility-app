<?php

namespace App\Http\Middleware;

use App\Models\Crawl;
use Closure;
use Illuminate\Http\Request;

class EnsureCrawlTokenIsValid
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
        $crawl = Crawl::where('token', $token)
            ->whereNotIn('status', ['failed', 'success'])->first();

        // Abort if the Crawl task is invalid
        if (!$crawl) return abort(401);

        // Add the Crawl task to the request
        $request->attributes->add(['crawl' => $crawl]);

        return $next($request);
    }
}
