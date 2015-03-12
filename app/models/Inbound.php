<?php

class Inbound extends Eloquent {
	
	protected $table = 'inbound';

	protected $fillable = array('from', 'to', 'text', 'type');

	public static function boot()
    {
        parent::boot();

        static::created(function($inbound) {

            Pusherer::trigger('boom', 'add_inbound', $inbound);
        });
    }
}