<?php

namespace App\Listeners;

use App\Events\QuotationSubmitted;
// Replaced by centralized notification system
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
        
        Notification::send($admins, Notifications::send('quotation.created', $event->quotation));
    }
}