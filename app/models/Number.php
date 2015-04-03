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

        static::created(function($number) use($nexmo) {

            Pusherer::trigger('boom', 'add_number', $number);
            // set mo and voice callback url
            Queue::getIron()->addSubscriber('setupNumberCallbackUrl', array('url' => url('queue/receive')));
            Queue::push('setupNumberCallbackUrl', array('nexmo_key' => $nexmo->nexmo_key, 'nexmo_secret' => $nexmo->nexmo_secret, 'country_code' => $number->country_code, 'number' => $number->number));
        });

        static::updating(function($number) use($nexmo){
           
            return $nexmo->updateNumber($number->country_code, $number->number, url('callback/mo'), array('voiceCallbackType' => $number->voice_callback_type, 'voiceCallbackValue' => $number->voice_callback_value, 'voiceStatusCallback' => url('callback/voice')));
        });

        static::deleting(function($number) use($nexmo){
            
            return $nexmo->cancelNumber($number->country_code, $number->number);
        });

        static::deleted(function($number) {
            
            Pusherer::trigger('boom', 'remove_number', $number);
        });
    }
}

class setupNumberCallbackUrl {

    public function fire($job, $data)
    {
        $nexmo = new NexmoAccount($data['nexmo_key'], $data['nexmo_secret']);
        $nexmo->updateNumber($data['country_code'], $data['number'], url('callback/mo'), array('voiceStatusCallback' => url('callback/voice')));

        $job->delete();
    }
}