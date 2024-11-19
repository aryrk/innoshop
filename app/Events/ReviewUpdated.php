<?php

namespace App\Events;

use InnoShop\Common\Models\Review;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Log;

class ReviewUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
        Log::info("ReviewUpdated event fired for review ID: {$review->id}");
    }

    public function broadcastOn()
    {
        Log::info("Broadcasting on reviews-channel");
        return new Channel('reviews-channel');
    }

    public function broadcastAs()
    {
        return 'review.updated';
    }

    public function broadcastWith()
    {
        \Log::info('Broadcasting ReviewUpdated Event', [
            'review_id' => $this->review->id,
            'data' => [
                'id' => $this->review->id,
                'customer_name' => $this->review->customer->name ?? 'Anonymous',
                'content' => $this->review->content,
                'rating' => $this->review->rating,
                'created_at' => $this->review->created_at->format('Y-m-d H:i:s'),
            ],
        ]);

        \Log::info('ReviewUpdated event data sent', [
            'id' => $this->review->id,
            'content' => $this->review->content,
        ]);

        return [
            'id' => $this->review->id,
            'customer_name' => $this->review->customer->name ?? 'Anonymous',
            'content' => $this->review->content,
            'rating' => $this->review->rating,
            'created_at' => $this->review->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
