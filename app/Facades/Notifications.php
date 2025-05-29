<?php
// File: app/Facades/Notifications.php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool send(string $type, $data = null, $recipients = null, array $channels = null)
 * @method static array sendBulk(string $type, array $dataRecipientPairs)
 * @method static bool schedule(string $type, $data, $recipients, \Carbon\Carbon $sendAt, array $channels = null)
 * @method static array getStatistics()
 * @method static array test(\App\Models\User $user)
 * @method static void clearCache()
 * @method static array getAvailableTypes()
 * @method static bool hasType(string $type)
 * @method static void registerType(string $type, string $notificationClass)
 *
 * @see \App\Services\NotificationService
 */
class Notifications extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'notifications';
    }
}