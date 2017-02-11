<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Simulacion;
use App\Actor;


class Simulador extends Controller {
   
   private $colores = ['#A239CA','#3CC47C','#0375B4','#E37222','#FFCE00'];
   public function getIndex(){
      Log::info(__METHOD__);

      $sess = uniqid();
      $latDefault = 19.417244;
      $lonDefault = -99.171586;
      $noAutosDefault = 2;
      $noClientesDefault = 4;
      
      $sim = new Simulacion();
      $sim->sess = $sess;
      $sim->noAutos = $noAutosDefault;
      $sim->noClientes = $noClientesDefault;
      $sim->lat = $latDefault;
      $sim->lon = $lonDefault;
      $sim->fgActivo = 0;
      $sim->save();
      
      $sim = Simulacion::find($sess);
      $this->addActores( $sess, $sim->lat, $sim->lon, 'auto', $sim->noAutos);
      $this->addActores( $sess, $sim->lat, $sim->lon, 'cliente', $sim->noClientes);

      return view( 'index', [ 'sesion' => $sess
                            , 'lat' => $latDefault
                            , 'lon' => $lonDefault
                            , 'clientes' => $noClientesDefault
                            , 'autos' => $noAutosDefault
                            ]
                 );
   }
   
   public function getAdmin(){
      Log::info(__METHOD__);
      return 'Panel de administracion';
   }
   
   public function getSimulacion($sess){
      Log::info(__METHOD__.$sess);
      $this->mueveActores($sess);
      return Simulacion::find($sess)->actores->toArray();
   }
   
   public function setClientes($sess,$no){
      Log::info(__METHOD__.' '.$sess.':'.$no);
      if($sess !== 'undefined'){
         $sim = Simulacion::find($sess);
         if( $sim->noClientes < $no )
            $this->addActores( $sess, $sim->lat, $sim->lon, 'cliente', $no-$sim->noClientes );
         else if( $sim->noClientes > $no )
            $this->delActores( $sess, $sim->noClientes-$no, 'cliente' );
         $sim->noClientes = $no;
         $sim->save();
      }
      return 'OK';
   }
   
   public function setAutos($sess,$no){
      Log::info(__METHOD__.' '.$sess.':'.$no);
      if($sess !== 'undefined'){
         $sim = Simulacion::find($sess);
         Log::info(__METHOD__.' '.$no.'-'.$sim->noAutos.'='.($no-$sim->noAutos));
         if( $sim->noAutos < $no )
            $this->addActores( $sess, $sim->lat, $sim->lon, 'auto', $no-$sim->noAutos );
         else if( $sim->noAutos > $no )
            $this->delActores( $sess, $sim->noAutos-$no, 'auto' );
         $sim->noAutos = $no;
         $sim->save();
      }
      return 'OK';
   }
   
   private function addActores( $sess
                              , $lat
                              , $lon
                              , $tipo
                              , $cantidad
                              ){
      Log::info(__METHOD__.' adding '.$cantidad.' '.$tipo);
      for( $n =1; $n<=$cantidad; $n++ ){
         $actor = new Actor();
         $actor->sess = $sess;
         $actor->tipo = $tipo;
         $actor->color = $this->colores[mt_rand(0,count($this->colores)-1)];
         $signo = mt_rand(0,1) == 0 ? 1 : -1;
         $actor->lat = $lat + $signo*mt_rand(0,500)/100000;
         $signo = mt_rand(0,1) == 0 ? 1 : -1;
         $actor->lon = $lon + $signo*mt_rand(0,1000)/100000;
         $actor->aceleracion = mt_rand(0,20)/100000;
         $signo = mt_rand(0,1) == 0 ? 1 : -1;
         $actor->latRumbo = $actor->lat + $signo*mt_rand(0,500)/100000;
         $signo = mt_rand(0,1) == 0 ? 1 : -1;
         $actor->lonRumbo = $actor->lon + $signo*mt_rand(0,1000)/100000;
         $actor->save();
      }
   }
   
   private function mueveActores($sess){
      Log::info(__METHOD__);
      
      foreach( Simulacion::find($sess)->actores as $actor){
         Log::info('  '.$actor->id.':'.$actor->lat.','.$actor->lon);
         $lat = $actor->lat;
         $limite = 0;
         while( !$this->between( $lat, $actor->lat, $actor->latRumbo ) ){
            $signo = mt_rand(0,1) == 0 ? 1 : -1;
            $lat = $actor->lat + $signo*$actor->aceleracion;
            if($limite++ == 10){
               // Si no hallas el rumbo mejor quedate
               $lat = $actor->lat;
               $actor->aceleracion = mt_rand(1,20)/100000;
               break;
            }
         }
         $lon = $actor->lon;
         $limite = 0;
         while( !$this->between( $lon, $actor->lon, $actor->lonRumbo ) ){
            $signo = mt_rand(0,1) == 0 ? 1 : -1;
            $lon = $actor->lon + $signo*$actor->aceleracion;
            if($limite++ == 10){
               // Si no hallas el rumbo mejor quedate
               $lon = $actor->lon;
               $actor->aceleracion = mt_rand(1,20)/100000;
               break;
            }
         }
         $actor->lat = $lat;
         $actor->lon = $lon;
         // Si esta cerca del objectivo establece un nuevo rumbo
         if(   $this->between($actor->latRumbo, $actor->lat-$actor->aceleracion, $actor->lat+$actor->aceleracion)
            && $this->between($actor->lonRumbo, $actor->lon-$actor->aceleracion, $actor->lon+$actor->aceleracion)
           ){
            $signo = mt_rand(0,1) == 0 ? 1 : -1;
            $actor->latRumbo = $actor->lat + $signo*mt_rand(0,500)/100000;
            $signo = mt_rand(0,1) == 0 ? 1 : -1;
            $actor->lonRumbo = $actor->lon + $signo*mt_rand(0,1000)/100000;
            Log::debug('    Rumbo:'.$actor->id.':'.$actor->latRumbo.','.$actor->lonRumbo);
         }
         $actor->save();
      }
      
   }
   
   private function between($value, $min, $max){
      if(   ( $value > $min && $value <= $max )
         || ( $value < $min && $value >= $max )
        )
         return true;
      return false;
   }
   
   private function delActores( $sess, $no, $tipo ){
      Log::info(__METHOD__);
      $result = DB::delete( 'DELETE FROM actores
                               WHERE sess = ? 
                                 AND tipo = ? 
                               LIMIT ?'
                          , [ $sess
                            , $tipo
                            , $no
                            ]
                          );
   }
   
}

      

