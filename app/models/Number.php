<?php

class Number extends Eloquent {
	
	protected $table = 'number';

	protected $fillable = array(

		'number',
		'country_code',
		'type',
		'features',
		'voice_callback_type',
		'voice_callback_value'
	);

	public function getFeaturesAttribute()
    {
        return unserialize($this->attributes['features']);
    }

    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = serialize($value);
    }
/*
	public static function boot()
    {
        parent::boot();

        static::created(function($number) {

        	$nexmo = new NexmoAccount($nexmo_key, $nexmo_secret);
            // set mo and voice callback url
			$nexmo->updateNumber($num['country'], $num['msisdn'], url('/callback/mo'), array('voiceStatusCallback' => url('/callback/voice')));
        });
    }
 */
}