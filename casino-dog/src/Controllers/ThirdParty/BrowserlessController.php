<?php
    namespace Wainwright\CasinoDog\Controllers\ThirdParty;
    use Facebook\WebDriver\Remote\DesiredCapabilities;
    use Facebook\WebDriver\Chrome\ChromeOptions;
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Illuminate\Support\Facades\Http;

    class BrowserlessController
    {
        /**
         * A basic browser test example.
         *
         * @return void
         */
        public function test_basic_example()
        {
            $url = "https://chrome.browserless.io/content?token=291c1381-d8f2-4552-94bc-c18e1fc7e162";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = <<<DATA
            {
            "url": "https://demogamesfree.pragmaticplay.net/gs2c/openGame.do?gameSymbol=vs40hotburnx&websiteUrl=https%3A%2F%2Fdemogamesfree.pragmaticplay.net&jurisdiction=99&lobby_url=https%3A%2F%2Fwww.pragmaticplay.com%2Fen%2F"
            }
            DATA;

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $resp = curl_exec($curl);
            curl_close($curl);
            return array(
                'html_content' => $resp,
            );
        }
    }
