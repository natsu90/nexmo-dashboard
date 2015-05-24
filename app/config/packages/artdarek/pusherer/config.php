<?php 
$pusher = parse_url(getenv('PUSHER_URL'));

return array( 
	
	/*
	|--------------------------------------------------------------------------
	| Pusher Config
	|--------------------------------------------------------------------------
	|
	| Pusher is a simple hosted API for quickly, easily and securely adding
	| realtime bi-directional functionality via WebSockets to web and mobile 
	| apps, or any other Internet connected device.
	|
	*/

	/**
	 * App id
	 */
	'app_id' => substr($pusher["path"], 6), 

	/**
	 * App key
	 */
	'key' => $pusher['user'],

	/**
	 * App Secret
	 */
	'secret' => $pusher['pass']	

);