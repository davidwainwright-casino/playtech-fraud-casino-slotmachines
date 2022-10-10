@if(!isset($_GET['channel']))
    @php
		$exploded = explode('?', $game_content['link']);
        $url = "IframedView?".$_SERVER['QUERY_STRING'].'?'.$exploded[1];
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
        die();
    @endphp
@else 

<base href="https://asccw.playngonetwork.com/Casino">
{!! $game_content['html'] !!}

@endif