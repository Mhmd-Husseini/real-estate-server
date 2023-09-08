<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\City;
use App\Models\Home;

class GuestController extends Controller
{
    public function getProperties(Request $request)
    {
        $query = Property::query();
        $query->where('status', 'available');

        if ($request->has('type')) {
            $propertyType = $request->input('type');
            $query->where('type', $propertyType);
        }

        if ($request->has('city_name')) {
            $query->whereHas('city', function ($cityQuery) use ($request) {
                $cityQuery->where('city', 'like', '%' . $request->input('city_name') . '%');
            });
        }
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }
        if ($request->has('min_area')) {
            $query->where('area', '>=', $request->input('min_area'));
        }
        if ($request->has('max_area')) {
            $query->where('area', '<=', $request->input('max_area'));
        }

        $properties = $query->with(['city', 'home'])->select(['id','title','price','area','img1','city_id'])->paginate(15);

        return response()->json($properties);
    }
}
