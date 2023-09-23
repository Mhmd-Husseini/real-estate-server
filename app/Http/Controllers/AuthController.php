<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\ClientException;
use Laravel\Passport\HasApiTokens;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller{

    public function unauthorized(Request $request){
        return response()->json([
            'status' => 'Error',
            'message' => 'Unauthorized',
        ], 200);
    }

    public function profile(Request $request){
        return response()->json([
            'status' => 'Success',
            'authenticated' => auth()->check(),
            'user' => auth()->user(),
        ], 200);
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

    public function updateUser(Request $request)
{
    $user = auth()->user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'phone' => 'required|string',
    ]);

    if ($request->has('password')) {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);
        $user->password = Hash::make($request->password);
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->save();

    return response()->json([
        'status' => 'Success',
        'data' => $user,
    ]);
}

    public function redirectToAuth(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')
                         ->stateless()
                         ->redirect()
                         ->getTargetUrl(),
        ]);
    }

    public function handleAuthCallback(): JsonResponse
    {
        try {
            /** @var SocialiteUser $socialiteUser */
            $socialiteUser = Socialite::driver('google')->stateless()->user();
        } catch (ClientException $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        /** @var User $user */
        $randomPassword = Str::random(12);
        $user = User::query()
            ->firstOrCreate(
                [
                    'email' => $socialiteUser->getEmail(),
                ],
                [
                    'email_verified_at' => now(),
                    'name' => $socialiteUser->getName(),
                    'google_id' => $socialiteUser->getId(),
                    'password' => Hash::make($randomPassword),
                    'role_id' => 2
                ]
            );
            $token = Auth::login($user);
            $user->token = $token;
            $user->role = "user";
        return response()->json([
            'user' => $user,
            'token_type' => 'Bearer',
        ]);
    }
}