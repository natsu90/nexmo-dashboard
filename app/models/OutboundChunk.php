<?php
use GuzzleHttp\Client;

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

    			Queue::getIron()->addSubscriber('dnCallback', array('url' => url('queue/receive')));
            	Queue::push('dnCallback', $outbound_chunk->id);
    		}
        });
    }
}

class dnCallback {

	public function fire($job, $outbound_chunk_id)
	{
		$client = new Client();
        $outbound_chunk = OutboundChunk::find($outbound_chunk_id);
        $number = Number::where('number', $outbound_chunk->outbound->from)->first();

        if(filter_var($number->own_callback_url, FILTER_VALIDATE_URL) !== FALSE) {

            try {

                $client->post($number->own_callback_url, array(
                    'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
                    'body' => array_merge($outbound_chunk->toArray(), $outbound_chunk->outbound->toArray(), array('callback_type' => 'dn'))
                ));
            } catch(Exception $e) {

            }
        }

        $job->delete();
	}
}