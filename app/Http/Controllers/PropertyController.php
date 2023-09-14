<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\Home;

class PropertyController extends Controller
{
    public function AddOrUpdate(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $propertyId = $request->input('id');
        $property = $propertyId ? Property::find($propertyId) : new Property();

        $imageData = [];

        if ($request->has('images') && is_array($request->input('images'))) {
            foreach ($request->input('images') as $base64Image) {
                $imageData[] = $base64Image;
            }
        }

        $rules = [
            'city_id' => 'required|numeric',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'area' => 'required|numeric',
            'address' => 'required|string|max:255',
            'type' => 'required|in:home,land',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];

        if ($request->input('type') === 'home') {
            $rules['rooms_nb'] = 'required|integer';
            $rules['balconies_nb'] = 'required|integer';
            $rules['bathrooms_nb'] = 'required|integer';
            $rules['garages_nb'] = 'required|integer';
        }

        $validatedData = $request->validate($rules);

        $property->user_id = auth()->user()->id;
        $property->city_id = $validatedData['city_id'];
        $property->title = $validatedData['title'];
        $property->description = $validatedData['description'];
        $property->status = "available";
        $property->price = $validatedData['price'];
        $property->area = $validatedData['area'];
        $property->address = $validatedData['address'];
        $property->type = $validatedData['type'];
        $property->img1 = isset($imageData[0]) ? $imageData[0] : null;
        $property->img2 = isset($imageData[1]) ? $imageData[1] : null;
        $property->img3 = isset($imageData[2]) ? $imageData[2] : null;
        $property->latitude = $validatedData['latitude'];
        $property->longitude = $validatedData['longitude'];

        $property->save();

        if ($property->type === 'home') {
            $home = $property->home ?: new Home();
            $home->property_id = $property->id;
            $home->rooms_nb = $validatedData['rooms_nb'];
            $home->balconies_nb = $validatedData['balconies_nb'];
            $home->bathrooms_nb = $validatedData['bathrooms_nb'];
            $home->garages_nb = $validatedData['garages_nb'];
            $home->save();
        }

        return response()->json(['message' => 'Property created/updated successfully'], 201);
    }


}
