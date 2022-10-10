<?php
namespace Wainwright\CasinoDog\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Wainwright\CasinoDog\Controllers\DataController;
class GameslistImporterProcessGame implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $game_options;
    private array $game_data;

    public function __construct(array $game_options, array $game_data)
    {
        $this->game_options = $game_options;
        $this->game_data = $game_data;
    }

    public function handle()
    {
        $game_options = $this->game_options;
        $data = $this->game_data;
        $data_controller = new DataController();
        if($game_options['schema'] === 'softswiss') {
            $data_controller->gameslist_process_softswiss($game_options, $data);
        } elseif($game_options['schema'] === 'parimatch') {
            $data_controller->gameslist_process_parimatch($game_options, $data);
        }
    }
}
