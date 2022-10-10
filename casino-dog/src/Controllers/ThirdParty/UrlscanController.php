<?php

namespace Wainwright\CasinoDog\Controllers\ThirdParty;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Wainwright\CasinoDog\CasinoDog;
use Illuminate\Http\Response;
use Wainwright\CasinoDog\Jobs\UrlscanInit;
use Wainwright\CasinoDog\Models\CrawlerData;
use Wainwright\CasinoDog\Jobs\UrlscanCheck;

class UrlscanController
{

    public function init_http_client($url) {
        $http = Http::withHeaders([
            'API-Key' => config('casino-dog.urlscan_apikey')
        ])->post('https://urlscan.io/api/v1/scan', [
            'url' => $url,
            'visibility' => 'public'
        ]);
        return $http;
    }

    public function check_state_http_client($uuid) {
        $http = Http::get('https://urlscan.io/api/v1/result/'.$uuid);
        return $http;
    }

    public function get_dom_http_client($uuid) {
        $http = Http::get('https://urlscan.io/dom/'.$uuid);
        return $http;
    }

    public function urlscan_start_job(string $url) {
        $job = UrlscanInit::dispatch($url)->delay(now()->addSeconds(60));
        if($job) {
            return 'success';
        } else {
            return 'failed';
        }
    }

    public function urlscan_check_job(string $uuid)
    {

        try {
        $check_http = $this->get_dom_http_client($uuid);
        $update = CrawlerData::where('extra_id', $uuid)->first();
        $expired_bool = $update->expired_bool;
        if($expired_bool !== 1) { // check if state is already set on failed (so we don't check again)
            if($check_http->status() !== '404') {
                $get_dom = $this->get_dom_http_client($uuid);
                $update->state = 'COMPLETED';
                $update->updated_at = now();
                $update->expired_bool = 1;
                $update->result = collect(array('body' => $get_dom->body()));
                $update->save();
            } else {
                $get_try = explode('_', $update->state);
                if(!is_numeric($get_try[1])) { // check if state is numeric, exploding the _ and counting up
                $update->state = 'CHECKED_1';
                $update->save();
                } elseif($get_try[1] > 10) { // failed
                    $update->state = 'FAILED';
                    $update->state_message = 'Did not get result after '.$get_try[1].' tries.';
                    $update->expired_bool = 1;
                    $update->save();
                } else { // add try number
                    $update->state = 'CHECKED_'.($get_try[1] + 1);
                    $update->save();
                }
            }
        }
        return $update;

        } catch(\Exception $e) {
            $data = [
                'state' => 'ERROR',
                'status' => 400,
                'state_message' => $e->getMessage(),
            ];
            return $data;
        }
    }

    public function urlscan_retrieve_job(string $uuid)
    {
        $get_job = CrawlerData::where('extra_id', $uuid)->first();
        $return[] = $get_job;
        $return[] = [
            'links' => [
                'api_result_url' => 'https://urlscan.io/api/v1/result/'.$uuid,
                'dom' => 'https://urlscan.io/dom/'.$uuid,
                'screenshot' => 'https://urlscan.io/screenshots/'.$uuid.'.png',
            ],
        ];
        return $return;
    }

    public function urlscan_retrieve_dom(string $uuid)
    {
        $update = CrawlerData::where('extra_id', $uuid)->first();
        return $update['result'] ?? 'No dom recorded. Check https://urlscan.io/dom/'.$uuid;
    }


    public function init_request(array $data) {

        try {
        $url_scan = new UrlscanController();
        $http_request = $this->init_http_client($data['url']);
        $http_decoded = json_decode($http_request->body() ?? NULL, true);
            $data = [
                'state' => 'SUBMIT_SUCCESS',
                'state_message' => 'Urlscan Result: https://urlscan.io/result/'.$http_decoded['uuid'],
                'extra_id' => $http_decoded['uuid'],
                'type' => 'urlscan.io',
                'url' => $data['url'] ?? 'error',
                'result' => collect($http_decoded),
            ];
            $new = new CrawlerData($data);
            $new->created_at = now();
            $new->expired_bool = 0;
            $new->save();
            //$job = UrlscanCheck::dispatch($http_decoded['uuid']);

            return $new;
    } catch(\Exception $e) {
        $data = [
            'state' => 'ERROR',
            'status' => 400,
            'state_message' => $e->getMessage(),
        ];
        return $data;
    }
    }

    public function check_result(array $data) {
        $http = Http::withHeaders([
            'API-Key' => config('casino-dog.urlscan_apikey')
        ])->post('https://urlscan.io/api/v1/scan', [
            'url' => $data['url'],
            'visibility' => 'public'
        ]);

        $data = [
            'url' => $data['url'] ?? 'error',
            'status' => $http->status() ?? 400,
            'data' => json_decode($http->body() ?? NULL, true),
        ];
        return $data;
    }


}