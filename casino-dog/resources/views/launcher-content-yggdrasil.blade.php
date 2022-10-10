@if(!isset($_GET['appsrv']))
    @php
        $url = "g?".$_SERVER['QUERY_STRING'].'&'.$game_content['orig_query'];
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
        die();
    @endphp
@else 
<?php echo $game_content['link'] ?>

@endif