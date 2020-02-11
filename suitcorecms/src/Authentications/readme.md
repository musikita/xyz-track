## Auth/LoginController

Add Suitcorecms\Authentications\AuthenticationTrait;

## App\Exceptions\Handler@unauthenticated

```PHP
if ($request->expectsJson()) {
    return  response()->json(['message' => $exception->getMessage()], 401);
}
if ($request->is('cms') || $request->is('paneladmin/*') || $request->is('paneladmin')) {
    return redirect()->guest(route('cms.login'));
}
if ($request->is('web') || $request->is('/*')) {
    return redirect()->guest('/login');
}

return redirect()->guest('/login');

```

## App\Http\Middleware\RedirectIfAuthenticated@handle

```PHP
if ($request->is('/login') && Auth::guard('web')->check()) {
    return redirect('/');
}
if ($request->is('paneladmin/login') && Auth::guard('cms')->check()) {
    return redirect()->route('cms.index');
}

return $next($request);

```

## routes/web.php

```PHP
Route::middleware(['guest'])->group(function () {
    // Web Auth
    Route::post('/login', 'Auth\LoginController@webLogin')->name('web.login.post');

    // CMS Auth
    Route::get('/paneladmin/login', 'Auth\LoginController@cmsLoginForm')->name('cms.login');
    Route::post('/paneladmin/login', 'Auth\LoginController@cmsLogin')->name('cms.login.post');
});

Route::middleware(['auth:cms'])->group(function () {
    Route::get('/paneladmin', 'Cms\DashboardController@index')->name('cms.index');
    Route::get('/paneladmin/logout', 'Auth\LoginController@cmsLogout')->name('cms.logout');
});

Route::middleware(['auth:web'])->group(function () {
    Route::get('/logout', 'Auth\LoginController@webLogout')->name('web.logout');
});

```

## .env

```ENV
CMS_AUTH=true
```

