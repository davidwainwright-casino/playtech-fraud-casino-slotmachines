@php
    if(!isset($_GET['GAMESERVERURL'])) {

        $url = "g?".$_SERVER['QUERY_STRING'].'&'.$game_content['query'];
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
    }
@endphp

@if(isset($_GET['load_variables']))
    <base href="https://softswiss-mga-c2ss.betsoftgaming.com">

    {{ $game_content['html'] }}
@else
    {!! $game_content['html'] !!}

    @endif