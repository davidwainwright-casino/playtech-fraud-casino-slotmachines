<?php
namespace Wainwright\CasinoDog\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UrlscanRatelimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $domain = parse_url($request->url(), PHP_URL_HOST);

        // Remove www prefix if necessary
        if (strpos($domain, 'www.') === 0) $domain = substr($domain, 4);

        // In my case, I had a list of pre-defined, supported domains
        foreach(Config::get('app.clients') as $client) {
            if (in_array($domain, $client['domains'])) {
                // From now on, every controller will be able to access
                // current domain and its settings via $request object
                $request->client = $client;
                return $next($request);
            }
        }

        abort(404);
    }
}