<?php

Route::get('/', 'Simulador@getIndex');
Route::get( '/api/simulacion/{sess}', 'Simulador@getSimulacion' );
Route::get( '/api/simulacion/{sess}/autos/{no}', 'Simulador@setAutos' );
Route::get( '/api/simulacion/{sess}/clientes/{no}', 'Simulador@setClientes' );