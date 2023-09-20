<?php 

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller{

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function unauthorized(Request $request){
        return response()->json([
            'status' => 'Error',
            'message' => 'Unauthorized',
        ], 200);
    }

    public function profile(Request $request){
        $user = Auth::user();
        if ($user) {
            return response()->json([
                'status' => 'Success',
                'authenticated' => true,
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'status' => 'Success',
                'authenticated' => false,
                'user' => null,
            ], 200);
        }
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
 
        if (!$token) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        $user->token = $token;
        $user->role = $user->user_type_id == 1 ? "admin" : ($user->user_type_id == 3 ? "author" : "user");
        
        return response()->json([
                'status' => 'Success',
                'data' => $user
            ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
        ]);

        $user = new User; 
        $user->role_id = 2; 
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->available_time = $request->available_time;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = Auth::login($user);
        $user->token = $token;
        $user->role = "user";

        return response()->json([
            'status' => 'Success',
            'data' => $user
        ]);
    }

    public function logout(){
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh() {
        $user = Auth::user();
        $user->token = Auth::refresh();

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

}