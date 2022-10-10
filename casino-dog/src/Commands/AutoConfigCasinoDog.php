<?php

namespace Wainwright\CasinoDog\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
class AutoConfigCasinoDog extends Command
{
    protected $signature = 'casino-dog:auto-config';

    public $description = 'Auto config the casino dog variables (where applicable).';

    public function handle()
    {
        if ($this->confirm('Do you want to set your public IP?')) {
            $this->autoconfig_public_ip();
            if ($this->confirm('Do you want to override IP retrieved?')) {
                $ip = $this->ask('What is your public IP');
                $this->manual_public_ip($ip);
            }

        }
        if ($this->confirm('Do you want to set wainwright/casino-dog-proxy?')) {
            $name = $this->ask('What is URL of config proxy?');
        }
    }

    protected function manual_public_ip($ip)
    {
        try {
            $currentIP = config('casino-dog.server_ip');
            $this->components->info('> New IP given: '.$ip);
            $this->components->info('> Current IP in config: '.$currentIP);

            if($ip === $currentIP) {
                $this->components->info('Public IP unchanged as it is the same.');
            } else {
                $this->replaceInFile($currentIP, $ip, base_path('wainwright/config/casino-dog.php'));
                if (file_exists(config_path('casino-dog.php'))) {
                    $this->replaceInFile($currentIP, $ip, config_path('casino-dog.php'));
                }
            $this->line('');
            $this->components->info('Config \'server_ip\' in casino-dog.php set: '.$ip);
            }
        } catch(\Exception $e) {
            $this->line('');
            $this->components->error('Failed to set IP, manually change in casino-dog.php config. Error: '.$e);
        }
    }

    protected function autoconfig_public_ip()
    {
        $url_get_ip = 'https://api.ipify.org/?format=json';

        try {
        //Retrieve & set public IP
        $ip_get = json_decode((Http::timeout(10)->get($url_get_ip)), true);
        if(isset($ip_get['ip'])) {
            $ip = $ip_get['ip'];
            $currentIP = config('casino-dog.server_ip');
            $detect_ip_msg = 'New IP: '.$ip;
            $current_ip_msg = 'Current IP: '.$currentIP;
            $this->components->info($detect_ip_msg.' - '.$current_ip_msg);

            if($ip === $currentIP) {
                $this->components->info('Public IP unchanged.');
            } else {
                if (file_exists(config_path('casino-dog.php'))) {
                    $this->replaceInFile($currentIP, $ip, config_path('casino-dog.php'));
                }
            $this->line('');
            $this->components->info('Config \'server_ip\' in casino-dog.php set: '.$ip);
            }
        } else {
            $this->line('');
            $this->components->error('Response did not include \'ip\' at '.$url_get_ip);
        }
        } catch(\Exception $e) {
            $this->line('');
            $this->components->error('Set IP manually in casino-dog.php config as failed to reach IP API at: '.$e);
        }
    }
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

}
