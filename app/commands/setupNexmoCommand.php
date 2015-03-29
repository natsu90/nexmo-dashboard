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
			$credit_balance = $nexmo->getBalance();

			// check db connection
			DB::connection()->getDatabaseName();

			// migrate db
			Artisan::call('migrate');

			// truncate number table
			DB::table('number')->truncate();

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
			$nexmo->updateAccountSettings(array('drCallBackUrl' => url('/callback/dn')));

			// set balance to cache
			Cache::put('nexmo', $credit_balance, 10);

			// set nexmo credentials to env
			Cache::forever('NEXMO_KEY', $nexmo_key);
			Cache::forever('NEXMO_SECRET', $nexmo_secret);

			print_r($nexmo->getInboundNumbers());

		} catch(Exception $e) {

			$this->error('Something went wrong! Error: '.$e->getMessage());
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
			array('nexmo_key', InputArgument::REQUIRED, 'Nexmo Key'),
			array('nexmo_secret', InputArgument::REQUIRED, 'Nexmo Secret'),
		);
	}

}
