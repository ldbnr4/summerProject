<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Clust extends Model {

	public function zip_codes(){
        return $this->belongsToMany('App\Zip')->withTimestamps();
    }

}
