<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\ClientProjectUpdated;
use App\Notifications\ClientQuotationUpdated;
use App\Notifications\ClientMessageReceived;
use Illuminate\Support\Facades\Notification;

class ClientNotificationService
{
    /**
     * Send project update notification to client.
     */
    public function notifyProjectUpdate(User $client, $project, string $updateType): void
    {
        $client->notify(new ClientProjectUpdated($project, $updateType));
    }

    /**
     * Send quotation update notification to client.
     */
    public function notifyQuotationUpdate(User $client, $quotation, string $updateType): void
    {
        $client->notify(new ClientQuotationUpdated($quotation, $updateType));
    }

    /**
     * Send new message notification to client.
     */
    public function notifyNewMessage(User $client, $message): void
    {
        $client->notify(new ClientMessageReceived($message));
    }

    /**
     * Send bulk notifications to multiple clients.
     */
    public function sendBulkNotification(array $clientIds, $notification): void
    {
        $clients = User::whereIn('id', $clientIds)->get();
        Notification::send($clients, $notification);
    }

    /**
     * Send welcome notification to new client.
     */
    public function sendWelcomeNotification(User $client): void
    {
        $client->notify(new \App\Notifications\ClientWelcome());
    }
}