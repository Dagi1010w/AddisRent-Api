<?php
namespace App\Http\Controllers\Api; // In the API namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MessagingService; // <-- IMPORT THE SERVICE

class ContactFormController extends Controller
{
    // Inject the service into the controller
    public function __construct(protected MessagingService $messagingService)
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'body' => 'required|string',
        ]);
        
        // One clean line to send the message.
        $this->messagingService->sendContactFormMessage(
            $validated['name'],
            $validated['email'],
            $validated['body']
        );
        
        return response()->json(['message' => 'Message sent successfully!'], 201);
    }
}