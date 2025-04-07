<?php

/** @noinspection ALL */

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends AdminBaseController
{
    use AuthenticatesUsers;

    /**
     * Default authentication view admin
     *
     * @return Application|Factory|View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Authentication for admin with validation request
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): RedirectResponse
    {

        if(!$admin = $this->attemptLogin($request)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
        $this->sendUserDataToAnotherController($admin);

        return redirect()->route('admin.dashboard');

    }

    private function sendUserDataToAnotherController(User $admin)
    {

        session(['user_data' => $admin]);

    }

    /**
     * Determine if the user credentials are correct
     *
     * @param Request $request
     * @return false|Builder|Model|object
     */
    protected function attemptLogin(Request $request)
    {
        $admin = User::query()->where('email', $request->input('email'))
                            ->where('role', 'admin')
                            ->first();

        if($admin && Hash::check($request->input('password'), $admin->password)) {
            Auth::login($admin);

            return $admin;
        }

        return false;

    }

    /**
     * generate unique token from id
     *
     * @param $id
     * @return string
     */
    private function generateToken($id): string
    {
        $token = bcrypt(uniqid($id, true));

        $admin = User::query()->find($id);
        $admin->token = $token;
        $admin->save();

        return $token;
    }
}
