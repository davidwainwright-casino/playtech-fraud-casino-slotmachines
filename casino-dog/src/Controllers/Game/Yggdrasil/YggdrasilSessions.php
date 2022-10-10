<?php
namespace Wainwright\CasinoDog\Controllers\Game\Yggdrasil;
use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Wainwright\CasinoDog\Models\Gameslist;

class YggdrasilSessions extends YggdrasilMain
{
    use GameKernelTrait;

    public function extra_game_metadata($gid)
    {
        return false;
    }

    public function fresh_game_session($game_id, $method, $token_internal = NULL)
    {
        if($method === 'demo_method') {
            $demo_link = $this->get_game_demolink($game_id);
            $parse_query = $this->parse_query($demo_link);
            $new_api_endpoint = config('casino-dog.games.yggdrasil.new_api_endpoint').$token_internal.'/'.$parse_query['gameid'];  // building up the api endpoint we want to receive game events upon

            $build_url = 'https://staticdemo.yggdrasilgaming.com/init/launchClient.php?appsrv='.$new_api_endpoint.'&gameid='.$parse_query['gameid'].'&license=default&lang=en&org=SourceGroup&channel=pc&cheatPanel=yes&depositURL=https://launcher.yggdrasilgaming.com&filePrefix=production_&home=https://launcher.yggdrasilgaming.com&localiseAsset=yes&share=no';

            $html = Http::get($build_url);

            $data = [
                'html' => $html,
                'link' => $build_url,
                'orig_query' => 'appsrv='.$new_api_endpoint.'&gameid='.$parse_query['gameid'].'&license=default&lang=en&org=SourceGroup&channel=pc&cheatPanel=yes&depositURL=https://launcher.yggdrasilgaming.com&filePrefix=production_&home=https://launcher.yggdrasilgaming.com&localiseAsset=yes&share=no',
            ];
            return $data;
        }
        // Add in additional grey methods here, specify the method on the internal session creation when a session is requested, don't split this here
        return 'generateSessionToken() method not supported';
    }

    public function get_game_demolink($gid) {
        $select = Gameslist::where('gid', $gid)->first();
        return $select->demolink;
    }


    public function get_game_identifier($gid) {
        $select = Gameslist::where('gid', $gid)->first();
        return $select->gid_extra;
    }

    public function create_session(string $internal_token)
    {
        $select_session = $this->get_internal_session($internal_token);
        if($select_session['status'] !== 200) { //internal session not found
               return false;
        }

        $token_internal = $select_session['data']['token_internal'];
        $game_id = $select_session['data']['game_id_original'];


        $game = $this->fresh_game_session($game_id, 'demo_method', $token_internal);
        $html_content_modify = $this->modify_game($token_internal, $game['html']);

        $response = [
            'modified_html' => $html_content_modify,
            'original_html' => $game['html'],
            'link' => $game['link'],
            'orig_query' => $game['orig_query'],
        ];
        return $response;
    }


}
