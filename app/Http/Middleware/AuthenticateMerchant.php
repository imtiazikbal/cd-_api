<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\merchantCheck;
use Illuminate\Support\Str;

class AuthenticateMerchant
{
    use merchantCheck;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!empty($request)) {
            if(!$this->checkIfLogin($request)) {
                return response()->json(
                    ['errMgs' => 'Not Authorized to make this request'],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return $next($request);

        }

        return $next($request);
    }

    protected function checkIfLogin(Request $request): bool
    {
        $id = $request->header('id');
        $token = Str::replace('Bearer ', '', $request->header('authorization'));
        $isMerchant = $this->isAuthenticated($id, $token);

        return $isMerchant ? true : false;
    }
}
