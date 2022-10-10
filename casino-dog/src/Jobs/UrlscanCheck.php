<?php
namespace Wainwright\CasinoDog\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Wainwright\CasinoDog\Controllers\UrlscanController;
class UrlscanCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }
    public $tries = 15;
    public $maxExceptions = 10;


    public function handle()
    {
        $uuid = $this->uuid;
        $init = new \Wainwright\CasinoDog\Controllers\UrlscanController;
        $check = $init->urlscan_check_job($uuid);
        if($check->expired_bool === 1) {
            return $check;
        } else {
            $delay = rand(7, 12); //delay in seconds to put back to end of queue
            return $this->release($delay);
        }
    }
}
