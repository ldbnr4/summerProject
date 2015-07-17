<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class EventArtist extends Model {

	protected $fillable = ['event_id', 'artist_id', 'date'];

}
