<?php
// File: app/Http/Controllers/Admin/EmailSettingsController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Models\Setting;
use Exception;

class EmailSettingsController extends Controller
{
    /**
     * Get setting value with fallback
     */
    private function getSetting($key, $default = null)
    {
        try {
            if (function_exists('settings')) {
                return settings($key, $default);
            }
            
            // Fallback method
            return Setting::where('key', $key)->value('value') ?? $default;
        } catch (Exception $e) {
            return $default;
        }
    }
    
    /**
     * Update setting with fallback
     */
    private function updateSetting($key, $value): bool
    {
        try {
            if (function_exists('update_setting')) {
                return update_setting($key, $value);
            }
            
            // Fallback method
            $setting = Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            
            Cache::forget('settings');
            
            return $setting !== null;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Show email settings page
     */
    public function index()
    {
        // Default email templates
        $defaultMessageReply = $this->getDefaultMessageReplyTemplate();
        $defaultQuotationConfirmation = $this->getDefaultQuotationConfirmationTemplate();
        
        return view('admin.settings.email', compact(
            'defaultMessageReply',
            'defaultQuotationConfirmation'
        ));
    }
    
    /**
     * Update email settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // SMTP Configuration
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_encryption' => 'required|in:tls,ssl,null',
            'mail_username' => 'required|string|max:255',
            'mail_password' => 'required|string|max:255',
            
            // Email Addresses
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'support_email' => 'nullable|email|max:255',
            
            // Message Settings
            'message_email_enabled' => 'boolean',
            'message_auto_reply_enabled' => 'boolean',
            'message_auto_reply_template' => 'nullable|string',
            'message_reply_to' => 'nullable|email|max:255',
            
            // Quotation Settings
            'quotation_email_enabled' => 'boolean',
            'quotation_client_confirmation_enabled' => 'boolean',
            'quotation_confirmation_template' => 'nullable|string',
            'quotation_reply_to' => 'nullable|email|max:255',
            'quotation_cc_email' => 'nullable|email|max:255',
            'quotation_status_updates_enabled' => 'boolean',
            
            // Queue & Delivery Settings
            'queue_driver' => 'required|in:sync,database,redis',
            'mail_logging_enabled' => 'boolean',
            'daily_email_limit' => 'nullable|integer|min:0',
            'email_retry_attempts' => 'nullable|integer|min:0|max:10',
        ]);
        
        try {
            // Update .env file with SMTP settings
            $this->updateEnvironmentFile([
                'MAIL_MAILER' => 'smtp',
                'MAIL_HOST' => $validated['mail_host'],
                'MAIL_PORT' => $validated['mail_port'],
                'MAIL_USERNAME' => $validated['mail_username'],
                'MAIL_PASSWORD' => $validated['mail_password'],
                'MAIL_ENCRYPTION' => $validated['mail_encryption'] === 'null' ? null : $validated['mail_encryption'],
                'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
                'MAIL_FROM_NAME' => '"' . $validated['mail_from_name'] . '"',
                'QUEUE_CONNECTION' => $validated['queue_driver'],
            ]);
            
            // Save other settings to database using bulk update for better performance
            $settingsToSave = [
                'admin_email' => $validated['admin_email'],
                'support_email' => $validated['support_email'] ?? '',
                'message_email_enabled' => $validated['message_email_enabled'] ?? false ? '1' : '0',
                'message_auto_reply_enabled' => $validated['message_auto_reply_enabled'] ?? false ? '1' : '0',
                'message_auto_reply_template' => $validated['message_auto_reply_template'] ?? '',
                'message_reply_to' => $validated['message_reply_to'] ?? $validated['admin_email'],
                'quotation_email_enabled' => $validated['quotation_email_enabled'] ?? false ? '1' : '0',
                'quotation_client_confirmation_enabled' => $validated['quotation_client_confirmation_enabled'] ?? false ? '1' : '0',
                'quotation_confirmation_template' => $validated['quotation_confirmation_template'] ?? '',
                'quotation_reply_to' => $validated['quotation_reply_to'] ?? $validated['admin_email'],
                'quotation_cc_email' => $validated['quotation_cc_email'] ?? '',
                'quotation_status_updates_enabled' => $validated['quotation_status_updates_enabled'] ?? false ? '1' : '0',
                'mail_logging_enabled' => $validated['mail_logging_enabled'] ?? false ? '1' : '0',
                'daily_email_limit' => strval($validated['daily_email_limit'] ?? 500),
                'email_retry_attempts' => strval($validated['email_retry_attempts'] ?? 3),
            ];
            
            // Update settings individually
            $allUpdatesSuccessful = true;
            foreach ($settingsToSave as $key => $value) {
                if (!$this->updateSetting($key, $value)) {
                    $allUpdatesSuccessful = false;
                    Log::warning("Failed to update setting: {$key}");
                }
            }
            
            if (!$allUpdatesSuccessful) {
                Log::warning('Some email settings failed to update');
            }
            
            // Clear config cache to reload new settings
            try {
                Artisan::call('config:cache');
            } catch (Exception $e) {
                Log::warning('Config cache failed: ' . $e->getMessage());
            }
            
            Cache::forget('settings');
            
            // Update runtime config
            $this->updateRuntimeConfig();
            
            return redirect()
                ->route('admin.settings.email')
                ->with('success', 'Email settings updated successfully!');
                
        } catch (Exception $e) {
            Log::error('Email settings update failed: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Test email connection
     */
    public function testConnection(Request $request)
    {
        try {
            // Test by sending a simple email
            Mail::raw('Connection test', function ($message) {
                $message->to('test@test.com') // This won't actually send
                        ->subject('Connection Test');
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Email connection successful!'
            ]);
            
        } catch (Exception $e) {
            Log::error('Email connection test failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Send test email
     */
    public function sendTestEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:general,message,quotation'
        ]);
        
        try {
            $email = $validated['email'];
            $type = $validated['type'];
            
            switch ($type) {
                case 'general':
                    $this->sendGeneralTestEmail($email);
                    break;
                case 'message':
                    $this->sendMessageTestEmail($email);
                    break;
                case 'quotation':
                    $this->sendQuotationTestEmail($email);
                    break;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!'
            ]);
            
        } catch (Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Send general test email
     */
    private function sendGeneralTestEmail($email)
    {
        Mail::raw('This is a test email from your CV Usaha Prima Lestari website. If you received this email, your email configuration is working correctly!', function ($message) use ($email) {
            $message->to($email)
                    ->subject('Test Email - Email Configuration Working')
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }
    
    /**
     * Send message test email
     */
    private function sendMessageTestEmail($email)
    {
        $template = $this->getSetting('message_auto_reply_template', $this->getDefaultMessageReplyTemplate());
        
        // Replace placeholders with test data
        $content = str_replace(
            ['{name}', '{email}', '{subject}'],
            ['Test User', $email, 'Test Message Subject'],
            $template
        );
        
        Mail::send([], [], function ($message) use ($email, $content) {
            $message->to($email)
                    ->subject('Test Message Auto-Reply')
                    ->html($content)
                    ->from(config('mail.from.address'), config('mail.from.name'));
                    
            if ($replyTo = $this->getSetting('message_reply_to')) {
                $message->replyTo($replyTo);
            }
        });
    }
    
    /**
     * Send quotation test email
     */
    private function sendQuotationTestEmail($email)
    {
        $template = $this->getSetting('quotation_confirmation_template', $this->getDefaultQuotationConfirmationTemplate());
        
        // Replace placeholders with test data
        $content = str_replace(
            ['{name}', '{email}', '{service}', '{company}'],
            ['Test Client', $email, 'Construction Services', 'Test Company Ltd'],
            $template
        );
        
        Mail::send([], [], function ($message) use ($email, $content) {
            $message->to($email)
                    ->subject('Test Quotation Confirmation')
                    ->html($content)
                    ->from(config('mail.from.address'), config('mail.from.name'));
                    
            if ($replyTo = $this->getSetting('quotation_reply_to')) {
                $message->replyTo($replyTo);
            }
            
            if ($cc = $this->getSetting('quotation_cc_email')) {
                $message->cc($cc);
            }
        });
    }
    
    /**
     * Update environment file
     */
    private function updateEnvironmentFile(array $data)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }
        
        $envContent = file_get_contents($envFile);
        
        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}=" . (is_null($value) ? '' : $value);
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }
        
        file_put_contents($envFile, $envContent);
    }
    
    /**
     * Update runtime configuration
     */
    private function updateRuntimeConfig()
    {
        Config::set([
            'mail.mailers.smtp.host' => env('MAIL_HOST'),
            'mail.mailers.smtp.port' => env('MAIL_PORT'),
            'mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION'),
            'mail.mailers.smtp.username' => env('MAIL_USERNAME'),
            'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
            'mail.from.address' => env('MAIL_FROM_ADDRESS'),
            'mail.from.name' => env('MAIL_FROM_NAME'),
            'queue.default' => env('QUEUE_CONNECTION'),
        ]);
    }
    
