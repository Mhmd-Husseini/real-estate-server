<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Meeting;
use DateTime;
use DateInterval;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DateTimeZone;

class CalendarController extends Controller
{
    public function setAvailable(Request $request)
    {
        $user = auth()->user();
        $validatedData = $request->validate([
            'available_time' => 'required|json',
        ]);

        $user->available_time = $validatedData['available_time'];
        $user->save();

        return response()->json(['message' => 'Available time slots updated successfully']);
    }

    
    public function bookMeeting(Request $request)
    {
        $buyer = Auth::user();
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'property_id' => 'required|exists:properties,id',
            'date' => 'required|date',
        ]);
    
        $meetingDateTime = new DateTime($request->input('date'), new DateTimeZone('EEST'));
        $meetingDateTime->add(new DateInterval('PT3H'));
        $formattedDate = $meetingDateTime->format('Y-m-d H:i:s');
        $meeting = new Meeting([
            'buyer_id' => $buyer->id,
            'seller_id' => $request->input('seller_id'),
            'property_id' => $request->input('property_id'),
            'date' => $formattedDate,
            'status' => "pending",
        ]);
        $meeting->save();
        return response()->json(['message' => 'Meeting saved successfully'], 201);
    }   
}

