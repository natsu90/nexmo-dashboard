<?php

class InboundController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Inbound::orderBy('created_at', 'desc')->get();
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Inbound::findOrFail($id);
	}

	public function postReply()
	{
		$inbound = Inbound::findOrFail(Input::get('inbound_id'));

		$text = trim(Input::get('text'));
		if($text == "")
			return array('error' => 'Text is empty');
		
		$outbound = new Outbound;
		$outbound->from = $inbound->to;
		$outbound->to = $inbound->from;
		$outbound->text = Input::get('text');
		$outbound->type = $inbound->type;
		$outbound->save();

		return ['status' => 'success'];
	}
}
