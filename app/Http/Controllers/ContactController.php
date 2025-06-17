<?php
// File: app/Http/Controllers/ContactController.php

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

    public function index()
    {
        // Set page meta
        $this->setPageMeta(
            'Contact Us - ' . $this->siteConfig['site_title'],
            'Get in touch with us. Contact information and contact form.',
            'contact, get in touch, contact form'
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Contact', 'url' => route('contact.index')]
        ]);

        return view('pages.contact');
    }
    
    public function store(ContactRequest $request)
    {
        // Create new message
        $message = Message::create($request->validated());
        
        // Send email notification (to be implemented)
        // Mail::to(config('mail.admin'))->send(new ContactFormSubmitted($message));
        
        return redirect()->route('contact.index')
            ->with('success', 'Thank you for your message. We will get back to you soon!');
    }
}