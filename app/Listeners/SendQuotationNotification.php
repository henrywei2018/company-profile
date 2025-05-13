<?php

namespace App\Listeners;

use App\Events\QuotationSubmitted;
use App\Notifications\NewQuotationNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class SendQuotationNotification
{
    /**
     * Handle the event.
     *
     * @param QuotationSubmitted $event
     * @return void
     */
    public function handle(QuotationSubmitted $event)
    {
        $admins = User::role('admin')->get();
        
        Notification::send($admins, new NewQuotationNotification($event->quotation));
    }
}