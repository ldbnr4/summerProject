<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Zip extends Model {

	protected $fillable = ['zipCode'];
    
    public function clusters(){
        return $this->belongsToMany('App\Clust')->withTimestamps();
    }
    

}
