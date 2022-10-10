<?php
namespace Wainwright\CasinoDog\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class JobRatelimitMiddleware
{
    /**
     * Process the queued job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        Redis::throttle('throttlekey')
                ->block(0)->allow(3)->every(1)
                ->then(function () use ($job, $next) {
                    // Lock obtained...
                    $next($job);
                }, function () use ($job) {
                    // Could not obtain lock...
                    $job->release(1);
                });
    }
}