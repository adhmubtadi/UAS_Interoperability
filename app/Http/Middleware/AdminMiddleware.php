<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ResponseHelper;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Ambil user yang sedang login
        $user = $request->user();

        // Jika user tidak ada (belum login)
        if (!$user) {
            return ResponseHelper::error('Unauthorized', null, 401);
        }

        // Jika role bukan admin
        if ($user->role !== 'admin') {
            return ResponseHelper::error(
                'Forbidden. Admin access only.',
                null,
                403
            );
        }

        // Lanjutkan request jika user adalah admin
        return $next($request);
    }
}
