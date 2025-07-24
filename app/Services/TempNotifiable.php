<?php

namespace App\Services;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

/**
 * Temporary notifiable class for sending notifications to non-registered email addresses
 * This allows us to send notifications to custom emails without requiring a User model
 */
class TempNotifiable
{
    use Notifiable;

    protected string $email;
    protected string $name;
    protected array $attributes;

    /**
     * Create a new temporary notifiable instance
     *
     * @param string $email The email address to send notifications to
     * @param string $name The name of the recipient
     * @param array $attributes Additional attributes for the notifiable
     */
    public function __construct(string $email, string $name = '', array $attributes = [])
    {
        $this->email = $email;
        $this->name = $name ?: $this->extractNameFromEmail($email);
        $this->attributes = $attributes;
    }

    /**
     * Get the notification routing information for the given driver.
     *
     * @param string $driver
     * @return mixed
     */
    public function routeNotificationFor($driver, $notification = null)
    {
        switch ($driver) {
            case 'mail':
                return $this->email;
            case 'database':
                // Database notifications not supported for temp notifiables
                return null;
            case 'slack':
                return $this->attributes['slack_webhook'] ?? null;
            case 'discord':
                return $this->attributes['discord_webhook'] ?? null;
            default:
                return $this->email;
        }
    }

    /**
     * Get the entity's notifications.
     * Required by Notifiable trait but not used for temp notifiables
     */
    public function notifications()
    {
        return collect();
    }

    /**
     * Get the entity's read notifications.
     * Required by Notifiable trait but not used for temp notifiables
     */
    public function readNotifications()
    {
        return collect();
    }

    /**
     * Get the entity's unread notifications.
     * Required by Notifiable trait but not used for temp notifiables
     */
    public function unreadNotifications()
    {
        return collect();
    }

    /**
     * Get the email address
     */
    public function getEmailAttribute(): string
    {
        return $this->email;
    }

    /**
     * Get the name
     */
    public function getNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get an attribute value
     */
    public function getAttribute(string $key)
    {
        return match($key) {
            'email' => $this->email,
            'name' => $this->name,
            'id' => null, // Temp notifiables don't have IDs
            default => $this->attributes[$key] ?? null,
        };
    }

    /**
     * Set an attribute value
     */
    public function setAttribute(string $key, $value): self
    {
        match($key) {
            'email' => $this->email = $value,
            'name' => $this->name = $value,
            default => $this->attributes[$key] = $value,
        };

        return $this;
    }

    /**
     * Check if notification preferences allow this notification
     * For temp notifiables, we assume all notifications are allowed
     */
    public function shouldReceiveNotification(Notification $notification): bool
    {
        // Check if this is a type that should be sent to non-registered users
        $allowedForGuests = [
            'message.auto_reply',
            'message.reply',
            'quotation.confirmation',
            'quotation.status_updated',
            'quotation.approved',
            'quotation.rejected',
        ];

        $notificationType = $this->getNotificationType($notification);
        
        return in_array($notificationType, $allowedForGuests);
    }

    /**
     * Extract a reasonable name from an email address
     */
    protected function extractNameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0];
        
        // Replace common separators with spaces and title case
        $name = str_replace(['.', '_', '-', '+'], ' ', $localPart);
        
        return ucwords($name);
    }

    /**
     * Get the notification type from a notification instance
     */
    protected function getNotificationType(Notification $notification): string
    {
        $className = get_class($notification);
        
        // Convert class name to dot notation type
        $shortName = class_basename($className);
        
        // Remove 'Notification' suffix if present
        if (str_ends_with($shortName, 'Notification')) {
            $shortName = substr($shortName, 0, -12);
        }
        
        // Convert CamelCase to dot.case
        $type = strtolower(preg_replace('/([a-z])([A-Z])/', '$1.$2', $shortName));
        
        return $type;
    }

    /**
     * Create a temp notifiable from an array of data
     */
    public static function fromArray(array $data): self
    {
        $email = $data['email'] ?? '';
        $name = $data['name'] ?? '';
        
        unset($data['email'], $data['name']);
        
        return new self($email, $name, $data);
    }
    public static function forProductOrder(string $email, string $name): self
    {
        return new self($email, $name);
    }

    /**
     * Create a temp notifiable for a quotation recipient
     */
    public static function forQuotation(string $email, string $name, array $quotationData = []): self
    {
        return new self($email, $name, [
            'type' => 'quotation_recipient',
            'quotation_data' => $quotationData,
        ]);
    }

    /**
     * Create a temp notifiable for a message recipient
     */
    public static function forMessage(string $email, string $name, array $messageData = []): self
    {
        return new self($email, $name, [
            'type' => 'message_recipient',
            'message_data' => $messageData,
        ]);
    }

    /**
     * Create a temp notifiable for a contact form submission
     */
    public static function forContact(string $email, string $name, array $contactData = []): self
    {
        return new self($email, $name, [
            'type' => 'contact_recipient',
            'contact_data' => $contactData,
        ]);
    }

    /**
     * Get all attributes as an array
     */
    public function toArray(): array
    {
        return array_merge([
            'email' => $this->email,
            'name' => $this->name,
        ], $this->attributes);
    }

    /**
     * Magic method to access attributes
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Magic method to set attributes
     */
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Magic method to check if attribute exists
     */
    public function __isset(string $key): bool
    {
        return $this->getAttribute($key) !== null;
    }

    /**
     * Convert to string (returns email)
     */
    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * Get a unique identifier for this temp notifiable
     * Useful for logging and tracking
     */
    public function getIdentifier(): string
    {
        return 'temp_' . md5($this->email . $this->name);
    }

    /**
     * Check if this temp notifiable represents a valid email
     */
    public function isValid(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Get the domain from the email address
     */
    public function getDomain(): string
    {
        return explode('@', $this->email)[1] ?? '';
    }

    /**
     * Check if the email is from a specific domain
     */
    public function isFromDomain(string $domain): bool
    {
        return strtolower($this->getDomain()) === strtolower($domain);
    }

    /**
     * Get localized name for display
     */
    public function getDisplayName(): string
    {
        if (empty($this->name) || $this->name === $this->extractNameFromEmail($this->email)) {
            return $this->email;
        }
        
        return $this->name;
    }

    /**
     * Check if this represents a business email (common business domains)
     */
    public function isBusinessEmail(): bool
    {
        $personalDomains = [
            'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
            'aol.com', 'icloud.com', 'live.com', 'msn.com'
        ];
        
        return !in_array(strtolower($this->getDomain()), $personalDomains);
    }

    /**
     * Create a collection of temp notifiables from an array of emails
     */
    public static function collection(array $emails): \Illuminate\Support\Collection
    {
        return collect($emails)->map(function ($item) {
            if (is_string($item)) {
                return new self($item);
            } elseif (is_array($item)) {
                return self::fromArray($item);
            }
            
            return null;
        })->filter();
    }

    /**
     * Merge with another temp notifiable or user data
     */
    public function merge(array $data): self
    {
        $this->attributes = array_merge($this->attributes, $data);
        
        if (isset($data['email'])) {
            $this->email = $data['email'];
        }
        
        if (isset($data['name'])) {
            $this->name = $data['name'];
        }
        
        return $this;
    }

    /**
     * Clone this temp notifiable with different attributes
     */
    public function with(array $attributes): self
    {
        return new self($this->email, $this->name, array_merge($this->attributes, $attributes));
    }
}