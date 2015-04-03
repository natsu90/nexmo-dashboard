<?php
use Natsu90\Nexmo\NexmoAccount;

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function postLogin()
	{
		if (Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password')), Input::get('remember_me')))
			return Redirect::intended('/');
		return Redirect::to('login')->with('message', 'Login failed!');
	}

	public function postRegister()
	{
		$validator = Validator::make(Input::all(), User::$rules);

		if($validator->passes()) {
			$user = new User;
			$user->username = Input::get('username');
			$user->password = Hash::make(Input::get('password'));
			$user->save();

			if(!Auth::check())
				Auth::login($user);

			return Redirect::to('/');
		} else
			return Redirect::to('register')->with('message', 'The following errors occurred')->withErrors($validator)->withInput();
	}

	public function postSetupNexmo()
	{
		$nexmo_key = Input::get('nexmo_key');
		$nexmo_secret = Input::get('nexmo_secret');

		$nexmo = new NexmoAccount($nexmo_key, $nexmo_secret);

		try {
			// check nexmo credentials
			$credit_balance = $nexmo->getBalance();

			// check db connection
			DB::connection()->getDatabaseName();

			// migrate db
			Artisan::call('migrate');

			// truncate number table
			DB::table('number')->truncate();

			// set nexmo credentials to env
			Cache::forever('NEXMO_KEY', $nexmo_key);
			Cache::forever('NEXMO_SECRET', $nexmo_secret);

			// add numbers to db
			$numbers = $nexmo->getInboundNumbers();
			if($numbers['count'] > 0) {
				foreach($numbers['numbers'] as $num)
				{
					$number = new Number;
					$number->number = $num['msisdn'];
					$number->country_code = $num['country'];
					$number->type = $num['type'];
					$number->features = $num['features'];
					$number->voice_callback_type = $num['voiceCallbackType'];
					$number->voice_callback_value = $num['voiceCallbackValue'];
					$number->save();
				}
			}
			// set dn callback url
        	// $nexmo->updateAccountSettings(array('drCallBackUrl' => url('callback/dn')));
			Queue::getIron()->addSubscriber('HomeController@setupDnCallbackUrl', array('url' => url('queue/receive')));
        	Queue::push('HomeController@setupDnCallbackUrl', array('nexmo_key' => $nexmo_key, 'nexmo_secret' => $nexmo_secret));

			// set balance to cache
			Cache::put('nexmo', $credit_balance, 10);

			if(Auth::check())
				return Redirect::to('/');
			return Redirect::to('/register');

		} catch(Exception $e) {
			return Redirect::to('start')->with('message', $e->__toString());
		}
	}

	public function setupDnCallbackUrl($job, $data)
	{
		$nexmo = new NexmoAccount($data['nexmo_key'], $data['nexmo_secret']);
		$nexmo->updateAccountSettings(array('drCallBackUrl' => url('callback/dn')));

		$job->delete();
	}

}