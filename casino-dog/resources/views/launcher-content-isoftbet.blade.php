@php

setcookie("isb_session", "", time() - 36000);
setcookie("cheat_tool_history", "", time() - 36000);
setcookie("MENU_SOUND_CATEGORY_SOUND_SWITCH", "", time() - 36000);

    if(!isset($_GET['password'])) {
            $url = "g?".$_SERVER['QUERY_STRING'].'&'.$game_content['query'];
            header("HTTP/1.1 200");
            header("Location: $url");
        }

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $url = "https://";
else
        $url = "http://";
// Append the host(domain name, ip) to the URL.
$url.= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL
$url.= $_SERVER['REQUEST_URI'].'&iframe_link=true';

@endphp

@if(isset($_GET['iframe_link']))
        {!! $game_content['content'] !!}
@else
        <iframe
        id="game-iframe"
        allowtransparency="true"
        srcdoc=""
        scrolling="no"
        class="embed-responsive-item absolute top-0 right-0 bottom-0 left-0 w-full h-full"
        src="{{ $game_content['iframe_link'] }}"
        frameborder="0"
        allowfullscreen=""
      ></iframe>

@endif
<!--




<div class="container">

        </div>

      <style>
.container {
    width:100%;
   height:70vh;
}

#game-iframe {
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
}

</style>

       !-->

        <!--

<script id="loader" type="text/javascript">
 document.getElementById("game-iframe").contentWindow.document.write("\x3Cscript crossorigin='anonymous'>location.replace('{!! $url !!}')\x3C/script>");
    loader.remove();
</script>
        !-->
<style>
    canvas {
        border: 0;
        margin: 0px;
        width: 100%;
        height: 100%;
        max-width: 100%;
        max-height: 100%;
    }
</style>
<!--
        <script>

console.log( window.location.href );  // whatever your current location href is
window.history.replaceState( {} , 'https://www.google.com', 'https://www.google.com' );
console.log( window.location.href );
        </script>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        window._apiURL = "https://demolobby.isoftbet.com/";
    })
</script>

        </div>
    </div>
</body>
</html>

endif!-->

