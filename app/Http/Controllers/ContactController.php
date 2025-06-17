<?php
// File: app/Http/Controllers/ContactController.php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\CompanyProfile;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;

class ContactController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display the contact page.
     */
    public function index()
    {
        // Get company profile for contact information
        $companyProfile = CompanyProfile::getInstance();
        
        return view('pages.contact', compact('companyProfile'));
    }
    
    /**
     * Store a newly created message.
     */
    public function store(ContactRequest $request)
    {
        // Create new message
        $message = Message::create($request->validated());
        
        // Send email notification (to be implemented)
        // Mail::to(config('mail.admin'))->send(new ContactFormSubmitted($message));
        
        // Flash success message
        return redirect()->route('contact.index')
            ->with('success', 'Thank you for your message. We will get back to you soon!');
    }
}