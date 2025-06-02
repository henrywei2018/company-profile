<?php
// File: app/Observers/CertificationObserver.php

namespace App\Observers;

use App\Models\Certification;
use App\Traits\SendsNotifications;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class CertificationObserver
{
    use SendsNotifications;

    /**
     * Handle the Certification "created" event.
     */
    public function created(Certification $certification): void
    {
        try {
            Log::info('Certification created, sending notifications', [
                'certification_id' => $certification->id,
                'name' => $certification->name,
                'issuer' => $certification->issuer
            ]);

            // Notify admins about new certification
            $this->sendIfEnabled('system.certification_added', $certification);

            // Schedule expiry reminders if expiry date exists
            if ($certification->expiry_date) {
                $this->scheduleExpiryReminders($certification);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send certification created notifications', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Certification "updated" event.
     */
    public function updated(Certification $certification): void
    {
        try {
            // Check if certification was activated/deactivated
            if ($certification->isDirty('is_active')) {
                $this->handleActiveStatusChange($certification);
            }

            // Check if expiry date changed
            if ($certification->isDirty('expiry_date')) {
                $this->handleExpiryDateChange($certification);
            }

            // Check if certification details changed
            if ($this->hasImportantChanges($certification)) {
                $this->handleCertificationUpdate($certification);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send certification update notifications', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle active status changes
     */
    protected function handleActiveStatusChange(Certification $certification): void
    {
        if ($certification->is_active) {
            // Certification activated
            $this->sendIfEnabled('system.certification_activated', $certification);
            
            Log::info('Certification activated', [
                'certification_id' => $certification->id
            ]);
        } else {
            // Certification deactivated
            $this->sendIfEnabled('system.certification_deactivated', $certification);
            
            Log::info('Certification deactivated', [
                'certification_id' => $certification->id
            ]);
        }
    }

    /**
     * Handle expiry date changes
     */
    protected function handleExpiryDateChange(Certification $certification): void
    {
        $oldExpiryDate = $certification->getOriginal('expiry_date');
        $newExpiryDate = $certification->expiry_date;

        Log::info('Certification expiry date changed', [
            'certification_id' => $certification->id,
            'old_expiry' => $oldExpiryDate,
            'new_expiry' => $newExpiryDate
        ]);

        // Check current expiry status
        if ($newExpiryDate) {
            $this->checkExpiryStatus($certification);
            $this->scheduleExpiryReminders($certification);
        }

        // Notify admins about expiry date change
        $this->notifyAdmins('system.certification_expiry_updated', $certification);
    }

    /**
     * Handle certification details update
     */
    protected function handleCertificationUpdate(Certification $certification): void
    {
        // Notify admins about certification update
        $this->notifyAdmins('system.certification_updated', $certification);

        Log::info('Certification details updated', [
            'certification_id' => $certification->id
        ]);
    }

    /**
     * Check certification expiry status
     */
    protected function checkExpiryStatus(Certification $certification): void
    {
        if (!$certification->expiry_date || !$certification->is_active) {
            return;
        }

        $daysUntilExpiry = now()->diffInDays($certification->expiry_date, false);
        
        // Alert if expiring within 30 days
        if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
            $this->sendNotification('system.certificate_expiring', $certification);
            
            Log::warning('Certification expiring soon', [
                'certification_id' => $certification->id,
                'days_until_expiry' => $daysUntilExpiry
            ]);
        }
        
        // Alert if already expired
        if ($daysUntilExpiry < 0) {
            $this->sendNotification('system.certificate_expired', $certification);
            
            Log::error('Certification has expired', [
                'certification_id' => $certification->id,
                'days_overdue' => abs($daysUntilExpiry)
            ]);
        }
    }

    /**
     * Schedule expiry reminder notifications
     */
    protected function scheduleExpiryReminders(Certification $certification): void
    {
        if (!$certification->expiry_date) {
            return;
        }

        $reminderDays = [90, 60, 30, 14, 7, 1]; // Days before expiry
        
        foreach ($reminderDays as $days) {
            $reminderDate = $certification->expiry_date->subDays($days);
            
            if ($reminderDate->isFuture()) {
                // Here you would schedule the notification job
                Log::info('Certification expiry reminder scheduled', [
                    'certification_id' => $certification->id,
                    'reminder_date' => $reminderDate->toDateString(),
                    'days_before_expiry' => $days
                ]);
            }
        }
    }

    /**
     * Check if certification has important changes
     */
    protected function hasImportantChanges(Certification $certification): bool
    {
        $importantFields = ['name', 'issuer', 'issue_date', 'description'];
        
        foreach ($importantFields as $field) {
            if ($certification->isDirty($field)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Handle the Certification "deleted" event.
     */
    public function deleted(Certification $certification): void
    {
        try {
            // Notify admins about certification deletion
            $this->notifyAdmins('system.certification_deleted', $certification);

            Log::info('Certification deleted notification sent', [
                'certification_id' => $certification->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send certification deletion notification', [
                'certification_id' => $certification->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check for expiring certifications (called by scheduled job)
     */
    public static function checkExpiringCertifications(): void
    {
        $expiringCertifications = Certification::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>', now())
            ->get();

        foreach ($expiringCertifications as $certification) {
            $daysUntilExpiry = now()->diffInDays($certification->expiry_date, false);
            
            // Send appropriate notification based on urgency
            if ($daysUntilExpiry <= 7) {
                Notifications::send('system.certificate_expiring_urgent', $certification);
            } else {
                Notifications::send('system.certificate_expiring', $certification);
            }
        }

        Log::info('Expiring certifications check completed', [
            'expiring_count' => $expiringCertifications->count()
        ]);
    }

    /**
     * Check for expired certifications (called by scheduled job)
     */
    public static function checkExpiredCertifications(): void
    {
        $expiredCertifications = Certification::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->get();

        foreach ($expiredCertifications as $certification) {
            $daysOverdue = now()->diffInDays($certification->expiry_date);
            
            Notifications::send('system.certificate_expired', $certification);
            
            // Consider deactivating expired certifications
            if ($daysOverdue > 90) {
                $certification->update(['is_active' => false]);
                Log::info('Expired certification auto-deactivated', [
                    'certification_id' => $certification->id,
                    'days_overdue' => $daysOverdue
                ]);
            }
        }

        Log::info('Expired certifications check completed', [
            'expired_count' => $expiredCertifications->count()
        ]);
    }

    /**
     * Send certification renewal reminders
     */
    public static function sendRenewalReminders(): void
    {
        $certificationsNeedingRenewal = Certification::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(60))
            ->where('expiry_date', '>', now())
            ->get();

        foreach ($certificationsNeedingRenewal as $certification) {
            Notifications::send('system.certificate_renewal_reminder', $certification);
        }

        Log::info('Certification renewal reminders sent', [
            'renewal_count' => $certificationsNeedingRenewal->count()
        ]);
    }

    /**
     * Generate monthly certification report
     */
    public static function generateMonthlyCertificationReport(): void
    {
        $report = [
            'total_active' => Certification::where('is_active', true)->count(),
            'expiring_this_month' => Certification::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->whereMonth('expiry_date', now()->month)
                ->whereYear('expiry_date', now()->year)
                ->count(),
            'expired_this_month' => Certification::where('expiry_date', '<', now())
                ->whereMonth('expiry_date', now()->month)
                ->whereYear('expiry_date', now()->year)
                ->count(),
            'new_this_month' => Certification::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'near_expiry' => Certification::where('is_active', true)
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<=', now()->addDays(90))
                ->where('expiry_date', '>', now())
                ->count()
        ];

        Notifications::send('system.certification_monthly_report', $report);

        Log::info('Monthly certification report generated', $report);
    }
}