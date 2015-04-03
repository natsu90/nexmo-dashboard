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
            Queue::push('moCallback', $inbound->id);
        });
    }
}