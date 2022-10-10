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

class ImageStoreS3 implements ShouldQueue
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
        $prefix_dir = $this->rand_prefix;
        $output_filename = $prefix_dir.'/'.$gid.'.png';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // <-- don't forget this
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // <-- and this
        $result = curl_exec($ch);
        curl_close($ch);
        $save = Storage::disk($this->disk)->put($output_filename, $result);
        if($save) {
            $temporary_url = Storage::disk($this->disk)->temporaryUrl($output_filename, \Carbon\Carbon::now()->addDays(6));
	        $demo_url_build = env('MINIO_PUBLIC_BASE_URL').'/thumbnail/'.$output_filename;
            $select_game = Gameslist::where('gid', $gid)->first();
            $select_game->update([
                'image' => $demo_url_build
            ]);
            return $select_game;
        } else {
            abort(400, 'save failed');
        }
    }
}



