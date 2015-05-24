<?php
use Natsu90\Nexmo\NexmoAccount;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::filter('nexmo', function() {

	$nexmo_key = getenv('NEXMO_KEY') ?: (Schema::hasTable('cache') ? Cache::get('NEXMO_KEY') : false);
	$nexmo_secret = getenv('NEXMO_SECRET') ?: (Schema::hasTable('cache') ? Cache::get('NEXMO_SECRET') : false);
	if(!$nexmo_key || !$nexmo_secret)
		return Redirect::to('start');

	// validate nexmo credentials
	$nexmo = new NexmoAccount($nexmo_key, $nexmo_secret);

	try {

		$isNexmoValid = Cache::get('nexmo');
		if(!$isNexmoValid)
			Cache::put('nexmo', $nexmo->getBalance(), 10);

	} catch (Exception $e) {

		return Redirect::to('start');
	}  
});

/*
*	Main page
*/

Route::filter('dashboardFilter', function() {

	if(!Schema::hasTable('users'))
		return Redirect::to('start');

	if(!User::all()->count())
		return Redirect::to('register');

	if(!Auth::check())
		return Redirect::to('login');
});

Route::get('/', array('before' => array('dashboardFilter', 'nexmo'), function() {

	return View::make('hello', array('credit_balance' => Cache::pull('nexmo'), 'numbers' => Number::all()));
}));

/*
*	Login page
*/

Route::filter('loginFilter', function() {

	if(!Schema::hasTable('users'))
		return Redirect::to('start');

	if(!User::all()->count())
		return Redirect::to('register');
});

Route::get('/login', array('before' => array('loginFilter', 'nexmo'), 'as' => 'login', function() {

	return View::make('login');
}));

Route::post('/login', 'HomeController@postLogin');

/*
*	Register page
*/

Route::filter('registerFilter', function() {

	if(!Schema::hasTable('users'))
		return Redirect::to('start');

	if(User::all()->count()) {
		if(!Auth::check())
			return Redirect::to('login');
	}
});

Route::get('/register', array('before' => array('registerFilter', 'nexmo'), function() {

	return View::make('register');
}));

Route::post('/register', 'HomeController@postRegister');

/*
*	Setup page
*/

Route::filter('startFilter', function() {

	$nexmo_key = getenv('NEXMO_KEY') ?: (Schema::hasTable('cache') ? Cache::get('NEXMO_KEY') : false);
	$nexmo_secret = getenv('NEXMO_SECRET') ?: (Schema::hasTable('cache') ? Cache::get('NEXMO_SECRET') : false);

	if(Schema::hasTable('users') && $nexmo_key && $nexmo_secret) {
		if(User::all()->count()) {
			if(!Auth::check())
				return Redirect::to('login');
		} else
			return Redirect::to('register');
	}
});

Route::get('/start', array('before' => 'startFilter', function() {

	return View::make('setup');
}));

Route::post('/start', 'HomeController@postSetupNexmo');

/* Logout */

Route::get('/logout', function() {

	Auth::logout();
	return Redirect::to('login');
});

/* Queue */

Route::post('queue/receive', function()
{
    return Queue::marshal();

    //return Response::make(array('foo' => 'bar'), 202);
});

/* Callback Url */

Route::match(array('GET', 'POST'), 'callback/{item?}', function($item = 'debug') {

	switch($item)
	{
		case 'mo':
			if(!Input::has('msisdn') || !Input::has('to') || !Input::has('text'))
				break;
			$inbound = new Inbound;
			$inbound->from = Input::get('msisdn');
			$inbound->to = Input::get('to');
			$inbound->text = Input::get('text');
			$inbound->type = Input::get('type');
			$inbound->save();
			break;
		case 'dn':
			$outbound_chunk = OutboundChunk::where('message_id','=',Input::get('messageId'))->first();
			if (!$outbound_chunk)
				break;
			$outbound_chunk->dn_status = Input::get('status');
			$outbound_chunk->dn_error_code = Input::get('err-code');
			$outbound_chunk->save();
			break;
		case 'voice':
			if (!Input::has('call-id'))
				break;
			$call = new CallLog;
			$call->call_id = Input::get('call-id');
			$call->to = Input::get('to');
			$call->status = Input::get('status');
			$call->price = Input::get('call-price');
			$call->rate = Input::get('call-rate');
			$call->duration = Input::get('call-duration');
			$call->start_time = Input::get('call-start');
			$call->end_time = Input::get('call-end');
			$call->direction = Input::get('call-direction');
			$call->save();
	}

    return Response::make('OK');
});

/* API */

Route::api('v1', function() {

    Route::resource('inbound', 'InboundController', array('only' => array('index', 'show')));
    Route::post('outbound/whatsapp', 'OutboundController@postReplyWhatsapp');
    Route::resource('outbound', 'OutboundController', array('only' => array('index', 'show', 'store')));
    Route::get('number/calls', 'NumberController@getCalls');
    Route::get('number/search/{country_code}', 'NumberController@getSearch');
    Route::resource('number', 'NumberController', array('only' => array('index', 'show', 'store', 'update', 'destroy')));
});

/* VoiceXML Sample */

Route::get('voicexml.xml', function() {

$xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
	<vxml version = "2.1">
  		<form>
    		<block>
    			<prompt>
      				Surprise madafaka!
    			</prompt>
    		</block>
  		</form>
	</vxml>
EOT;

	return Response::make($xml)->header('Content-Type', 'text/xml');
});