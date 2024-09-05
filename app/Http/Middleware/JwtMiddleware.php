<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'error' => 'Token not provided.'
            ], 400);
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch (ExpiredException $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Provided token is expired.'], 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'An error while decoding token.'], 400);
        }

        /** @var Admin $admin */
        $admin = Admin::find($credentials->sub);

        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $admin;

        return $next($request);
    }
}
