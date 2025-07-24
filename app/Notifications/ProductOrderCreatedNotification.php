<?php

namespace App\Notifications;

use App\Notifications\BaseNotification;
use App\Models\ProductOrder;

class ProductOrderCreatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $order = $this->data;

        if ($order instanceof ProductOrder) {
            $this->subject = "Order Confirmation - {$order->order_number}";
            $this->greeting = "Hello {$order->client_name}!";
            
            $this->addLine("Thank you for your order! We have successfully received your product order.");
            $this->addLine("**Order Details:**");
            $this->addLine("Order Number: {$order->order_number}");
            $this->addLine("Total Amount: " . number_format($order->total_amount) . " IDR");
            
            if ($order->needed_date) {
                $this->addLine("Required Date: {$order->needed_date->format('d F Y')}");
            }
            
            $this->addLine("**Products Ordered:**");
            foreach ($order->items as $item) {
                $this->addLine("• {$item->product->name} (Qty: {$item->quantity}) - " . number_format($item->total) . " IDR");
            }
            
            if ($order->needs_quotation) {
                $this->addLine("**Important Notice:**");
                $this->addLine("Your order requires a formal quotation due to its nature or value. Our team will review your requirements and send you a detailed quotation within 24-48 hours.");
            } else {
                $this->addLine("**What's Next:**");
                $this->addLine("• Our team will process your order within 1-2 business days");
                $this->addLine("• You will receive updates on your order status");
                $this->addLine("• Estimated processing time: 3-5 business days");
            }
            
            $this->setAction('View Order Details', route('client.orders.show', $order));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}