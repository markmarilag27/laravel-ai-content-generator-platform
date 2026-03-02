<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        $remember = $request->boolean('remember');
        $user = $request->findUser();

        Auth::login($user, $remember);

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user->load(['workspace:id,public_id'])
        ]);
    }
}
