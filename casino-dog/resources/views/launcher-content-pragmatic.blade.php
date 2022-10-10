@if(!isset($_GET['symbol']))
    @php
        $url = "g?".$_SERVER['QUERY_STRING'].'&symbol='.$game_content['content']['query']['symbol'].'&gname='.$game_content['content']['query']['gname'].'&mgckey='.$game_content['content']['query']['mgckey'];
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
        die();
    @endphp
@else 

{!! $game_content['content']['modified_content'] !!}
<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
@endif