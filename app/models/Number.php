<?php
use Natsu90\Nexmo\NexmoAccount;

class Number extends Eloquent {
	
	protected $table = 'number';

	protected $fillable = array(

		'number',
		'country_code',
		'type',
		'features',
		'voice_callback_type',
		'voice_callback_value',
        'own_callback_url'
	);

	public function getFeaturesAttribute()
    {
        return unserialize($this->attributes['features']);
    }

    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = serialize($value);
    }

	public static function boot()
    {
        parent::boot();

        $nexmo = new NexmoAccount(Cache::get('NEXMO_KEY', getenv('NEXMO_KEY')), Cache::get('NEXMO_SECRET', getenv('NEXMO_SECRET')));

        static::creating(function($number) use($nexmo) {
            // set mo and voice callback url
			return $nexmo->updateNumber($number->country_code, $number->number, url('/callback/mo'), array('voiceStatusCallback' => url('/callback/voice')));
        });

        static::updating(function($number) use($nexmo){
           
            return $nexmo->updateNumber($number->country_code, $number->number, url('/callback/mo'), array('voiceCallbackType' => $number->voice_callback_type, 'voiceCallbackValue' => $number->voice_callback_value));
        });

        static::deleting(function($number) use($nexmo){
            
            return $nexmo->cancelNumber($number->country_code, $number->number);
        });
    }
}