<?php

class CallbackUrlController extends \BaseController {

	public function saveInboundMessage()
	{
		$inbound = new Inbound;
		$inbound->from = Input::get('msisdn');
		$inbound->to = Input::get('to');
		$inbound->text = Input::get('text');
		$inbound->type = Input::get('type');

		return $inbound->save();
	}

	public function updateDeliveryReceipt()
	{
		$outbound_chunk = OutboundChunk::where('message_id','=',Input::get('messageId'))->first();
		$outbound_chunk->dn_status = Input::get('status');
		$outbound_chunk->dn_error_code = Input::get('err-code');

		return $outbound_chunk->save();
	}

	public function saveVoiceCallStatus()
	{
		return true;
	}

}