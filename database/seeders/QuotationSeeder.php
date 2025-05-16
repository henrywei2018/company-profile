<?php

namespace Database\Seeders;

use App\Models\Quotation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuotationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get service IDs
        $services = Service::pluck('id')->toArray();
        
        // Get client IDs
        $clients = User::role('client')->pluck('id')->toArray();
        
        $statuses = ['pending', 'reviewed', 'approved', 'rejected'];
        $projectTypes = ['New Construction', 'Renovation', 'Expansion', 'Maintenance', 'Infrastructure'];
        $locations = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Makassar', 'Bali', 'Yogyakarta'];
        $budgetRanges = ['Under Rp 100 Million', 'Rp 100-500 Million', 'Rp 500 Million-1 Billion', 'Over Rp 1 Billion'];
        
        // Create quotations from clients
        foreach ($clients as $clientId) {
            $client = User::find($clientId);
            $status = $statuses[array_rand($statuses)];
            $serviceId = $services[array_rand($services)];
            
            Quotation::create([
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'company' => $client->company,
                'service_id' => $serviceId,
                'project_type' => $projectTypes[array_rand($projectTypes)],
                'location' => $client->city ?? $locations[array_rand($locations)],
                'requirements' => 'We need a ' . $projectTypes[array_rand($projectTypes)] . ' project completed within the next 6 months. The project involves ' . Service::find($serviceId)->title . ' and requires professional expertise.',
                'budget_range' => $budgetRanges[array_rand($budgetRanges)],
                'start_date' => now()->addMonths(rand(1, 3))->format('Y-m-d'),
                'status' => $status,
                'client_id' => $clientId,
                'admin_notes' => $status !== 'pending' ? 'Quote reviewed by admin. ' . ($status === 'approved' ? 'Approved and ready for client review.' : ($status === 'rejected' ? 'Rejected due to scope incompatibility.' : 'Under review by management.')) : null,
                'client_approved' => $status === 'approved' ? (rand(0, 1) ? true : null) : null,
                'client_decline_reason' => $status === 'approved' && rand(0, 1) && !isset($client_approved) ? 'Budget constraints require us to postpone this project.' : null,
                'client_approved_at' => $status === 'approved' && isset($client_approved) && $client_approved ? now()->subDays(rand(1, 5)) : null,
                'additional_info' => rand(0, 1) ? 'We prefer to use eco-friendly materials where possible. Also, we need the work to be done during weekends to minimize disruption to our operations.' : null,
            ]);
        }
        
        // Create quotations from non-clients
        for ($i = 0; $i < 5; $i++) {
            $status = $statuses[array_rand($statuses)];
            $serviceId = $services[array_rand($services)];
            
            Quotation::create([
                'name' => fake()->name(),
                'email' => fake()->safeEmail(),
                'phone' => '+62 8' . rand(10, 99) . ' ' . rand(1000, 9999) . ' ' . rand(1000, 9999),
                'company' => rand(0, 1) ? fake()->company() : null,
                'service_id' => $serviceId,
                'project_type' => $projectTypes[array_rand($projectTypes)],
                'location' => $locations[array_rand($locations)],
                'requirements' => 'I am interested in getting a quote for a ' . $projectTypes[array_rand($projectTypes)] . ' project. We need ' . Service::find($serviceId)->title . ' and would like to discuss the details with your team.',
                'budget_range' => $budgetRanges[array_rand($budgetRanges)],
                'start_date' => now()->addMonths(rand(1, 6))->format('Y-m-d'),
                'status' => $status,
                'client_id' => null,
                'admin_notes' => $status !== 'pending' ? 'Quotation ' . ($status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected due to unclear requirements' : 'under review')) . '.' : null,
            ]);
        }
    }
}