<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Natsu90\Nexmo\NexmoAccount;

class WhatsAppRegister extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'whatsapp:register';

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
		$this->line('==============================/');
		$this->line('Registering WhatsApp');
		$this->line('==============================/');

		$numbers = Number::where('type', 'like', 'mobile%')->where('features', 'like', '%SMS%')->get(array('number'))->map(function($number) {return $number->number;})->toArray();
		if(empty($numbers))
			return $this->error('No mobile number found.');
		$number = $this->choice('Choose which number to register.', $numbers);

		$numberObj = Number::where('number', $number)->first();

		if($numberObj->voice_callback_type != 'tel') {
			
			$newNumber = $this->ask('Enter your personal mobile number to receive whatsapp verification code.');

			$numberObj->voice_callback_type = 'tel';
			$numberObj->voice_callback_value = $newNumber;
			$isSaved = $numberObj->save();

			if(!$isSaved)
				return $this->error('Number is fail to update.');
		}
		// confirm personal number to forward call
		$isNumberOkay = $this->confirm('Verification code will be sent to this number, '. $numberObj->voice_callback_value.'. Proceed?', true);
		if(!$isNumberOkay)
			return;
		// registering
		$proceed = $this->confirm('This is very important. You will receive the verification code via phone call, you have to key in the code before the phone call end (around 30 secs) and correct on first try, otherwise you have to wait for 30 mins to 24 hours to get another verification code. Proceed?', true);
		$wa = new WhatsProt($number, $number, false);
		try {

			$waResponse = $wa->codeRequest('voice');
			if($waResponse->status != 'ok') {

				$verificationCode = str_replace('-', '', $this->ask('Enter your verification code.'));

				$waResponse = $wa->codeRegister($verificationCode);
			}

			$numberObj->wa_password = $waResponse->pw;
			//$numberObj->wa_identity = $waResponse->identity;
			$numberObj->wa_expiration = $waResponse->expiration;
			$numberObj->save();

			$this->line('Done. Run following command in supervisord, php artisan whatsapp:start '.$number);

		} catch(Exception $e) {

			$this->error($e->getMessage());
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
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
