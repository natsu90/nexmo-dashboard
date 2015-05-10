<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WhatsAppStart extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'whatsapp:start';

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
		if(!$this->argument('number'))
			return $this->error('No number specified.');

		$number = Number::where('number', $this->argument('number'))->first();

		if(is_null($number) || is_null($number->wa_password) || is_null($number->wa_expiration))
			return $this->error('Whatsapp not registered. Run php artisan whatsapp:register');

		$contacts = $number->contacts->map(function($contact) {
			return $contact->number;
		});

		$wa = new WhatsProt($number->number, $number->number, true);

		$wa->connect(); // Connects to WhatsApp
		$wa->loginWithPassword($number->wa_password); // Login
		$wa->pollMessage();
		$wa->sendGetPrivacyBlockedList(); // Get our privacy list
		$wa->sendGetClientConfig(); // Get client config
		$wa->sendGetServerProperties(); // Get server properties

		if(!$contacts->isEmpty())
			$wa->sendGetHasVoipEnabled($contacts); // Get which users have voip enabled

		$wa->sendGetGroups(); // Get groups (participating)
		$wa->sendGetBroadcastLists(); // Get broadcasts lists
		// $wa->sendGetProfilePicture(self); // self preview profile picture [OPTIONAL]

		if(!$contacts->isEmpty())
			$wa->sendSync($contacts); // Sync all contacts

		// $wa->sendGetStatuses(All contacts); // Get contacts status [OPTIONAL]

		/*
		for (All contacts) [OPTIONAL]
		{
  			$wa->sendGetProfilePicture(contact); // preview profile picture of every contact
		}
		*/

		// $wa->sendPing(); // Keep alive

		$wa->eventManager()->bind("onGetMessage", function($mynumber, $from, $id, $type, $time, $name, $body) {

			// todo // save message id and compare to avoid duplicate
			$inbound = new Inbound;
			$inbound->from = $this->getFrom($from);
			$inbound->to = $mynumber;
			$inbound->text = $body;
			$inbound->type = 'whatsapp';
			$inbound->save();
		});

		$wa->eventManager()->bind("onPresenceAvailable", function($mynumber, $from) {

			$from = $this->getFrom($from);
			// todo // add or update wa-contact
		});

		$wa->eventManager()->bind("onPresenceUnavailable", function($mynumber, $from, $last) {

			$from = $this->getFrom($from);
			// todo // add or update wa-contact
		});

		$wa->eventManager()->bind("onMessageReceivedClient", function($mynumber, $from, $id) {

			$outbound_chunk = OutboundChunk::where('message_id', $id)->first();

			if(!$outbound_chunk)
				return;

			$outbound_chunk->dn_error_code = 0;
			$outbound_chunk->dn_status = 'delivered';
			$outbound_chunk->save();
		});

		$time = time();
		while(true) {

			sleep(1);
			$wa->pollMessage();
			$this->processMessages($wa);

			if(time() - $time >= 8) {

				$wa->sendActiveStatus();
				$time = time();
				// whatsapp command action
				$whatsAppAction = Cache::get('whatsAppAction_'.$number->number, false);
				if($whatsAppAction) {

					$whatsAppActionInput = Cache::get('whatsAppActionInput_'.$number->number, false);
					Cache::forget('whatsAppAction_'.$number->number);
					Cache::forget('whatsAppActionInput_'.$number->number);

					switch(strtolower($whatsAppAction))
					{
						case 'updatestatus':
							$wa->sendStatusUpdate($whatsAppActionInput);
							break;

						case 'setprofilepicture':
							try {

								$wa->sendSetProfilePicture($whatsAppActionInput);
							} catch (Exception $e) {
							}
							break;

						case 'stop':
							$wa->disconnect();
							exit('whatsapp is stopped');
					}
				}
				// end whatsapp command action
			}
		}  
	}

	// send all outbound messages
	protected function processMessages($wa)
	{
		$outbounds = Outbound::whatsapp($this->argument('number'))->queued()->get();
		foreach($outbounds as $outbound)
		{
			$to = $outbound->to;
			// todo // add or update wa-contact
			// $wa->sendPresenceSubscription($to);
			$wa->sendMessageComposing($to);
			$wa->sendMessagePaused($to);
			$message_id = $wa->sendMessage($to, $outbound->text);

			$outbound_chunk = new OutboundChunk;
			$outbound_chunk->outbound_id = $outbound->id;
			$outbound_chunk->message_id = $message_id;
			$outbound_chunk->status_code = 0;
			$outbound_chunk->price = 0.00;
			$outbound_chunk->save();

			$outbound->status = 'sent';
			$outbound->save();

			$wa->pollMessage();
		}
	}

	public function getFrom($from)
	{
		return str_replace(array("@s.whatsapp.net","@g.us"), "", $from);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('number', InputArgument::OPTIONAL, 'Phone number.', getenv('WHATSAPP_NUMBER')),
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
