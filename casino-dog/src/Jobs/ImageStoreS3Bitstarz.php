<?php
namespace Wainwright\CasinoDog\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Wainwright\CasinoDog\Controllers\DataController;
use Wainwright\CasinoDog\Middleware\JobRatelimitMiddleware;
use Wainwright\CasinoDog\Models\Gameslist;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class ImageStoreS3Bitstarz implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $file_url;
    private string $disk;
    private string $rand_prefix;
    private string $gid;

    public function __construct(string $file_url, string $disk, string $rand_prefix, string $gid)
    {
        $this->file_url = $file_url;
        $this->disk = $disk;
        $this->gid = $gid;
        $this->rand_prefix = $rand_prefix;

    }
    /*
    public function middleware()
    {
        return [new JobRatelimitMiddleware];
    }*/

    public function handle()
    {
        $gid = $this->gid;
        $url = $this->file_url;
        $output_filename = $this->rand_prefix.$gid.'.png';

        $url = "https://d1sc13y7hrlskd.cloudfront.net/optimized_images/landscape/".$this->gid.".png";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
           "authority: d1sc13y7hrlskd.cloudfront.net",
           "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
           "accept-language: en-ZA,en;q=0.9",
           "cache-control: no-cache",
           "pragma: no-cache",
           "referer: https://www.bitstarz.com/",
           "sec-ch-ua-mobile: ?0",
           "sec-ch-ua-platform: Linux",
           "sec-fetch-dest: document",
           "sec-fetch-mode: navigate",
           "sec-fetch-site: cross-site",
           "sec-fetch-user: ?1",
           "upgrade-insecure-requests: 1",
           "user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Safari/537.36",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        curl_close($curl);
        $save = Storage::disk($this->disk)->put('bitstarz/'.$output_filename, $response);
        if($save) {
            $temporary_url = Storage::disk($this->disk)->temporaryUrl('bitstarz/'.$output_filename, \Carbon\Carbon::now()->addDays(6));
            $demo_url = strtok($temporary_url, '?');
            $demo_url = str_replace('http:', 'https:', $demo_url);
            $select_game = Gameslist::where('gid', $gid)->first();
            $select_game->update([
                'image' => $demo_url
            ]);
            return $select_game;
        } else {
            abort(400, 'save failed');
        }
    }
}