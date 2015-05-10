<?php
use Natsu90\Nexmo\NexmoAccount;

class Outbound extends Eloquent {
	
	protected $table = 'outbound';

	protected $fillable = array('from', 'to', 'text', 'status', 'type');

    /*
    protected $appends = array('status');

    public function getStatusAttribute()
    {
        return $this->attributes['status'];
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;
    }
    */

	public function outbound_chunks()
	{
		return $this->hasMany('OutboundChunk');
	}

    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    public function scopeWhatsapp($query, $number)
    {
        return $query->where('type', 'whatsapp')->where('from', $number);
    }

	public static function boot()
    {
        parent::boot();

        static::created(function($outbound) {

            $outbound = Outbound::find($outbound->id);
            if($outbound->type != 'whatsapp') {

                Queue::getIron()->addSubscriber('sendMessage', array('url' => url('queue/receive')));
                Queue::push('sendMessage', $outbound->id);
            }

            Pusherer::trigger('boom', 'add_outbound', $outbound);
        });

        static::updated(function($outbound) {
            
            Pusherer::trigger('boom', 'update_outbound', $outbound);
        });
    }
}

class sendMessage {

	public function fire($job, $outbound_id)
    {
    	$outbound = Outbound::findOrFail($outbound_id);

    	$nexmo = new NexmoAccount(Cache::get('NEXMO_KEY', getenv('NEXMO_KEY')), Cache::get('NEXMO_SECRET', getenv('NEXMO_SECRET')));

    	$response = $nexmo->sendMessage($outbound->from, $outbound->to, $outbound->text, array('status-report-req' => 1, 'client-ref' => $outbound_id));

        $isSent = true;
    	if($response['message-count'] > 0) {

    		foreach($response['messages'] as $message)
    		{
    			$outbound_chunk = new OutboundChunk;
    			$outbound_chunk->outbound_id = $outbound_id;
    			$outbound_chunk->message_id = $message['message-id'];
    			$outbound_chunk->status_code = $message['status'];
    			$outbound_chunk->price = $message['message-price'];
    			$outbound_chunk->save();
    			// update balance
    			Pusherer::trigger('boom', 'update_balance', $message['remaining-balance']);

                if($message['status'] > 0)
                    $isSent = false;
    		}
    	}
    	// update status outbound
        if($isSent) {
            $outbound->status = 'sent';
            $outbound->save();
        }
    	
        $job->delete();
    }
}