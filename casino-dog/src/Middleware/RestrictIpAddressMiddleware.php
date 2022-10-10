<?php
namespace Wainwright\CasinoDog\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictIpAddressMiddleware
{
     // Blocked IP addresses
     public function restrictedIp(){
        return config('casino-dog.firewall.allowed_ip');
     }

     public function restrictedEnabled() {
        return config('casino-dog.firewall.restrict_all_routes');
     }

     public function restrictedHttpsOnly() {
        return config('casino-dog.firewall.https_only');
     }
     public function unrestrictGameSession() {
        return config('casino-dog.firewall.unrestrict_game_session');

     }

     public function getIp($request) {
        $kernel_casinodog = new \Wainwright\CasinoDog\CasinoDog;
        $get_ip = $kernel_casinodog->getIp($request);
        return $get_ip;
     }

     /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if(request()->is('g')) {
            if($this->unrestrictGameSession()) {
                if($this->restrictedHttpsOnly()) {
                    if (!$request->isSecure()) {
                        return redirect()->secure($request->getRequestUri());
                    } else {
                        return $next($request);
                    }
                }
                return $next($request);
            }
        }


        if($this->restrictedEnabled()) {
            if (!in_array($this->getIp($request), $this->restrictedIp())) {
                return response()->json(['message' => "You are not allowed to access this site.", 'ip' => $this->getIp($request)]);
            }
        }
        if($this->restrictedHttpsOnly()) {
            if (!$request->isSecure()) {
                return redirect()->secure($request->getRequestUri());
            }
        }
        return $next($request);
    }
}
