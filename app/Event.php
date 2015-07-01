<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {

	protected $fillable = ['event','zip','date','venue','city','state'];

}
