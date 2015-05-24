<?php

class OutboundController extends \BaseController {

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$text = trim(Input::get('text'));
		if($text == "")
			return array('error' => 'Text is empty');

		$to = Input::get('to');
		$senders = preg_split('/[,;\\\n\\/]/', $to);

		$ids = array();
		foreach($senders as $sender)
		{
			if(trim($sender) == "")
				continue;

			$outbound = new Outbound;
			$outbound->from = Input::get('from');
			$outbound->to = str_replace(array('+', '-', ' ', '(', ')'), '', $sender);
			$outbound->text = $text;
			$outbound->save();
			$ids[] = $outbound->id;
		}

		return Outbound::whereIn('id', $ids)->get();
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Outbound::find($id);
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Outbound::orderBy('created_at', 'desc')->get();
	}

	public function postReplyWhatsapp()
	{
		$inbound = Inbound::findOrFail(Input::get('inbound_id'));

		$text = trim(Input::get('text'));
		if($text == "")
			return array('error' => 'Text is empty');
		
		$outbound = new Outbound;
		$outbound->from = $inbound->to;
		$outbound->to = $inbound->from;
		$outbound->text = Input::get('text');
		$outbound->type = 'whatsapp';
		$outbound->save();
	}
}
