<?php

namespace Wainwright\CasinoDog\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Process\Process;
use DB;
use Wainwright\CasinoDog\Models\OperatorAccess;
class AddOperatorAccessKey extends Command
{

    protected $signature = 'casino-dog:add-operator-key';

    public $description = 'Add operator key to gain access to your games through API. You can also do this through Nova panel if installed.';

    public function handle()
    {   
        $count_users = \App\Models\User::count();
        if($count_users === 0) {
                $this->components->error('You first need to create user before able to assign key to user. You can create your first user by invoking: artisan casino-dog:admin');
        }

        if($count_users === 1) {
            $user = \App\Models\User::first();
            $user_id = $user->id;
        }

        if($count_users > 1) {
            $user_ask = $this->ask('What user ID to assign new key? [1]', '1');
            $user = \App\Models\User::where('id', $user_ask)->first();
            if(!$user) {
                $this->components->error('User with given ID not found.');
            }
            $user_id = $user->id;      
        }

        $ip_ask = $this->ask('What ip-address should be given access to use this key (ipv4)? [127.0.0.1]', '127.0.0.1');
        $ip = $ip_ask;
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->components->error('IP '.$ip.' does not to be one of a valid ip syntax.');
        }

        $url_ask = $this->ask('Where to send game events & ping-pong? [https://casino.local/api/casino-dog-operator-api/callback]', 'https://casino.local/api/casino-dog-operator-api/callback');
        $url = $url_ask;
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->components->error('Url '.$url.' does not seem to be a valid URL syntax, add manually to database or try through the administration panel.');
        }

        $data = [
            'operator_key' => md5(rand(20, 200).now()),
            'operator_secret' => substr(md5(now().rand(10, 100)), 0, rand(9, 12)),
            'callback_url' => $url,
            'ownedBy' => $user_id,
            'active' => 1,
            'operator_access' => $ip,
        ];
        $operator_model = new OperatorAccess();
        $operator_model->insert($data);
        $this->line('');       
        $this->line('');       

        $this->info('Sucessfully added key. API key details:');       
        $this->line('');       

        foreach($data as $key => $value) {
            if($key !== 'active') {
                $this->components->info($value.' ['.$key.']');
            }
        }
        $this->line('');       

        $this->info('If you use this package in conjunction with `casino-dog-operator-api` package, you may enter above operator key & operator secret within the config/casino-dog-operator-api.php on your operator application.');
        $this->line('');       

        return self::SUCCESS;
    }


}
