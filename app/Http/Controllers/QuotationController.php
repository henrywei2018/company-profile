<?php
// File: app/Http/Controllers/QuotationController.php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Service;
use App\Http\Requests\QuotationRequest;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * Display the quotation form.
     */
    public function index()
    {
        // Get services for dropdown
        $services = Service::active()->get();
        
        return view('pages.quotation', compact('services'));
    }
    
    /**
     * Store a newly created quotation.
     */
    public function store(QuotationRequest $request)
    {
        // Create new quotation
        $quotation = Quotation::create($request->validated());
        
        // Link to existing user if authenticated
        if (auth()->check()) {
            $quotation->update(['client_id' => auth()->id()]);
        }
        
        // Send email notification (to be implemented)
        // Mail::to(config('mail.admin'))->send(new QuotationSubmitted($quotation));
        
        // Flash success message
        return redirect()->route('quotation.thank-you');
    }
    
    /**
     * Display thank you page after successful quotation.
     */
    public function thankYou()
    {
        return view('pages.quotation-thank-you');
    }
}