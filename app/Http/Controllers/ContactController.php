<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;

class ContactController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        // Set page meta
        $this->setPageMeta(
            'Contact Us - ' . $this->siteConfig['site_title'],
            'Get in touch with us. Contact information and contact form.',
            'contact, get in touch, contact form'
        );

        return view('pages.contact');
    }
    
    public function store(ContactRequest $request)
    {
        try {
            // Create new message
            $messageData = $request->validated();
            
            // Add additional fields if not in form
            $messageData['type'] = 'contact_form';
            $messageData['is_read'] = false;
            $messageData['is_replied'] = false;
            
            $message = Message::create($messageData);
            return redirect()->route('contact.thank-you')
                ->with('success', 'Thank you for your message. We will get back to you soon!')
                ->with('message_id', $message->id);
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            // Redirect back with error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sorry, there was an error sending your message. Please try again or contact us directly.');
        }
    }
    
    public function thankYou()
    {
        // Set page meta
        $this->setPageMeta(
            'Thank You - ' . $this->siteConfig['site_title'],
            'Thank you for contacting us. We have received your message and will get back to you soon.',
            'thank you, contact confirmation, message received'
        );

        return view('pages.thank-you');
    }
}