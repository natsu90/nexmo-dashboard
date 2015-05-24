<?php

class WhatsAppContact extends Eloquent {
	
	protected $table = 'wa_contact';

	protected $fillable = array(

		'number',
		'status',
		'last_seen',
		'number_id'
	);

	public function setLastSeenAttribute($value)
	{
        $this->attributes['last_seen'] = DateTime::createFromFormat('U', $value)->format('Y-m-d H:i:s');
	}

	public function number()
	{
		return $this->belongsTo('Number');
	}
}