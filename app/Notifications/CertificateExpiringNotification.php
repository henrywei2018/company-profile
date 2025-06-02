<?php

namespace App\Notifications;

use App\Models\Certification;

class CertificateExpiringNotification extends BaseNotification
{
    protected function configure(): void
    {
        $certification = $this->data;
        
        if ($certification instanceof Certification) {
            $daysUntilExpiry = $certification->expiry_date ? now()->diffInDays($certification->expiry_date, false) : 0;
            $isExpired = $daysUntilExpiry < 0;
            
            if ($isExpired) {
                $this->subject = "Certificate Expired: {$certification->name}";
                $this->greeting = "Critical Alert!";
                $this->addLine("Certificate '{$certification->name}' has expired " . abs($daysUntilExpiry) . " day(s) ago.");
            } else {
                $this->subject = "Certificate Expiring Soon: {$certification->name}";
                $this->greeting = "Attention Required!";
                $this->addLine("Certificate '{$certification->name}' will expire in {$daysUntilExpiry} day(s).");
            }
            
            $this->addLine("Issuer: {$certification->issuer}");
            $this->addLine("Expiry Date: " . $certification->expiry_date->format('M d, Y'));
            $this->addLine("Please take immediate action to renew this certificate.");
            
            $this->setAction('View Certificate', route('admin.certifications.show', $certification));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}