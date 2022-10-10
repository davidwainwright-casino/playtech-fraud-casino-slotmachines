<div class="embed-responsive embed-responsive-16by9 flex rounded bg-opacity-50 items-center justify-center relative w-full overflow-hidden" style="padding-top: 56.25%">
      <iframe
        id="game-iframe"
        allowtransparency="true"
        srcdoc=""
        scrolling="no"
        class="embed-responsive-item absolute top-0 right-0 bottom-0 left-0 w-full h-full"
        src="{{ $game['iframe_url'] }}"
        frameborder="0"
        allowfullscreen=""
      ></iframe>
</div>
<div class="embed-responsive embed-responsive-16by9 flex rounded-md shadow dark:border-gray-600 dark:bg-gray-800 relative bg-opacity-50 items-center justify-center relative w-full overflow-hidden">
    <button type="button" class="text-gray-900 bg-white hover:bg-gray-100 w-full focus:ring-4 focus:outline-none font-medium rounded-md text-sm px-3 py-2 text-center inline-flex items-center dark:bg-gray-800 dark:text-white mr-2">
            <div class="flex">
                <div class="px-3 py-2 text-sky-300 absolute right-0 cursor-pointer">{{ $player['mock_player_id'] }}</div>
                <div class="px-3 py-2 text-sky-300 absolute right-0 relative mr-10 cursor-pointer">Start Balance: ${{ $player['mock_balance'] }}</div>
            </div>
        <label for="currency" class="hidden block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400 mr-2 ml-2"></label>
        <select id="currency" class="hidden bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block max-w-md px-5 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option {{ $player['mock_currency'] === "EUR" ? 'selected' : ' '; }} value="EUR">EUR</option>
        <option {{ $player['mock_currency'] === "GBP" ? 'selected' : ' '; }} value="GBP">GBP</option>
        <option {{ $player['mock_currency'] === "USD" ? 'selected' : ' '; }} value="USD">USD</option>
        <option {{ $player['mock_currency'] === "CAD" ? 'selected' : ' '; }} value="CAD">CAD</option>
        </select>
    </button>
</div>

<script id="loader" type="text/javascript">
    document.getElementById("game-iframe").contentWindow.document.write("\x3Cscript crossorigin='anonymous'>location.replace('{!! $game['game_url'] !!}')\x3C/script>");
    loader.remove();
</script>
<div class="overflow-x-auto relative rounded">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="py-2 px-6">
                    Status
                </th>
                <th scope="col" class="py-2 px-6">
                    URL
                </th>
                <th scope="col" class="py-2 px-6">
                    Fake IFrame URL
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="py-2 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $game['state'] ?? 'null' }}
                </th>
                <td class="py-2 px-6">
                    <a target="_blank" href="{{ $game['game_url'] ?? 'null'}}">{{ $game['game_url'] ?? 'null'}}</a>
                </td>
                <td class="py-2 px-6">
                    <a target="_blank" href="{{ $game['iframe_url'] ?? 'null'}}">{{ $game['iframe_url'] ?? 'null'}}</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>


