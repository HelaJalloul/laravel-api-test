<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * AuthMiddleware a device and return the token if the provided credentials are correct.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     *
     */
    public function authenticate(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required'
            ]);

            date_default_timezone_set("Europe/Paris");

            /** @var Admin $admin */
            $admin = Admin::query()->where('email', $request->input('email'))->first();

            // Verify the admin and the password
            if ($admin === null || !Hash::check($request->input('password'), $admin->password)) {
                return response()->json("L'adresse mail ou le mot de passe est incorrect", 403);
            }

            // Return new auth token
            return response()->json([
                'token' => $this->jwt($admin),
                'timestamp' => time()
            ]);

        } catch (Exception $ex) {
            Log::error(__METHOD__ . ' -- ' . $ex->getMessage());
            return response()->json("Erreur pendant le login", 500);
        }
    }

    /**
     * Create a new session token.
     *
     * @param Admin $admin
     * @return string
     */
    private function jwt(Admin $admin): string
    {
        $payload = [
            'iss' => "HelloCSE-API", // Issuer of the token
            'sub' => $admin->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 86400 // Expiration time - 24 Heures
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }
}

