<?php

namespace Modules\Log\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;


class LogsController extends Controller
{
    public function log($logName,$log,$causedBy,$performedOn,$event)
    {   
        activity($logName)
        ->causedBy($causedBy)
        ->performedOn($performedOn)
        ->withProperties($log)
        ->event($event)
        ->log($event);

    }
}
