<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly ChatMessage $message) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.response.' . $this->message->blood_request_response_id);
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'blood_request_response_id' => $this->message->blood_request_response_id,
            'sender_id' => $this->message->sender_id,
            'message' => $this->message->message,
            'is_read' => $this->message->is_read,
            'created_at' => $this->message->created_at?->toISOString(),
        ];
    }
}
