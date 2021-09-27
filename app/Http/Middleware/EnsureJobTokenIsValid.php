<?php

namespace App\Http\Middleware;

use App\Models\Job;
use Closure;
use Illuminate\Http\Request;

class EnsureJobTokenIsValid
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

        // Ensure that there is a Job with a valid Token that isn't in the 
        // failed or success state.
        $job = Job::where('token', $token)
            ->whereNotIn('status', ['failed', 'success'])->first();

        // Abort if the Job is invalid
        if (!$job) return abort(401);

        // Add the job to the request
        $request->attributes->add(['job' => $job]);

        return $next($request);
    }
}
