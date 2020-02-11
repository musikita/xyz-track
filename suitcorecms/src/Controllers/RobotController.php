<?php

namespace Suitcorecms\Controllers;

use App\Http\Controllers\Controller;

class RobotController extends Controller
{
    public function robot()
    {
        return response()
                    ->view(
                        config('app.env', 'local') == 'production' ? 'suitcorecms-robot::production' : 'suitcorecms-robot::default'
                    )
                    ->header('Content-Type', 'text/plain');
    }
}
