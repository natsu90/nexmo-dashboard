<?php

class CallLog extends Eloquent {
	
	protected $table = 'call_logs';

	protected $fillable = array('call_id', 'to', 'status', 'price', 'rate', 'duration', 'start_time', 'end_time', 'direction');
}