@if(!isset($_GET['gameName']))
    @php
        $url = "?".$_SERVER['QUERY_STRING'].'&'.$game_content['query'];
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
        die();
    @endphp
@else 
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
{!! $game_content['html'] !!}

<script defer="">

</script>
@endif
