<?php
namespace App\Services;

use App\Models\User;
use App\Models\Message;
use App\Models\BookingRequest;
use Illuminate\Support\Facades\Auth;

class MessagingService
{
    /**
     * Send a message originating from the public "Contact Us" form.
     * The message is from a guest (or user) TO the Admin.
     *
     * @param string $senderName
     * @param string $senderEmail
     * @param string $body
     * @return Message
     */
    public function sendContactFormMessage(string $senderName, string $senderEmail, string $body): Message
    {
        $admin = User::role('admin')->firstOrFail();
        $loggedInUser = Auth::user();

        // Prepend guest info to the body if not logged in
        $messageBody = $body;
        if (!$loggedInUser) {
            $messageBody = "Guest Message From: {$senderName} ({$senderEmail})\n\n{$body}";
        }

        return Message::create([
            'sender_id' => $loggedInUser?->id, // Use safe navigation operator
            'recipient_id' => $admin->id,
            'body' => $messageBody,
        ]);
    }

    /**
     * Send the initial booking request message.
     * The message is from a Seeker TO a Lister.
     *
     * @param BookingRequest $bookingRequest
     * @param string $body
     * @return Message
     */
    public function sendBookingRequestMessage(BookingRequest $bookingRequest, string $body): Message
    {
        return Message::create([
            'sender_id' => $bookingRequest->seeker_id,
            'recipient_id' => $bookingRequest->property->lister_id,
            'body' => $body,
            'related_to_booking_id' => $bookingRequest->id,
        ]);
    }

    /**
     * Send a reply or new message in an existing conversation.
     *
     * @param User $sender
     * @param User $recipient
     * @param string $body
     * @param BookingRequest|null $bookingRequest
     * @return Message
     */
    public function sendReply(User $sender, User $recipient, string $body, BookingRequest $bookingRequest = null): Message
    {
         return Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'body' => $body,
            'related_to_booking_id' => $bookingRequest?->id,
        ]);
    }
    
    /**
     * Send a notification that a booking has been approved or rejected.
     * The message is an automated System message FROM a Lister TO a Seeker.
     *
     * @param BookingRequest $bookingRequest
     * @param bool $wasApproved
     * @return Message
     */
    public function sendBookingStatusUpdate(BookingRequest $bookingRequest, bool $wasApproved): Message
    {
        $statusText = $wasApproved ? 'APPROVED' : 'REJECTED';
        $messageBody = "SYSTEM NOTIFICATION: Your booking request for the property '{$bookingRequest->property->title}' has been {$statusText} by the lister.";
        
        return Message::create([
            'sender_id' => $bookingRequest->property->lister_id, // The message is "from" the lister
            'recipient_id' => $bookingRequest->seeker_id,
            'body' => $messageBody,
            'related_to_booking_id' => $bookingRequest->id,
        ]);
    }
}