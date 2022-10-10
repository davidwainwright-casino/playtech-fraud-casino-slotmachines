<?php
namespace Wainwright\CasinoDog\Controllers\Game;
use Illuminate\Contracts\Support\Arrayable;
use Wainwright\CasinoDog\Controllers\Game\SessionsHandler;
use Illuminate\Http\JsonResponse;
use Wainwright\CasinoDog\Controllers\Game\OperatorsController;
use Wainwright\CasinoDog\Controllers\DataController;
use Illuminate\Support\Str;

class GameKernel
{
    public function normalized_array($data, int $status_code = null, string $message = null): array {
        $data ??= [];
        $status_code ??= 200;
        $return_data = [
            'status' => (int) $status_code,
            'message' => $message ?? "n/a",
            'data' => $data,
        ];
        return $this->to_array($return_data);
    }

    public function random_uuid()
    {
        return Str::Uuid();
    }

    public function getIp($request) {
        $kernel_casinodog = new \Wainwright\CasinoDog\CasinoDog;
        $get_ip = $kernel_casinodog->getIp($request);
        return $get_ip;
    }

    public function normalized_json($data, int $status_code = null, string $message = null): JsonResponse
    {
        $data ??= [];
        $status_code ??= 200;
        $array = $this->normalized_array($data, $status_code, $message);

        return response()->json($array, $status_code);
    }

    public function update_session($token_internal, $key, $value) {
        $session = $this->get_internal_session($token_internal);
        if($session['status'] === 200) {
            $session = SessionsHandler::sessionUpdate($token_internal, $key, $value); //update session table
            return $this->normalized_array($this->to_array($session), 200, 'success');
        } else {
            return $this->normalized_array($this->to_array($session ?? NULL), 404, 'Session not found');
        }
    }

    public function in_between($a, $b, $data)
    {
        preg_match('/'.$a.'(.*?)'.$b.'/s', $data, $match);
        if(!isset($match[1])) {
            return false;
        }
        return $match[1];
    }


    public function get_internal_session(string $token) {
        $select_session = SessionsHandler::sessionData($token);
        if(isset($select_session['data'])) {
            return $this->normalized_array($this->to_array($select_session['data']), 200, "n/a");
        } else {
            return $this->normalized_array($this->to_array($select_session), 400, 'Session not found');
        }
    }
    public function fail_internal_session(string $token) { // session fail, expire session
        $session = $this->get_internal_session($token);
        if($session['status'] === 200) {
            $session_fail = SessionsHandler::sessionFailed($token);
            return $this->normalized_array($this->to_array($session['data']), 200, json_encode($session_fail));
        } else {
            return $this->normalized_array($this->to_array($session ?? NULL), 404, 'Session not found');
        }
    }

    public function get_balance($internal_token, $type = NULL):int
    {
        $type = 'internal';
        $data = [
            'game_data' => 'balance_call',
        ];
        $balance = OperatorsController::operatorCallbacks($internal_token, 'balance', $data);
        return (int) $balance;
    }

    public function process_game($internal_token, $betAmount, $winAmount, $game_data, $type = NULL):int
    {
        $type = 'internal';
        $data = [
            'bet' => $betAmount,
            'win' => $winAmount,
            'game_data' => $game_data,
        ];
        $balance = OperatorsController::operatorCallbacks($internal_token, 'game', $data);
        return (int) $balance;
    }

    public function expire_internal_session(string $token) {
        $session = $this->get_internal_session($token);
        if($session['status'] === 200) {
            $session_expired = SessionsHandler::sessionExpired($token);
            return $this->normalized_array($this->to_array($session['data']), 200, json_encode($session_expired));
        } else {
            return $this->normalized_array($this->to_array($session ?? NULL), 404, 'Session not found');
        }
    }

    public function build_response_query($query)
    {
        $resp = http_build_query($query);
        $resp = urldecode($resp);
        return $resp;
    }


    public function build_query($query)
    {
        $resp = http_build_query($query);
        $resp = urldecode($resp);
        return $resp;
    }


    public function parse_query($query_string)
    {
        parse_str($query_string, $q_arr);
        return $q_arr;
    }

    public function find_previous_active_session(string $token) {
        $session = $this->get_internal_session($token);
        $select_session = SessionsHandler::sessionFindPreviousActive($session['data']['player_id'], $session['data']['token_internal'], $session['data']['game_id_original']);
        if(isset($select_session['data'])) {
            return $this->normalized_array($this->to_array($select_session['data']), 200, "n/a");
        } else {
            return $this->normalized_array($this->to_array($select_session), 404, 'Session not found');
        }
    }

    public function to_array($data)
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }
        return $data;
    }

    public function get_gameslist()
    {
        return DataController::getGames();
    }

    public function proxy_json_softswiss(string $url)
    {
        $proxy = new \Wainwright\CasinoDog\Controllers\ProxyController();
        return $proxy->launch_job('json_softswiss', $url);
    }

    public function proxy_game_session_static(string $url)
    {
        $host = $this->get_host($url);
        $allowedhosts = $host.',www.'.$host;
        $proxy = new \Wainwright\CasinoDog\Controllers\ProxyController();
        return $proxy->launch_job('game_session_static', $url, $allowedhosts);
    }

    // Helper to get host from url
    public function get_host($url)
    {
        $url = urldecode($url);
        $parse = parse_url($url);
        $host = preg_replace('/^www\./', '', $parse['host']);
        return $host;
    }
}
