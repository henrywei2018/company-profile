<?php

namespace App\Notifications;

class SystemMaintenanceNotification extends BaseNotification
{
    protected function configure(): void
    {
        $maintenanceData = $this->data;
        
        $this->subject = "Scheduled System Maintenance - " . config('app.name');
        $this->greeting = "Important Notice!";
        
        $this->addLine("We will be performing scheduled system maintenance:");
        
        if (is_array($maintenanceData)) {
            if (isset($maintenanceData['start_time'])) {
                $this->addLine("Start Time: " . $maintenanceData['start_time']);
            }
            if (isset($maintenanceData['end_time'])) {
                $this->addLine("End Time: " . $maintenanceData['end_time']);
            }
            if (isset($maintenanceData['description'])) {
                $this->addLine("Description: " . $maintenanceData['description']);
            }
        }
        
        $this->addLine("During this time, some features may be temporarily unavailable.");
        $this->addLine("We apologize for any inconvenience and appreciate your patience.");
        
        $this->salutation = 'Best regards,<br>' . config('app.name') . ' Technical Team';
    }
}