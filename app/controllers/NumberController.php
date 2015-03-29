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
		/*
		$nexmo = new NexmoAccount(getenv('NEXMO_KEY'), getenv('NEXMO_SECRET'));
		return $nexmo->getInboundNumbers();
		*/
		return Number::all();
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
		return $number->save();
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
