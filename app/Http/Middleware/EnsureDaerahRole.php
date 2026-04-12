<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDaerahRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::DAERAH) {
            return response()->json(['message' => 'Unauthorized. Hanya dapat diakses oleh daerah.'], 403);
        }

        if (! $user->daerah || ! $user->daerah->is_active) {
            return response()->json(['message' => 'Akun daerah tidak aktif.'], 403);
        }

        return $next($request);
    }
}
