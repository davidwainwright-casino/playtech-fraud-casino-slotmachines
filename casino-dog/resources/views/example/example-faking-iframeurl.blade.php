<!-- Using srcdoc, theres many more ways (see netent launcher blade component f.e.) !-->

<iframe id=""game-iframe" src="https://fake-url-goes-here" srcdoc=""></iframe>

<!-- srcdoc actually is priotized over the src attribute. Srcdoc takes any html content, so what we are doing below in javascript is to make a redirect to the real game content/data that can be whatever, this make the srcdoc remain empty like above while the url also remains. We simply redirect the iframe's inner content to a different page same as you click a link or are redirected. 

This bypasses also any x-frame/cross restrictions in browser, because the srcdoc started empty it defaults to the same domain you are loading the iframe on so that the browser thinks the iframe is local (as it really is technically)

!-->
<script id="loader" type="text/javascript">
    document.getElementById("game-iframe").contentWindow.document.write("\x3Cscript crossorigin='anonymous'>location.replace('{!! $game_data['session']['session_url'] !!}')\x3C/script>");

    //loader.remove();
</script>


<!-- ^ loader.remove() is to clean up all the .js code after execution, best is to load in this .js snippet dynamically, so that really only it can be used in your iframed page and by actually expiring the access. For example <script src="/illegal-code.js?token={ID}</script> and to tie this token to one-use and only to the specific game token/session. !-->