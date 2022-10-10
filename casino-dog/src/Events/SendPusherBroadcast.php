<?php

namespace Wainwright\CasinoDog\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendPusherBroadcast implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $channel;
  public $channel_event;
  public $message;


  public function __construct($channel, $channel_event, $message)
  {
    $this->channel = $channel;
    $this->channel_event = $channel_event;
    $this->message = $message;
  }

  public function broadcastOn()
  {
      return $this->channel;
  }

  public function broadcastAs()
  {
      return $this->channel_event;
  }
}

