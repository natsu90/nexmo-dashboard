<?php
use GuzzleHttp\Client;

class Inbound extends Eloquent {
	
	protected $table = 'inbound';

	protected $fillable = array('from', 'to', 'text', 'type');

	public static function boot()
    {
        parent::boot();

        static::created(function($inbound) {

            Pusherer::trigger('boom', 'add_inbound', $inbound);

            Queue::getIron()->addSubscriber('moCallback', array('url' => url('queue/receive')));
            Queue::push('Inbound@moCallback', $inbound->id, 'moCallback');
        });
    }

    public function moCallback($job, $inbound_id)
    {
        $client = new Client();
        $inbound = Inbound::find($inbound_id);
        $number = Number::where('number', $inbound->to)->first();

        if(filter_var($number->own_callback_url, FILTER_VALIDATE_URL) !== FALSE) {

            $success = true;
            try {

                $client->post($number->own_callback_url, array(
                    'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
                    'body' => array_merge($inbound->toArray(), array('callback_type' => 'mo'))
                ));
            } catch(Exception $e) {
                $success = false;
            }

            if(!$success && $job->attempts() < 7)
                $job->release(10);
        }

        $job->delete();
    }
}