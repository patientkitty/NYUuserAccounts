<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;

class TestController extends Controller
{
    //
    public function show()
    {
        $logs = Logs::all();

        foreach ($logs as $log) {
            echo $log->NetID;
        }
        //echo "hello";
    }
}
