<?php
namespace Wainwright\CasinoDog\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UrlscanRatelimitMiddleware
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
                ->block(0)->allow(1)->every(5)
                ->then(function () use ($job, $next) {
                    // Lock obtained...
                    $next($job);
                }, function () use ($job) {
                    // Could not obtain lock...
                    $job->release(5);
                });
    }
}