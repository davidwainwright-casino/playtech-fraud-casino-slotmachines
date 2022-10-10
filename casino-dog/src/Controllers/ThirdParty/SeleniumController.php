<?php
namespace Wainwright\CasinoDog\Controllers\ThirdParty;

use Laravel\Dusk\Browser;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverBy;

class SeleniumController
{
    /**
     * Selenium open page & retrieve content
     *
     * @return void
     */
    public function test_selenium(string $url, int $timeout)
    {
        try {
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';
        $options = (new ChromeOptions)->addArguments(collect([
            '--start-maximized',
            '--enable-javascript',
        ])->all());
        $browser = RemoteWebDriver::create(
            $_ENV['SELENIUM_URL'] ?? 'http://10.42.0.77:4444',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );


        $browser->manage()->timeouts()->implicitlyWait(15);
        $browser->manage()->timeouts()->pageLoadTimeout(15);

        $get = $browser->get($url)->getPageSource();
        $response = array(
            'status' => 'success',
            'content' => $get
        );
        #$browser->close();
        $browser->quit();

        } catch(\Exception $e) {
            $response = array(
                'status' => 'error',
                'content' => $e->getMessage()
            );
        }

        return $response;
    }
}
