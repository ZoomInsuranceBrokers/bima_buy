<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateLead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $update_lead;
    public function __construct($update_lead)
    {
        $this->update_lead = $update_lead;
        print_r($this->update_lead);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {

        return [new PrivateChannel("updateLeadDetails.{$this->update_lead['receiver_id']}")];

    }

    public function broadcastWith()
    {
        // Returning the data that should be sent to the frontend
        return [
            'update_message' => $this->update_lead['message'], // example data
            'lead_id' => $this->update_lead['lead_id'],
        ];
    }
}
