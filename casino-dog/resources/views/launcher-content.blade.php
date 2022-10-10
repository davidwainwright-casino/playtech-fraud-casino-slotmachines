{!! $game_content !!}

<script>
function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
</script>

<script>
  window.localStorage.clear();
  setCookie('_games_session', <?php echo $cookie ?>, 5);
</script>