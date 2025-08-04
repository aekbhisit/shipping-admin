<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\AdminController;

class TemplateController extends AdminController
{
    public function page($name = null)
    {
        if (!empty($name)) {
            return view('components.' . $name);
        } else {
            return view('components.dashboard-human-resources');
        }
    }
}
