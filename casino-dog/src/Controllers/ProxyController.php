<?php

namespace Wainwright\CasinoDog\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class ProxyController
{
    /**
     * Launch proxy job based on type. $allowed_hosts only is used currently for type: 'game_session_static'
     *
     * @param [string] $type
     * @param [string] $url
     * @param [string] $allowed_hosts
     * @return void
     */
    public function launch_job($type, $url, $allowed_hosts = NULL)
    {
        if($type === 'json_softswiss') {
            $proxy = new ProxyController;
            $json = $proxy->json_softswiss($url);
            return $json;
        } elseif($type === 'game_session_static')
        {
            $proxy = new ProxyController;
            $proxy_url = $proxy->game_session_get_session($allowed_hosts);
            $json = $proxy->game_session_static($url, $proxy_url);

            if($json === false) {
                $proxy->game_session_forget_session($allowed_hosts);
            }
            return $json;
        }
    }

    /**
     * Get wainwright-proxy config from cache storage, if cache missed it will fire retrieve_config()
     *
     * @return void
     */
    public function configsheet()
    {
        $cache = Cache::get('wainwright_proxy');
        if(!$cache) {
            return $this->retrieve_config();
        } else {
            return $cache;
        }
    }

    /**
     * Retrieve wainwright-proxy config from proxy server itself
     *
     * @return void
     */
    public function retrieve_config()
    {
        $sheet = Http::get(config('casino-dog.wainwright_proxy.config_url'));
        $sheet = json_decode($sheet, true);
        if(isset($sheet['proxy_entrypoints'])) {
        Cache::put('wainwright_proxy', $sheet, 15);
        return $sheet;
        } else {
            return false;
        }
    }

    /**
     * Proxy meant for json gamelist retrieval & meta data crawling, use Selenium or other solutions for actual game proxy if funcs don't work
     *
     * @param [string] $url
     * @return void
     */
    public function json_softswiss($url)
    {
        $config = $this->configsheet();
        if(isset($config['proxy_entrypoints'])) {
            $proxy_url = $config['proxy_entrypoints']['json_softswiss']['api_url'];
            $proxy_url = str_replace('[target_url]', $url, $proxy_url);
            $http_client = json_decode(Http::timeout(10)->get($proxy_url), true);
            return $http_client;
        } else {
            Cache::forget('wainwright_proxy');
            return false;
        }
    }

    /**
     * Fire gamesession
     *
     * @param [string] $url
     * @param [string] $proxy_url
     * @return void
     */
    public function game_session_static($url, $proxy_url)
    {
            $final_url = str_replace('[target_url]', $url, $proxy_url);
            Log::notice('Final URL game_session_static(): '.$final_url);
            $http_client = Http::timeout(10)->get($final_url);
            if($http_client->status() === 403) {
                Log::warning('Proxy returned error 403: '.$http_client);
                return false;
            }
            return $http_client;
    }

    /**
     * Create session (valid for 120 minutes) on proxy end, stores session_id (used to enter proxy) itself in cache
     * $allowed_hosts is which will be allowed to target within the proxy session. Multiple hosts should be seperated with comma, f.e: "bets.io,www.bets.io,bitstarz.com"
     *
     * @param [string] $allowed_hosts
     * @return void
     */
    public function game_session_get_session($allowed_hosts) {
        $session_url = Cache::get('wainwright_game_sessionproxy:'.$allowed_hosts);
        if(!$session_url) {
            $config = $this->configsheet();
            $create_session_url = $config['proxy_entrypoints']['game_session_static']['create_session']['url'];
            $create_session_http_client = json_decode(Http::withHeaders(['x-wainwright-allowedhosts' => $allowed_hosts])->timeout(10)->get($create_session_url), true);
            $session_id = $create_session_http_client['session_id'];
            $build_session_url = $config['proxy_entrypoints']['game_session_static']['api_url'];
            $session_url = str_replace('[session_id]', $session_id, $build_session_url);
            $store_cache = Cache::put('wainwright_game_sessionproxy:'.$allowed_hosts, $session_url, now()->addMinutes(30));
        }
        return $session_url;
    }

    /**
     * Remove session from cache (used to retrigger cache in case session time runs out before cache timing runs out)
     *
     * @param [string] $allowed_hosts
     * @return void
     */
    public function game_session_forget_session($allowed_hosts) {
        Cache::forget('wainwright_game_sessionproxy:'.$allowed_hosts);
    }
}