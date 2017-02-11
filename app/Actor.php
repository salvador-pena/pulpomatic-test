<?php namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use DB;

class Actor extends Model{
   protected $table = 'actores';
   public $timestamps = false;
   protected $appends = array('posicion','clienteCercano');
   
   public function getPosicionAttribute(){
      return array( 'lat' => $this->lat 
                  , 'lon' => $this->lon
                  );
   }
   
   public function getClienteCercanoAttribute(){
      if($this->tipo == 'auto'){
         $id = $this->getActorCercano('cliente');
         return Actor::find($id);
      }
      return null;
   }
   
   private function getActorCercano($pTipo){
      //DB::enableQueryLog();
      $results = DB::select( 'SELECT x.id
                                   , sqrt( pow(a.lat-x.lat,2) + pow(a.lon-x.lon,2) ) d
                                FROM actores a
                                  INNER JOIN actores x
                                    ON    x.sess = a.sess
                                      AND x.tipo = ?
                                WHERE a.id = ?
                                ORDER BY d
                                LIMIT 1
                             '
                           , [ $pTipo
                             , $this->id
                             ]
                           );
      //$log = DB::getQueryLog();
      //Log::info(__METHOD__.':'.print_r($log,true));
      if(count($results)>=1)
         return $results[0]->id;
      else
         return null;
   }
}
