<?php
namespace Wainwright\CasinoDog\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Wainwright\CasinoDog\Controllers\DataController;
class GameslistImporterBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $game;

    public function __construct(string $game)
    {
        $this->game = $game;
    }

    public function handle()
    {
        $data = $this->game;
        $data_controller = new DataController();
        $data_controller->gameslist_batch($data);
    }
}
