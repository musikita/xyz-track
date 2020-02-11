<?php

namespace Suitcorecms\Authentications;

use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use URL;

trait AuthenticationTrait
{
    protected $webIndexRouteName = 'web.login';
    protected $cmsLoginView = 'suitcorecms::auth.login';

    public function cmsLoginForm()
    {
        return view($this->cmsLoginView, ['url' => 'cms']);
    }

    public function cmsLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('cms')->attempt(['email' => $request->email, 'password' => $request->password], $request->filled('remember'))) {
            return redirect()->intended(route('cms.index'));
        }

        $request->session()->flash('is_cms_login_error_popup', true);

        return back()->withInput($request->only('email', 'remember'));
    }

    public function cmsLogout()
    {
        Auth::logout();

        return redirect()->route('cms.login');
    }

    public function webLogin(Request $request)
    {
        $this->validate($request, [
            'username'    => 'required|string',
            'password'    => 'required|min:6',
        ]);

        if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password], $request->filled('remember'))) {
            if (Auth::user()->email_verified_at == false) {
                $activationUrl = URL::temporarySignedRoute('verification.resend.web', Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)), ['id' => auth()->user()->getKey()]);
                Auth::logout();
                $request->session()->flash('user_activation_url', $activationUrl);

                return back()->withInput($request->only('username', 'remember'));
            }

            return redirect()->intended(route($this->webIndexRouteName));
        }

        $request->session()->flash('show_web_login_error_popup', true);

        return back()->withInput($request->only('username', 'remember'));
    }

    public function webLogout()
    {
        Auth::logout();

        return redirect()->route($this->webIndexRouteName);
    }
}
