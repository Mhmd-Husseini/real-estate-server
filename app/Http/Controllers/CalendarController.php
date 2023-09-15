<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;

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
}
