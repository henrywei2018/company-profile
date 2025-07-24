<?php

namespace App\Services;

use App\Models\ProductOrder;
use App\Models\Quotation;

class ProductOrderService
{
    public function createOrderFromCart($cartItems, $clientData, $deliveryInfo)
    {
        $order = ProductOrder::create([
            'order_number' => $this->generateOrderNumber(),
            'client_id' => auth()->id(),
            'client_name' => $clientData['name'],
            'client_email' => $clientData['email'],
            'client_phone' => $clientData['phone'] ?? null,
            'delivery_address' => $deliveryInfo['address'],
            'needed_date' => $deliveryInfo['needed_date'] ?? null,
            'notes' => $deliveryInfo['notes'] ?? null,
        ]);

        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->getCurrentPrice(),
                'specifications' => $item->specifications,
            ]);
        }

        $order->calculateTotal();

        // Check if needs quotation
        if ($order->shouldCreateQuotation()) {
            $this->convertToQuotation($order);
        }

        return $order;
    }

    private function convertToQuotation($order)
    {
        $quotation = Quotation::create([
            'quotation_number' => $this->generateQuotationNumber(),
            'product_order_id' => $order->id,
            'name' => $order->client_name,
            'email' => $order->client_email,
            'phone' => $order->client_phone,
            'client_id' => $order->client_id,
            'project_type' => 'Product Order - ' . $order->order_number,
            'requirements' => $this->generateRequirementsFromOrder($order),
            'has_products' => true,
            'status' => 'pending',
        ]);

        $order->update([
            'quotation_id' => $quotation->id,
            'needs_quotation' => true,
            'status' => 'pending' // Wait for quotation approval
        ]);

        return $quotation;
    }

    private function generateOrderNumber()
    {
        $prefix = 'PO';
        $date = date('Ym');
        $sequence = ProductOrder::whereRaw("order_number LIKE '{$prefix}{$date}%'")->count() + 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function generateRequirementsFromOrder($order)
    {
        $requirements = "Product Order Details:\n\n";
        foreach ($order->items as $item) {
            $requirements .= "- {$item->product->name} (Qty: {$item->quantity})\n";
            if ($item->specifications) {
                $requirements .= "  Specs: {$item->specifications}\n";
            }
        }
        return $requirements;
    }
}