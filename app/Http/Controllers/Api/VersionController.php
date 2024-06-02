<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VersionController extends Controller
{
    public function version()
    {
        $version["android"]["user"]["latest"]         = "2.0.0";
        $version["android"]["user"]["critical"]       = "2.0.0";

        $version["android"]["technician"]["latest"]   = "2.0.0";
        $version["android"]["technician"]["critical"] = "2.0.0";

        $version["ios"]["user"]["latest"]             = "1.1.1";
        $version["ios"]["user"]["critical"]           = "1.1.1";

        $version["ios"]["technician"]["latest"]       = "1.1.0";
        $version["ios"]["technician"]["critical"]     = "1.1.0";
        
        return $version;
    }
}
