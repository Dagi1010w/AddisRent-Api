<?php
namespace App\Http\Controllers\Api; // or Api

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\BookingRequestResource;
use App\Services\MessagingService;

class BookingRequestController extends Controller
{
    use AuthorizesRequests;

    protected $messagingService;
    public function __construct(MessagingService $messagingService) {
        $this->messagingService = $messagingService;
    }



    /**
     * Store a new booking request from a Seeker.
     * PROTECTED: User must be authenticated.
     */
    public function store(Request $request, Property $property)
    {
        // 1. Authorize: Check if user is trying to book their own property
        if ($property->lister_id === $request->user()->id) {
            abort(403, 'You cannot book your own property.');
        }

        // 2. Validate
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);
        
        // 3. Create the booking request
        $bookingRequest = BookingRequest::create([
            'seeker_id' => $request->user()->id,
            'property_id' => $property->id,
            'message' => $validated['message'],
        ]);
        
        // You would dispatch a notification event here.
        // event(new BookingRequestSubmitted($bookingRequest));
        
          //4.  Now, also create the initial message associated with it.
          $this->messagingService->sendBookingRequestMessage($bookingRequest, $validated['message']);

        // 5. Return the new resource with a 201 Created status
        return (new BookingRequestResource($bookingRequest))
                ->response()
                ->setStatusCode(201);
    }
    
    /**
     * Get booking requests FOR the logged-in Lister.
     * PROTECTED: Must be a Lister.
     */
    public function indexForLister(Request $request)
    {
        $this->authorize('hasRole', 'lister'); // Simple role check using a custom policy gate
        
        $bookings = $request->user()->receivedBookings()
                                ->with(['property', 'seeker'])
                                ->latest()
                                ->paginate(10);
                                
        return BookingRequestResource::collection($bookings);
    }

    /**
     * Get booking requests SENT BY the logged-in Seeker.
     * PROTECTED: For any authenticated user.
     */
    public function indexForSeeker(Request $request)
    {
        $bookings = $request->user()->sentBookings()
                                ->with('property.lister')
                                ->latest()
                                ->paginate(10);
                                
        return BookingRequestResource::collection($bookings);
    }
    
    /**
     * Update the status of a booking request (by a Lister).
     * PROTECTED: Lister-only action.
     */
    public function update(Request $request, BookingRequest $bookingRequest)
    {
        // Use the policy we wrote: Only the property owner can update.
        $this->authorize('update', $bookingRequest);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $bookingRequest->update(['status' => $validated['status']]);

        
        $this->messagingService->sendBookingStatusUpdate($bookingRequest, true);
        
        return new BookingRequestResource($bookingRequest);
    }

    /**
     * Cancel a booking request (by a Seeker).
     * PROTECTED: Seeker-only action.
     */
    public function destroy(BookingRequest $bookingRequest)
    {
        // Use the policy: Only the Seeker who made the request can delete it,
        // and only if it's still 'pending'.
        $this->authorize('delete', $bookingRequest);
        
        $this->messagingService->sendBookingStatusUpdate($bookingRequest, false);
        
        if ($bookingRequest->status !== 'pending') {
            return response()->json(['error' => 'Cannot delete a non-pending booking request.'], 403);
        }
        
        $bookingRequest->delete();

        return response()->json(null, 204);
    }
}