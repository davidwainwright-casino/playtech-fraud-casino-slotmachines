<?php
namespace Wainwright\CasinoDog\Controllers\Game\iSoftbet\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Controllers\Game\GameKernelTrait;
use Wainwright\CasinoDog\Models\Gameslist;

class GetRawIdentifierFromDemo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, GameKernelTrait;

    private string $url;
    private string $gid;

    public function __construct(string $gid, string $url)
    {
        $this->url = $url;
        $this->gid = $gid;
    }

    public function handle()
    {
        $gid = $this->gid;
        $gameslist = Gameslist::where('gid', $gid)->first();
        $http = Http::get($gameslist->demolink);

        $origin_identifier = $this->in_between('identifier: "', '"', $http);
        if($origin_identifier) {
            $gameslist->update([
                'gid_extra' => $origin_identifier
            ]);
        }
    }
}
