<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model {

	protected $fillable = ['event_id', 'name', 'pic_url'];

}
