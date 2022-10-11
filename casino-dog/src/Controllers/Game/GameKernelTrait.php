<?php
namespace Wainwright\CasinoDog\Controllers\Game;
use Wainwright\CasinoDog\Controllers\Game\GameKernel;
use Illuminate\Http\JsonResponse;
use Wainwright\CasinoDog\Controllers\Game\AssetController;
use Wainwright\CasinoDog\Models\GameRespinTemplate;
trait GameKernelTrait
{
    public function load_game_kernel()
    {
        return new GameKernel();
    }

    public function get_internal_session(string $token) {
        $kernel = new GameKernel();
        return $kernel->get_internal_session($token);
    }

    public function find_previous_active_session(string $token) {
        $kernel = new GameKernel();
        return $kernel->find_previous_active_session($token);
    }

    public function update_session($token, $key, $value) {
        $kernel = new GameKernel();
        return $kernel->update_session($token, $key, $value);
    }

    public function random_uuid()
    {
        $kernel = new GameKernel();
        return $kernel->random_uuid();
    }


    public function request_ip($request)
    {
        $kernel = new GameKernel();
        return $kernel->getIp($request);
    }

    public function expire_internal_session(string $token)
    {
        $kernel = new GameKernel();
        return $kernel->expire_internal_session($token);
    }

    public function fail_internal_session(string $token)
    {
        $kernel = new GameKernel();
        return $kernel->fail_internal_session($token);
    }


    public function pretendResponseIsFile(string $path, string $contentType)
    {
        $kernel = new AssetController();
        return $kernel->pretendResponseIsFile($path, $contentType);
    }

    public function process_game($internal_token, $betAmount, $winAmount, $game_data, $type = NULL):int
    {
        $kernel = new GameKernel();
        return $kernel->process_game($internal_token, $betAmount, $winAmount, $game_data, $type);
    }

    public function get_balance($internal_token, $type = NULL):int
    {
        $kernel = new GameKernel();
        return $kernel->get_balance($internal_token);
    }

    public function get_gameslist()
    {
        $kernel = new GameKernel();
        return $kernel->get_gameslist();
    }

    public function proxy_game_session_static(string $url) {
        return $this->load_game_kernel()->proxy_game_session_static($url);
    }

    public function proxy_json_softswiss(string $url) {
        return $this->load_game_kernel()->proxy_json_softswiss($url);
    }

    public function normalized_array($data, int $status_code = null, string $message = null): array {
        $data ??= [];
        $status_code ??= 200;
        return $this->load_game_kernel()->normalized_array($data);
    }

    public function build_response_query($string) {
        $kernel = new GameKernel();
        return $kernel->build_response_query($string);
    }

    public function parse_query($string) {
        $kernel = new GameKernel();
        return $kernel->parse_query($string);
    }

    public function build_query($string) {
        $kernel = new GameKernel();
        return $kernel->build_query($string);
    }

    public function morph_to_array($data) {
        $kernel = new GameKernel();
        return $kernel->to_array($data);
    }

    public function in_between($a, $b, $data) {
        $kernel = new GameKernel();
        return $kernel->in_between($a, $b, $data);
    }

    public function save_game_respins_template($gid, $game_data, $game_type) {
        $kernel = new GameRespinTemplate();
        return $kernel->save_game_template($gid, $game_data, $game_type);
    }

    public function retrieve_game_respins_template($gid, $game_type) {
        $kernel = new GameRespinTemplate();
        return $kernel->retrieve_game_template($gid, $game_type);
    }

    public function normalized_json($data, int $status_code = null, string $message = null): JsonResponse {
        $data ??= [];
        $status_code ??= 200;
        return $this->load_game_kernel()->normalized_json($data, $status_code, $message);
    }

    public function error_exception_message(\Exception $exception) {
        return $exception->getMessage();
    }
    

}
