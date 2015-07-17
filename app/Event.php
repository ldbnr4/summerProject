<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {

	protected $fillable = ['event','date','venue','city','state','tic_url'];

}