    /**
     * Get default message reply template
     */
    private function getDefaultMessageReplyTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
            <div style="background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #1f2937; margin: 0;">CV Usaha Prima Lestari</h1>
                    <p style="color: #6b7280; margin: 5px 0 0 0;">Professional Construction & General Supplier</p>
                </div>
                
                <h2 style="color: #1f2937; margin-bottom: 20px;">Thank you for contacting us!</h2>
                
                <p style="color: #4b5563; line-height: 1.6; margin-bottom: 15px;">
                    Dear {name},
                </p>
                
                <p style="color: #4b5563; line-height: 1.6; margin-bottom: 15px;">
                    Thank you for reaching out to us. We have received your message regarding "<strong>{subject}</strong>" and will respond within 24 hours.
                </p>
                
                <p style="color: #4b5563; line-height: 1.6; margin-bottom: 20px;">
                    Our team is committed to providing you with the best service possible. If you have any urgent questions, please don\'t hesitate to call us directly.
                </p>
                
                <div style="background-color: #f3f4f6; padding: 20px; border-radius: 6px; margin: 20px 0;">
                    <h3 style="color: #1f2937; margin: 0 0 10px 0; font-size: 16px;">Contact Information:</h3>
                    <p style="color: #4b5563; margin: 5px 0; font-size: 14px;">📧 Email: info@usahaprimalestari.com</p>
                    <p style="color: #4b5563; margin: 5px 0; font-size: 14px;">📞 Phone: +62 XXX XXXX XXXX</p>
                    <p style="color: #4b5563; margin: 5px 0; font-size: 14px;">🌐 Website: www.usahaprimalestari.com</p>
                </div>
                
