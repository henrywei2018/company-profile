<?php

namespace App\Notifications;
use App\Notifications\BaseNotification;
use App\Models\ProductOrder;
class ProductOrderAdminNotification extends BaseNotification
{
    protected function configure(): void
    {
        $order = $this->data;

        if ($order instanceof ProductOrder) {
            $this->subject = "New Product Order - {$order->order_number}";
            $this->greeting = "New Product Order Received!";
            
            $this->addLine("A new product order has been submitted and requires your attention.");
            
            $this->addLine("**Order Information:**");
            $this->addLine("Order Number: {$order->order_number}");
            $this->addLine("Client: {$order->client_name}");
            $this->addLine("Email: {$order->client_email}");
            $this->addLine("Phone: " . ($order->client_phone ?: 'Not provided'));
            $this->addLine("Total Amount: " . number_format($order->total_amount) . " IDR");
            
            $this->addLine("**Products Ordered:**");
            foreach ($order->items as $item) {
                $this->addLine("• {$item->product->name} (Qty: {$item->quantity}) - " . number_format($item->total) . " IDR");
                if ($item->specifications) {
                    $this->addLine("  Specifications: {$item->specifications}");
                }
            }
            
            $this->addLine("**Delivery Information:**");
            $this->addLine("Address: {$order->delivery_address}");
            
            if ($order->needed_date) {
                $this->addLine("Required Date: {$order->needed_date->format('d F Y')}");
            }
            
            if ($order->notes) {
                $this->addLine("**Client Notes:**");
                $this->addLine($order->notes);
            }
            
            if ($order->needs_quotation) {
                $this->addLine("**⚠️ Action Required:**");
                $this->addLine("This order requires a quotation. Please review and create a formal quote for the client.");
            }
            
            $this->setAction('Review Order', route('admin.orders.show', $order));
            $this->salutation = 'System Notification';
        }
    }
}