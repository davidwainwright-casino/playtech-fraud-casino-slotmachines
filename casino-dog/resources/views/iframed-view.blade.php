<html> 
<head>
<title>Iframed</title>
<script type="text/javascript" src="//cdn.ably.io/lib/ably.min-1.js"></script>
<script src="//jsbin-files.ably.io/js/jquery-1.8.3.min.js"></script>

<style id="jsbin-css">
body {
  font: 14px 'Helvetica Neue', Helvetica, Arial, sans-serif;
}

.row {
    word-wrap: break-word;
    font: 10px 'Helvetica Neue', Helvetica, Arial, sans-serif;
}

</style>
</head>
<body>

<div class="row">
  <input id="publish" type="submit" value="Publish a message">
  <input id="window" type="submit" value="Window">

</div>

<p>Listener</p>
<p><small><b>Internal Session ID:</b> {{ $game_data['session']['data']['token_internal'] }}</small></p>
<p><small><b>Real URL:</b> {{ $game_data['session']['session_url'] }}:</small></p>
<p><small><b>Fake iFrame URL (if applicable):</b> {{ $game_data['session']['fake_iframe_url'] }}</small></p>

<iframe
    id="game-iframe"
    allowtransparency=""
    srcdoc=""
    scrolling="no"
    class="embed-responsive-item absolute top-0 right-0 bottom-0 left-0 w-full h-full"
    src="{{ $game_data['session']['fake_iframe_url'] }}"
    frameborder="0"
    allowfullscreen=""
    >
</iframe>
<ul class="row" id="channel-status"></ul>

<script>
    console.log(@json($game_data))
    const ably = new Ably.Realtime('{{ $game_data['ably']['key'] }}');
    const channel = ably.channels.get('{{ $game_data['ably']['channel'] }}');
    $('input#publish').on('click', function() {
    channel.publish('greeting', 'Test Message');
    });

    $('input#window').on('click', function() {
        var gameiframe = document.getElementById("game-iframe");
      frames[0].myFunction();
    });

    channel.subscribe(function(message) {
    show('⬅ ' + message.name + ': ' + message.data, 'green');
    });

    function show(status, color) {
    console.log(status)
        $('#channel-status').prepend($('<li style="margin-bottom: 10px;">').text(status).css('color', color));
    }
</script>

<script id="loader" type="text/javascript">
    document.getElementById("game-iframe").contentWindow.document.write("\x3Cscript crossorigin='anonymous'>location.replace('{!! $game_data['session']['session_url'] !!}')\x3C/script>");
    //loader.remove();
</script>
</body>
</html>
