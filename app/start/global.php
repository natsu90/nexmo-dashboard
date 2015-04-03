<?php
use Natsu90\Nexmo\NexmoAccount;
use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

if (is_file(base_path(). '/.env'))
    Dotenv::load(base_path());

class setupDnCallbackUrl {

	public function fire($job, $nexmo_credentials)
	{
		$nexmo = new NexmoAccount($nexmo_credentials['nexmo_key'], $nexmo_credentials['nexmo_secret']);
		$nexmo->updateAccountSettings(array('drCallBackUrl' => url('callback/dn')));

		$job->delete();
	}
}

class setupNumberCallbackUrl {

    public function fire($job, $data)
    {
        $nexmo = new NexmoAccount($data['nexmo_key'], $data['nexmo_secret']);
        $nexmo->updateNumber($data['country_code'], $data['number'], url('callback/mo'), array('voiceStatusCallback' => url('callback/voice')));

        $job->delete();
    }
}

class moCallback {

    public function fire($job, $inbound_id)
    {
        $client = new Client();
        $inbound = Inbound::find($inbound_id);
        $number = Number::where('number', $inbound->to)->first();

        if(filter_var($number->own_callback_url, FILTER_VALIDATE_URL) !== FALSE) {

            try {

                $client->post($number->own_callback_url, array(
                    'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
                    'body' => array_merge($inbound->toArray(), array('callback_type' => 'mo'))
                ));
            } catch(Exception $e) {

            }
        }

        $job->delete();
    }
}