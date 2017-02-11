<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Simulacion extends Model{
   protected $table = 'simulaciones';
   protected $primaryKey = 'sess';
   public $timestamps = false;
   
   public function actores(){
      return $this->hasMany('App\Actor','sess','sess');
   }
}
