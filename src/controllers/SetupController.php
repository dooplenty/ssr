<?php

namespace Dooplenty\SyncSendRepeat\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class SetupController extends Controller
{
    public function getSetup()
    {
    	return view('syncsendrepeat::email.setup');
    }

    public function postSetup(Request $request)
    {
    	$validator = $this->validator($request->all());

    	if($validator->fails()) {
    		
    	}
    }

    protected function validator()
    {
    	return Validator::make($data,[
    		'username' => 'required',
    		'password' => 'required',
    		'port' => 'required',
    		'hostname' => 'required',
    		'protocol' => 'required'
    	]);
    }
}
