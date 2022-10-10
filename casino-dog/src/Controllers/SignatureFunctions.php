<?php

namespace Wainwright\CasinoDog\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SignatureFunctions
{
    /**
     * Generating security signature - using hmac signing, you can change algo's however MD5 is fastest
     *
     * @param [string] $token
     * @param [string] $pwd
     * @return void
     */
    public function generate_sign(string $token, string $pwd = NULL)
    {
        $timestamp = time();
        if($pwd === NULL) {
            $pwd = config('casino-dog.securitysalt');
        }
        $encryption_key = $pwd.'-'.$timestamp; //Consider timestamp the randomizing salt, can be replaced by any randomizing key/regex
        $generate_sign = hash_hmac('md5', $token, $encryption_key);
        $concat_sign_time = $generate_sign.'-'.$timestamp;
        return $concat_sign_time;
    }

    /**
     * Verification on input signature
     *
     * @param [string] $signature
     * @param [string] $token
     * @param [string] $pwd
     * @return void
     */
    public function verify_sign(string $signature, string $token, string $pwd = NULL)
    {
        if($pwd === NULL) {
            $pwd = config('casino-dog.securitysalt');
        }
        try {
            $explode_signature = explode('-', $signature);
            $timestamp = $explode_signature[1];
            $encryption_key =  $pwd.'-'.$timestamp;
            $generate_sign = hash_hmac('md5', $token, $encryption_key);
            $concat_sign_time = $generate_sign.'-'.$timestamp;
            if($signature === $concat_sign_time) { // verify signature is same outcome
                return true;
            }
        } catch (\Exception $exception) {
            return false;
        }
        return false; //signature not matching, returning false
    }
}