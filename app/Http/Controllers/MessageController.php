<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;

class MessageController extends Controller
{
    /**
     * Store a newly created message.
     */
    public function store(StoreMessageRequest $request)
    {
        // Create the message
        $message = Message::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'status' => 'unread',
        ]);
        
        // Handle attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message-attachments', 'public');
                $message->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
        
        // Send notification to admin if needed
        // ...
        
        return redirect()->back()->with('success', 'Your message has been sent successfully. We will get back to you soon.');
    }
}