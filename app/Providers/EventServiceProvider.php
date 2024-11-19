<?php

namespace App\Listeners;

use App\Events\ReviewUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReviewUpdatedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\ReviewUpdated  $event
     * @return void
     */
    public function handle(ReviewUpdated $event)
    {
        // Handle the event, e.g., send a notification
        \Log::info('Handling ReviewUpdated event', ['review_id' => $event->review->id]);
    }
}
