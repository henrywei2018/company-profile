<?php
// File: app/Services/NotificationTypeHelper.php

namespace App\Services;

class NotificationTypeHelper
{
    /**
     * Mapping dari Laravel notification class ke dot notation type
     */
    protected static array $classToTypeMapping = [
        // Chat notifications
        'ChatSessionStartedNotification' => 'chat.session_started',
        'ChatOperatorReplyNotification' => 'chat.operator_reply',
        'ChatMessageReceivedNotification' => 'chat.message_received',
        'ChatSessionClosedNotification' => 'chat.session_closed',
        'ChatOperatorJoinedNotification' => 'chat.operator_joined',
        'ChatOperatorChangedNotification' => 'chat.operator_changed',
        'ChatSessionWaitingNotification' => 'chat.session_waiting',
        'ChatSessionInactiveNotification' => 'chat.session_inactive',
        
        // Project notifications
        'ProjectCreatedNotification' => 'project.created',
        'ProjectUpdatedNotification' => 'project.updated',
        'ProjectStatusChangedNotification' => 'project.status_changed',
        'ProjectCompletedNotification' => 'project.completed',
        'ProjectDeadlineNotification' => 'project.deadline_approaching',
        'ProjectOverdueNotification' => 'project.overdue',
        
        // Quotation notifications
        'QuotationCreatedNotification' => 'quotation.created',
        'QuotationStatusUpdatedNotification' => 'quotation.status_updated',
        'QuotationApprovedNotification' => 'quotation.approved',
        'QuotationRejectedNotification' => 'quotation.rejected',
        'QuotationClientResponseNotification' => 'quotation.client_response_needed',
        'QuotationExpiredNotification' => 'quotation.expired',
        'QuotationConvertedNotification' => 'quotation.converted',
        
        // Message notifications
        'MessageCreatedNotification' => 'message.created',
        'MessageReplyNotification' => 'message.reply',
        'UrgentMessageNotification' => 'message.urgent',
        'MessageAutoReplyNotification' => 'message.auto_reply',
        
        // User notifications
        'WelcomeNotification' => 'user.welcome',
        'EmailVerifiedNotification' => 'user.email_verified',
        'PasswordChangedNotification' => 'user.password_changed',
        'ProfileIncompleteNotification' => 'user.profile_incomplete',
        
        // System notifications
        'SystemMaintenanceNotification' => 'system.maintenance',
        'BackupCompletedNotification' => 'system.backup_completed',
        'SecurityAlertNotification' => 'system.security_alert',
        'CertificateExpiringNotification' => 'system.certificate_expiring',
        
        // Testimonial notifications
        'TestimonialCreatedNotification' => 'testimonial.created',
        'TestimonialApprovedNotification' => 'testimonial.approved',
        'TestimonialFeaturedNotification' => 'testimonial.featured',
        
        // Generic fallback
        'GenericNotification' => 'notification',
    ];

    /**
     * Convert Laravel notification class name to dot notation type
     */
    public static function classToType(string $fullClassName): string
    {
        // Extract class name from full namespace
        $className = class_basename($fullClassName);
        
        // Check direct mapping first
        if (isset(static::$classToTypeMapping[$className])) {
            return static::$classToTypeMapping[$className];
        }
        
        // Fallback: convert CamelCase to dot notation
        return static::convertCamelCaseToDotNotation($className);
    }

    /**
     * Convert CamelCase class name to dot notation
     */
    protected static function convertCamelCaseToDotNotation(string $className): string
    {
        // Remove "Notification" suffix
        $withoutSuffix = str_replace('Notification', '', $className);
        
        // Convert to snake_case first
        $snakeCase = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $withoutSuffix));
        
        // Convert snake_case to dot notation
        // Look for common patterns to place dots
        $patterns = [
            'chat_' => 'chat.',
            'project_' => 'project.',
            'quotation_' => 'quotation.',
            'message_' => 'message.',
            'user_' => 'user.',
            'system_' => 'system.',
            'testimonial_' => 'testimonial.',
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (str_starts_with($snakeCase, $pattern)) {
                return str_replace($pattern, $replacement, $snakeCase);
            }
        }
        
        // If no pattern matches, return as is (fallback)
        return $snakeCase;
    }

    /**
     * Get category from notification type
     */
    public static function getCategory(string $type): string
    {
        $parts = explode('.', $type);
        return $parts[0] ?? 'system';
    }

    /**
     * Get display title from notification type
     */
    public static function getDisplayTitle(string $type): string
    {
        $titleMapping = [
            // Chat notifications
            'chat.session_started' => 'New Chat Session',
            'chat.operator_reply' => 'Chat Reply',
            'chat.message_received' => 'New Chat Message',
            'chat.session_closed' => 'Chat Session Closed',
            'chat.operator_joined' => 'Operator Joined',
            'chat.operator_changed' => 'Operator Changed',
            'chat.session_waiting' => 'Chat Waiting',
            'chat.session_inactive' => 'Chat Inactive',
            
            // Project notifications
            'project.created' => 'New Project',
            'project.updated' => 'Project Updated',
            'project.status_changed' => 'Project Status Changed',
            'project.completed' => 'Project Completed',
            'project.deadline_approaching' => 'Deadline Approaching',
            'project.overdue' => 'Project Overdue',
            
            // Quotation notifications
            'quotation.created' => 'New Quotation',
            'quotation.status_updated' => 'Quotation Updated',
            'quotation.approved' => 'Quotation Approved',
            'quotation.rejected' => 'Quotation Rejected',
            'quotation.client_response_needed' => 'Response Needed',
            'quotation.expired' => 'Quotation Expired',
            'quotation.converted' => 'Quotation Converted',
            
            // Message notifications
            'message.created' => 'New Message',
            'message.reply' => 'Message Reply',
            'message.urgent' => 'Urgent Message',
            'message.auto_reply' => 'Auto Reply',
            
            // User notifications
            'user.welcome' => 'Welcome',
            'user.email_verified' => 'Email Verified',
            'user.password_changed' => 'Password Changed',
            'user.profile_incomplete' => 'Profile Incomplete',
            
            // System notifications
            'system.maintenance' => 'System Maintenance',
            'system.backup_completed' => 'Backup Completed',
            'system.security_alert' => 'Security Alert',
            'system.certificate_expiring' => 'Certificate Expiring',
            
            // Testimonial notifications
            'testimonial.created' => 'New Review',
            'testimonial.approved' => 'Review Approved',
            'testimonial.featured' => 'Review Featured',
        ];
        
        return $titleMapping[$type] ?? ucwords(str_replace(['.', '_'], ' ', $type));
    }

    /**
     * Check if notification type is from specific category
     */
    public static function isCategory(string $type, string $category): bool
    {
        return static::getCategory($type) === $category;
    }

    /**
     * Get all available notification types by category
     */
    public static function getTypesByCategory(): array
    {
        $typesByCategory = [];
        
        foreach (static::$classToTypeMapping as $class => $type) {
            $category = static::getCategory($type);
            $typesByCategory[$category][] = $type;
        }
        
        return $typesByCategory;
    }
}