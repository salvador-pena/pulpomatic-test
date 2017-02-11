<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="/dev/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
			integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      <script 	src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" 
				integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
	</script>
	<script  type="text/javascript" 
				src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD2zLX_axeURKX_vjZAyKtjt-0dalOlPEM">		
	</script>
	<link rel="import" href="bower_components/polymer/polymer.html">
	<link rel="import" href="bower_components/spinner/Spinner.html">
      <link rel="import" href="bower_components/simulador/Simulador.html">
</head>
<html style="background-color: #dbe9d9">
	<mapa-simulador class = "col-sm-12 col-md-10 col-md-offset-1" 
                      style = "height:300px" 
                      sesion = "<?php echo $sesion; ?>"
                      lat = "<?php echo $lat; ?>"
                      lon = "<?php echo $lon; ?>"
                      clientes = "<?php echo $clientes; ?>"
                      autos = "<?php echo $autos; ?>"
      >
	</mapa-simulador>
</html>

