<?php
namespace Wainwright\CasinoDog\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Wainwright\CasinoDog\Controllers\UrlscanController;
class UrlscanInit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $url;

    public function __construct($url)
    {
        $this->url = $url;
    }
    public $tries = 2;

    public function handle()
    {
        $data = $this->url;
        $data_controller = new UrlscanController();
        $data = [
            'url' => $data,
        ];
        $init = new \Wainwright\CasinoDog\Controllers\UrlscanController;
        $data = $data_controller->init_request($data);
    }
}
