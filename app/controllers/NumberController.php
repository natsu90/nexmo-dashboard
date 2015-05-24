<?php
use Natsu90\Nexmo\NexmoAccount;

class NumberController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Number::all();
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $nexmo = new NexmoAccount(Cache::get('NEXMO_KEY', getenv('NEXMO_KEY')), Cache::get('NEXMO_SECRET', getenv('NEXMO_SECRET')));

        $isBought = $nexmo->buyNumber(Input::get('country_code'), Input::get('number'));

        if($isBought) {

        	Pusherer::trigger('boom', 'update_balance', $nexmo->getBalance());

        	$number = new Number;
        	$number->number = Input::get('number');
        	$number->country_code = Input::get('country_code');
        	$number->type = Input::get('type');
        	$number->features = explode(',', Input::get('features'));
        	$number->save();

        	return $number;
        }

        return $this->response->errorInternal();
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Number::where('number', $id)->first();
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$number = Number::where('number', $id)->first();
		$number->voice_callback_type = Input::get('voice_callback_type');
		$number->voice_callback_value = Input::get('voice_callback_value');
		$number->own_callback_url = Input::get('own_callback_url');

		if($number->save())
			return array();
		return $this->response->errorInternal();
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$number = Number::where('number', $id)->first();
		
		if($number->delete())
			return array();
		return $this->response->errorInternal();
	}

	public function getSearch($country_code)
	{
        $nexmo = new NexmoAccount(Cache::get('NEXMO_KEY', getenv('NEXMO_KEY')), Cache::get('NEXMO_SECRET', getenv('NEXMO_SECRET')));

		return $nexmo->getAvailableInboundNumbers($country_code, array('size' => 100));
	}

	public function getCalls()
	{
		return array('numbers' => CallLog::all());
	}
}
