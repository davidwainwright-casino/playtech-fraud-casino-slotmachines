<?php
namespace Wainwright\CasinoDog\Controllers\ThirdParty;

use Ably;

class AblyController
{
    public function send_message(string $channel, string $type, $message) 
    {
        try {
            $token = \Ably::auth()->requestToken(['clientId' => $channel]); // Ably\Models\TokenDetails
            \Ably::channel($channel)->publish($type, $message);
        } catch(\Exception $e) {
            $casino_dog = new \Wainwright\CasinoDog\CasinoDog();
            $casino_dog->save_log('OperatorsController()', 'Ably Error: '.$e);
        }
    }
}