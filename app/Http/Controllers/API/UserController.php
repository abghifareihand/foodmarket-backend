<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function login(Request $request)
    {
        try {
            // validasi input
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            // mengecek credential (login)
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            // jika hash tidak sesuai maka error
            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            // jika berhasil maka login
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules()
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'houseNumber' => $request->houseNumber,
                'phoneNumber' => $request->phoneNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoked');
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success(
            $request->user(),
            'Get Profile Success'
        );
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();

        // $user = Auth::user();
        $user = $request->user();
        $user->update($data);

        return ResponseFormatter::success($user, 'Profile Update Success');
    }

    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|max:2048'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()
            ], 'Update Photo Failed', 401);
        }

        if ($request->file('file')) {
            $file = $request->file('file');

            // Buat nama file baru dengan menggunakan time() dan ekstensi file asli
            $nameFile = time() . '.' . $file->getClientOriginalExtension();

            // Simpan file di dalam folder 'user' dalam storage 'public'
            $file->storeAs('public/user', $nameFile);

            // Tentukan URL lengkap untuk gambar
            $imagePath = url('storage/user/' . $nameFile);

            // Simpan foto ke database (url)
            // $user = Auth::user();
            $user = $request->user();
            $user->profile_photo_path = $imagePath;
            $user->update();

            return ResponseFormatter::success([
                'image_path' => $imagePath,
            ], 'File Upload Success');
        }
    }


}
