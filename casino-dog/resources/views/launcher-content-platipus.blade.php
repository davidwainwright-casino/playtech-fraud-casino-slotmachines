@php
    if(!isset($_GET['key'])) {

        $transformed_key = md5($game_content['session']['token_original'].'__'.$game_content['session']['token_internal']);
        $url = "g?".$_SERVER['QUERY_STRING'].'&demo=false&key='.$game_content['session']['token_original'].'&server='.env('APP_URL').'/api/c/platipus/'.$game_content['session']['token_internal'].'/';
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
    }
@endphp
{!! $game_content['content'] !!}
