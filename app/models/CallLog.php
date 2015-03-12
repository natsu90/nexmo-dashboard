<?php

class CallLog extends Eloquent {
	
	protected $table = 'call_log';

	protected $fillable = array('to', 'status', 'price', 'rate', 'duration', 'start_time', 'end_time', 'direction');
}