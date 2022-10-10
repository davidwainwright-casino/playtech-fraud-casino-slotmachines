<?php
namespace Wainwright\CasinoDog;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Http;

class ProxyHelper {
    //Original request
    private Request $originalRequest;
    //Params from multipart requests
    private $multipartParams;
    //Custom Headers
    private $headers;
    //Custom Authorization
    private $authorization;
    //Custom Method
    private $customMethod;

    private $addQuery;

    //If needed add cookies support with:
    //  - PendingRequest->withCookies
    //  - Request->hasCookie
    //  - or custom

    //Settings
    private $useDefaultAuth;
    //It's recommandable check manually (for multipart exceptions and other things)
    private $useDefaultHeaders;

    public function CreateProxy(Request $request, $useDefaultAuth = true, $useDefaultHeaders = false){
        $this->originalRequest = $request;
        $this->multipartParams = $this->GetMultipartParams();
        $this->useDefaultAuth = $useDefaultAuth;
        $this->useDefaultHeaders = $useDefaultHeaders;
        return $this;
    }

    public function withHeaders($headers){ $this->headers = $headers; return $this; }

    public function withBasicAuth($user, $secret){ $this->authorization = ['type' => 'basic', 'user' => $user, 'secret' => $secret ]; return $this; }

    public function withDigestAuth($user, $secret){ $this->authorization = ['type' => 'digest', 'user' => $user, 'secret' => $secret ]; return $this; }

    public function withToken($token){ $this->authorization = ['type' => 'token', 'token' => $token ]; return $this; }

    public function withMethod($method = 'POST'){ $this->customMethod = $method; return $this; }

    public function preserveQuery($preserve){ $this->addQuery = $preserve; return $this; }

    public function getResponse($url){

        $info = $this->getRequestInfo();

        $http = $this->createHttp($info['type']);
        $http = $this->setAuth($http, $info['token']);
        $http = $this->setHeaders($http);

        if($this->addQuery && $info['query'])
            $url = $url.'?'.http_build_query($info['query']);

        $response = $this->call($http, $info['method'], $url, $this->getParams($info));

        return response($this->isJson($response) ? $response->json() : $response->body(), $response->status());
    }

    public function toUrl($url){ return $this->getResponse($url); }

    public function toHost($host, $proxyController){
        return $this->getResponse($host.str_replace($proxyController, '', $this->originalRequest->path()));
    }

    private function getParams($info){
        $defaultParams = [];
        if($info['method'] == 'GET')
            return $info['params'];
        if($info['type'] == 'multipart')
            $defaultParams = $this->multipartParams;
        else
            $defaultParams = $info['params'];
        if($info['query'])
            foreach ($info['query'] as $key => $value)
                unset($defaultParams[array_search(['name' => $key,'contents' => $value], $defaultParams)]);
        return $defaultParams;
    }
    private function setAuth(PendingRequest $request, $currentAuth = null){
        if(!$this->authorization)
            return $request;
        switch ($this->authorization['type']) {
            case 'basic':
                return $request->withBasicAuth($this->authorization['user'],$this->authorization['secret']);
            case 'digest':
                return $request->withDigestAuth($this->authorization['user'],$this->authorization['secret']);
            case 'token':
                return $request->withToken($this->authorization['token']);
            default:
                if($currentAuth && $this->useDefaultAuth)
                    return $request->withToken($currentAuth);
                return $request;
        }
    }

    private function setHeaders(PendingRequest $request){
        if(!$this->headers)
            return $request;
        return $request->withHeaders($this->headers);
    }

    private function createHttp($type){
        switch ($type) {
            case 'multipart':
                return Http::asMultipart();
            case 'form':
                return Http::asForm();
            case 'json':
                return Http::asJson();
            case null:
                return new PendingRequest();
            default:
            return Http::asJson();
        }
    }

    private function call(PendingRequest $request, $method, $url, $params){
        if($this->customMethod)
            $method = $this->customMethod;
        switch ($method) {
            case 'GET':
                return $request->get($url, $params);
            case 'HEAD':
                return $request->head($url, $params);
            default:
            case 'POST':
                return $request->post($url, $params);
            case 'PATCH':
                return $request->patch($url, $params);
            case 'PUT':
                return $request->put($url, $params);
            case 'DELETE':
                return $request->delete($url, $params);
        }
    }

    private function getRequestInfo(){
        return [
            'type' => ($this->originalRequest->isJson() ? 'json' :
                    (strpos($this->originalRequest->header('Content-Type'),'multipart') !== false ? 'multipart' :
                    ($this->originalRequest->header('Content-Type') == 'application/x-www-form-urlencoded' ? 'form' : $this->originalRequest->header('Content-Type')))),
            'agent' => $this->originalRequest->userAgent(),
            'method' => $this->originalRequest->method(),
            'token' => $this->originalRequest->bearerToken(),
            'full_url'=>$this->originalRequest->fullUrl(),
            'url'=>$this->originalRequest->url(),
            'format'=>$this->originalRequest->format(),
            'query' =>$this->originalRequest->query(),
            'params' => $this->originalRequest->all(),
        ];
    }

    private function GetMultipartParams(){
        $multipartParams = [];
        if ($this->originalRequest->isMethod('post')) {
            $formParams = $this->originalRequest->all();
            $fileUploads = [];
            foreach ($formParams as $key => $param)
                if ($param instanceof HttpUploadedFile) {
                  $fileUploads[$key] = $param;
                  unset($formParams[$key]);
                }
            if (count($fileUploads) > 0){
                $multipartParams = [];
                foreach ($formParams as $key => $value)
                    $multipartParams[] = [
                      'name' => $key,
                      'contents' => $value
                    ];
                foreach ($fileUploads as $key => $value)
                    $multipartParams[] = [
                      'name' => $key,
                      'contents' => fopen($value->getRealPath(), 'r'),
                      'filename' => $value->getClientOriginalName(),
                      'headers' => [
                        'Content-Type' => $value->getMimeType()
                      ]
                    ];
            }
        }
        return $multipartParams;
    }

    private function isJson(Response $response){
        return strpos($response->header('Content-Type'),'json') !== false;
    }
}
