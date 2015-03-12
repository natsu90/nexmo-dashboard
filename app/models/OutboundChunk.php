<?php

class OutboundChunk extends Eloquent {
	
	protected $table = 'outbound_chunk';

	protected $fillable = array(

		'outbound_id', // client-ref
		'message_id', // messageId
		'status_code', // status
		'price', // message-price
		'dn_status',
		'dn_error_code',
	);

	public $status_string = array(
		'Success',
		'Throttled',
		'Missing params',
		'Invalid params',
		'Invalid credentials',
		'Internal error',
		'Invalid message',
		'Number barred',
		'Partner account barred',
		'Partner quota exceeded',
		'Unknown',
		'Account not enabled for REST',
		'Message too long',
		'Communication Failed',
		'Invalid Signature',
		'Invalid sender address',
		'Invalid TTL',
		'Unknown',
		'Unknown',
		'Facility not allowed',
		'Invalid Message class'
	);

	public function outbound()
	{
		return $this->belongsTo('Outbound');
	}

	public static function boot()
    {
        parent::boot();

        static::created(function($outbound_chunk) {

        	$outbound_id = $outbound_chunk->outbound->id;
    		// update status outbound
    		if($outbound_chunk->status_code > 0 && isset($status_string[(int) $outbound_chunk->status_code])) {
    			$outbound = Outbound::find($outbound_id);
    			$outbound->status = strtolower($status_string[(int) $outbound_chunk->status_code]);
    			$outbound->save();
    		}
        });

        static::updated(function($outbound_chunk) {

        	$outbound_id = $outbound_chunk->outbound->id;
    		// update status outbound
    		if($outbound_chunk->dn_error_code >= 0) {
    			$outbound = Outbound::find($outbound_id);
    			$outbound->status = strtolower($outbound_chunk->dn_status);
    			$outbound->save();
    		}
        });
    }
}