                <p style="color: #4b5563; line-height: 1.6; margin-bottom: 5px;">
                    Best regards,
                </p>
                <p style="color: #1f2937; font-weight: 600; margin: 0;">
                    CV Usaha Prima Lestari Team
                </p>
                
                <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                
                <p style="color: #9ca3af; font-size: 12px; text-align: center; margin: 0;">
                    This is an automated message. Please do not reply directly to this email.
                </p>
            </div>
        </div>';
    }
    
    /**
     * Get default quotation confirmation template
     */
    private function getDefaultQuotationConfirmationTemplate()
    {
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;">
            <div style="background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #1f2937; margin: 0;">CV Usaha Prima Lestari</h1>
                    <p style="color: #6b7280; margin: 5px 0 0 0;">Professional Construction & General Supplier</p>
                </div>
                
                <h2 style="color: #1f2937; margin-bottom: 20px;">Quotation Request Received</h2>
                
                <p style="color: #4b5563; line-height: 1.6; margin-bottom: 15px;">
                    Dear {name},
                </p>
                
                <p style="color: #4b5563; line-height: 1.6; margin-bottom: 15px;">
                    Thank you for your interest in our services. We have successfully received your quotation request for <strong>{service}</strong>.
                </p>
                
                <div style="background-color: #dbeafe; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0;">
                    <h3 style="color: #1e40af; margin: 0 0 10px 0; font-size: 16px;">📋 Request Details:</h3>
                    <p style="color: #1e3a8a; margin: 5px 0; font-size: 14px;"><strong>Service:</strong> {service}</p>
                    <p style="color: #1e3a8a; margin: 5px 0; font-size: 14px;"><strong>Company:</strong> {company}</p>
                    <p style="color: #1e3a8a; margin: 5px 0; font-size: 14px;"><strong>Contact Email:</strong> {email}</p>
                </div>
                
                <h3 style="color: #1f2937; margin: 25px 0 15px 0;">What happens next?</h3>
                <ul style="color: #4b5563; line-height: 1.6; padding-left: 20px;">
                    <li style="margin-bottom: 8px;">Our team will review your requirements within 24 hours</li>
                    <li style="margin-bottom: 8px;">We may contact you for additional details if needed</li>
                    <li style="margin-bottom: 8px;">You will receive a detailed quotation within 2-3 business days</li>
                    <li style="margin-bottom: 8px;">Our quotation will be valid for 30 days from the date of issue</li>
                </ul>
                
                <div style="background-color: #f3f4f6; padding: 20px; border-radius: 6px; margin: 25px 0;">
                    <h3 style="color: #1f2937; margin: 0 0 10px 0; font-size: 16px;">Contact Information:</h3>
                    <p style="color: #4b5563; margin: 5px 0; font-size: 14px;">📧 Email: quotations@usahaprimalestari.com</p>
                    <p style="color: #4b5563; margin: 5px 0; font-size: 14px;">📞 Phone: +62 XXX XXXX XXXX</p>
                    <p style="color: #4b5563; margin: 5px 0; font-size: 14px;">🌐 Website: www.usahaprimalestari.com</p>
                </div>
                
                <p style="color: #4b5563; line-height: 1.6; margin-bottom: 5px;">
                    Best regards,
                </p>
                <p style="color: #1f2937; font-weight: 600; margin: 0;">
                    CV Usaha Prima Lestari Sales Team
                </p>
                
                <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                
                <p style="color: #9ca3af; font-size: 12px; text-align: center; margin: 0;">
                    This is an automated confirmation. If you have any questions, please reply to this email or contact us directly.
                </p>
            </div>
        </div>';
    }
}