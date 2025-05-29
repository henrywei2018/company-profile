<?php

namespace App\Observers;

use App\Models\Certification;
use App\Services\NotificationService;

class CertificationObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Certification "created" event.
     */
    public function created(Certification $certification): void
    {
        // Notify admins about new certification
        $this->notificationService->send('system.certification_added', $certification);
    }

    /**
     * Handle the Certification "updated" event.
     */
    public function updated(Certification $certification): void
    {
        // Check if certification was activated/deactivated
        if ($certification->isDirty('is_active')) {
            $type = $certification->is_active ? 'system.certification_activated' : 'system.certification_deactivated';
            $this->notificationService->send($type, $certification);
        }

        // Check if expiry date changed
        if ($certification->isDirty('expiry_date')) {
            $this->checkExpiryDate($certification);
        }
    }

    /**
     * Check certification expiry date
     */
    protected function checkExpiryDate(Certification $certification): void
    {
        if (!$certification->expiry_date || !$certification->is_active) {
            return;
        }

        $daysUntilExpiry = now()->diffInDays($certification->expiry_date, false);
        
        // Alert if expiring within 30 days
        if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
            $this->notificationService->send('system.certificate_expiring', $certification);
        }
        
        // Alert if already expired
        if ($daysUntilExpiry < 0) {
            $this->notificationService->send('system.certificate_expired', $certification);
        }
    }
}