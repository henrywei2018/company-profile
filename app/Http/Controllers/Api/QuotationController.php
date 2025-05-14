<?php
// File: app/Http/Controllers/Api/QuotationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuotationController extends Controller
{
    protected $quotationService;

    /**
     * Create a new controller instance.
     *
     * @param QuotationService $quotationService
     */
    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
     * Submit a quotation request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'requirements' => 'required|string',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'g-recaptcha-response' => 'sometimes|required', // If using reCAPTCHA
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create quotation data
        $quotationData = $validator->validated();
        $quotationData['status'] = 'pending';
        
        // Handle client association if authenticated
        if (auth('api')->check()) {
            $user = auth('api')->user();
            $quotationData['client_id'] = $user->id;
        }
        
        // Use service to create quotation (without attachments for API)
        $quotation = $this->quotationService->createQuotation($quotationData);
        
        // Fire event
        // event(new QuotationSubmitted($quotation));
        
        return response()->json([
            'success' => true,
            'message' => 'Your quotation request has been submitted successfully. We will contact you soon!',
            'data' => [
                'id' => $quotation->id,
                'created_at' => $quotation->created_at
            ]
        ]);
    }
}