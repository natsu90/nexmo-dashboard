<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Natsu90\Nexmo\NexmoAccount;

class setupNexmoCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nexmo:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$nexmo_key = $this->argument('nexmo_key');
		$nexmo_secret = $this->argument('nexmo_secret');

		$nexmo = new NexmoAccount($nexmo_key, $nexmo_secret);

		try {
			// check nexmo credentials
			Cache::put('nexmo', $nexmo->getBalance(), 10);

			// check db connection
			DB::connection()->getDatabaseName();

			// migrate db
			Artisan::call('migrate');

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

					// set mo and voice callback url
					$nexmo->updateNumber($num['country'], $num['msisdn'], url('/callback/mo'), array('voiceStatusCallback' => url('/callback/voice')));
				}
			}
			// set dn callback url
			$nexmo->updateAccountSettings(array('drCallBackUrl' => url('/callback/dn')));

			// set nexmo credentials to env
			Cache::get('NEXMO_KEY', getenv('NEXMO_KEY'));
			Cache::get('NEXMO_SECRET', getenv('NEXMO_SECRET'));

			print_r($nexmo->getInboundNumbers());

		} catch(Exception $e) {
			Log::error( $e->__toString());
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('nexmo_key', InputArgument::REQUIRED, 'An example argument.'),
			array('nexmo_secret', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

}
