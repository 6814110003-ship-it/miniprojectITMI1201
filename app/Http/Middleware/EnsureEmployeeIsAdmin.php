<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $employee = Auth::guard('admin')->user();

        abort_unless($employee?->is_admin, 403, 'เฉพาะเจ้าของร้านเท่านั้นที่จัดการพนักงานได้');
        abort_unless($employee->is_active, 403, 'บัญชีนี้ถูกระงับการใช้งาน');

        return $next($request);
    }
}
