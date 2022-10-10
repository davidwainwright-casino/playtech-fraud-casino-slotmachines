<div id="capture" style="display: none;">
    {!! $game_content !!}
</div>


<script src="http://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script type="text/javascript">
set
 var options = {
        removeContainer: true,
        proxy: 'http://localhost:3000',
    };
    html2canvas(document.querySelector("#capture"), options).then(canvas => {
    document.body.appendChild(canvas)
});
</script>


