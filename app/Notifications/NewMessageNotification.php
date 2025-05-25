<?php
// File: app/Notifications/NewMessageNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Message;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = [];
        
        if (settings('message_email_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Message Received - ' . $this->message->subject)
            ->greeting('New Message Alert!')
            ->line('You have received a new message through your website.')
            ->line('**From:** ' . $this->message->name)
            ->line('**Email:** ' . $this->message->email)
            ->line('**Phone:** ' . ($this->message->phone ?: 'Not provided'))
            ->line('**Company:** ' . ($this->message->company ?: 'Not provided'))
            ->line('**Subject:** ' . $this->message->subject)
            ->line('**Message:**')
            ->line($this->message->message)
            ->action('View Message', route('admin.messages.show', $this->message))
            ->line('Please respond to this message as soon as possible.')
            ->replyTo($this->message->email, $this->message->name);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'sender_name' => $this->message->name,
            'sender_email' => $this->message->email,
            'subject' => $this->message->subject,
        ];
    }
}

// File: app/Notifications/MessageAutoReplyNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Message;

class MessageAutoReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = [];
        
        if (settings('message_auto_reply_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $template = settings('message_auto_reply_template');
        
        if ($template) {
            // Replace placeholders in custom template
            $content = str_replace(
                ['{name}', '{email}', '{subject}', '{company}'],
                [
                    $this->message->name,
                    $this->message->email,
                    $this->message->subject,
                    settings('company_name', 'CV Usaha Prima Lestari')
                ],
                $template
            );
            
            return (new MailMessage)
                ->subject('Thank you for contacting us - ' . $this->message->subject)
                ->view('emails.custom-template', ['content' => $content])
                ->replyTo(settings('message_reply_to', settings('admin_email')));
        }
        
        // Default auto-reply
        return (new MailMessage)
            ->subject('Thank you for contacting us - ' . $this->message->subject)
            ->greeting('Hello ' . $this->message->name . '!')
            ->line('Thank you for reaching out to us. We have received your message regarding "' . $this->message->subject . '" and will respond within 24 hours.')
            ->line('Our team is committed to providing you with the best service possible.')
            ->line('If you have any urgent questions, please don\'t hesitate to call us directly.')
            ->line('Best regards,')
            ->line(settings('company_name', 'CV Usaha Prima Lestari') . ' Team')
            ->replyTo(settings('message_reply_to', settings('admin_email')));
    }
}

// File: app/Notifications/NewQuotationNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Quotation;

class NewQuotationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quotation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = [];
        
        if (settings('quotation_email_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('New Quotation Request - ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
            ->greeting('New Quotation Request!')
            ->line('You have received a new quotation request through your website.')
            ->line('**Client:** ' . $this->quotation->name)
            ->line('**Email:** ' . $this->quotation->email)
            ->line('**Phone:** ' . ($this->quotation->phone ?: 'Not provided'))
            ->line('**Company:** ' . ($this->quotation->company ?: 'Not provided'))
            ->line('**Service:** ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
            ->line('**Budget:** ' . ($this->quotation->budget ?: 'Not specified'))
            ->line('**Timeline:** ' . ($this->quotation->timeline ?: 'Not specified'))
            ->line('**Requirements:**')
            ->line($this->quotation->requirements)
            ->action('Review Quotation', route('admin.quotations.show', $this->quotation))
            ->line('Please review and respond to this quotation request promptly.')
            ->replyTo($this->quotation->email, $this->quotation->name);
        
        // Add CC if configured
        if ($ccEmail = settings('quotation_cc_email')) {
            $mail->cc($ccEmail);
        }
        
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'quotation_id' => $this->quotation->id,
            'client_name' => $this->quotation->name,
            'client_email' => $this->quotation->email,
            'service' => $this->quotation->service ? $this->quotation->service->name : 'General',
            'budget' => $this->quotation->budget,
        ];
    }
}

// File: app/Notifications/QuotationConfirmationNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Quotation;

class QuotationConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quotation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = [];
        
        if (settings('quotation_client_confirmation_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $template = settings('quotation_confirmation_template');
        
        if ($template) {
            // Replace placeholders in custom template
            $content = str_replace(
                ['{name}', '{email}', '{service}', '{company}'],
                [
                    $this->quotation->name,
                    $this->quotation->email,
                    $this->quotation->service ? $this->quotation->service->name : 'General Service',
                    $this->quotation->company ?: 'Your Company'
                ],
                $template
            );
            
            return (new MailMessage)
                ->subject('Quotation Request Received - ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
                ->view('emails.custom-template', ['content' => $content])
                ->replyTo(settings('quotation_reply_to', settings('admin_email')));
        }
        
        // Default confirmation
        return (new MailMessage)
            ->subject('Quotation Request Received - ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
            ->greeting('Hello ' . $this->quotation->name . '!')
            ->line('Thank you for your interest in our services. We have successfully received your quotation request for ' . ($this->quotation->service ? $this->quotation->service->name : 'our services') . '.')
            ->line('**What happens next?**')
            ->line('• Our team will review your requirements within 24 hours')
            ->line('• We may contact you for additional details if needed')
            ->line('• You will receive a detailed quotation within 2-3 business days')
            ->line('• Our quotation will be valid for 30 days from the date of issue')
            ->line('If you have any urgent questions, please don\'t hesitate to contact us directly.')
            ->line('Best regards,')
            ->line(settings('company_name', 'CV Usaha Prima Lestari') . ' Sales Team')
            ->replyTo(settings('quotation_reply_to', settings('admin_email')));
    }
}

// File: app/Notifications/QuotationStatusUpdatedNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Quotation;

class QuotationStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quotation;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Quotation $quotation, $oldStatus, $newStatus)
    {
        $this->quotation = $quotation;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = [];
        
        if (settings('quotation_status_updates_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $statusMessages = [
            'pending' => 'Your quotation request is being reviewed by our team.',
            'reviewed' => 'Your quotation request has been reviewed and we will send you a detailed quote soon.',
            'approved' => 'Great news! Your quotation request has been approved. We will send you the detailed quotation shortly.',
            'rejected' => 'Thank you for your interest. Unfortunately, we cannot proceed with your quotation request at this time.',
            'sent' => 'Your quotation has been prepared and sent. Please check your email for the detailed quote.',
            'accepted' => 'Thank you for accepting our quotation! We will contact you soon to discuss the next steps.',
            'completed' => 'Your project has been successfully completed. Thank you for choosing our services!',
        ];

        $message = $statusMessages[$this->newStatus] ?? 'Your quotation status has been updated.';

        return (new MailMessage)
            ->subject('Quotation Status Update - ' . ucfirst($this->newStatus))
            ->greeting('Hello ' . $this->quotation->name . '!')
            ->line('Your quotation request status has been updated.')
            ->line('**Service:** ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
            ->line('**Status:** ' . ucfirst($this->newStatus))
            ->line($message)
            ->when($this->newStatus === 'approved', function ($mail) {
                return $mail->line('We will contact you within 24 hours to discuss the details and next steps.');
            })
            ->when($this->newStatus === 'rejected', function ($mail) {
                return $mail->line('If you have any questions about this decision, please feel free to contact us.');
            })
            ->line('If you have any questions, please don\'t hesitate to reach out to us.')
            ->line('Best regards,')
            ->line(settings('company_name', 'CV Usaha Prima Lestari') . ' Team')
            ->replyTo(settings('quotation_reply_to', settings('admin_email')));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'quotation_id' => $this->quotation->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'client_name' => $this->quotation->name,
        ];
    }
}