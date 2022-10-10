
@if(!isset($_GET['lang']))
    @php
        $url = "?".$_SERVER['QUERY_STRING'].'&'.$game_content['query'];
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
        die();
    @endphp
@else 
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>

<base href="https://casino.nolimitcdn.com/">

{!! $game_content['html'] !!}

<script defer="">
    window.addEventListener('message', function(e) {
        console.log(e); // busy, idle, ready
});
window.addEventListener('deposit', function(e) {
        console.log(e); // busy, idle, ready
});

window.addEventListener('events', function(e) {
        console.log(e); // busy, idle, ready
});

</script>
@endif
