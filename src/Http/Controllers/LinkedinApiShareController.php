<?php

namespace Neurohub\Apilinkedin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Neurohub\Apilinkedin\Classes\LinkedinHelper;

class LinkedinApiShareController extends Controller
{
    public function changeStatus(Request $request)
    {
        if (
            request()->header('Authorization') == md5(config('apiservice.api_md5')) and request()->header('sourecehosting') == 'https://superadmin.neurohub.it/'
            and $request->has('id_attivita') and $request->has('status')
        ) {
            $attivita = \App\Attivita::find($request->id_attivita);
            if (empty($attivita)) {
                return json_encode('Activity not found');
            }
            $attivita->status->linkedin = $request->status;
            $attivita->save();
            return json_encode('OK');
        }
        return json_encode('403');
    }
}
