<?php

namespace App\Events;

use App\Models\Quotation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuotationSubmitted
{
    use Dispatchable, SerializesModels;

    public $quotation;

    /**
     * Create a new event instance.
     *
     * @param Quotation $quotation
     * @return void
     */
    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }
}