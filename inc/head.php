<!DOCTYPE html>
<html lang="es">
	<head>
		<title>LUFantasie - Panel de Administración</title>
		<!-- Meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<!-- Link Tags -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="css/materialize.min.css">
		<link rel="stylesheet" href="css/admin_estilos.css">
		<link rel="stylesheet" href="css/style.css">
		<!-- Script Tags -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="js/materialize.js"></script>
		<script src="js/ckeditor.js"></script>
		<script src="js/config.js"></script>
		<script src="js/general.js"></script>
		<script>
			$(document).ready(function() {
				$(".dropdown-button").dropdown();
				$(".datepicker").pickadate({
					selectMonths: true,
					selectYears: 15,

					// The title label to use for the month nav buttons
			        labelMonthNext: 'Próximo mes',
			        labelMonthPrev: 'Mes anterior',

			        // The title label to use for the dropdown selectors
			        labelMonthSelect: 'Selecciona un mes',
			        labelYearSelect: 'Selecciona un año',

			        // Months and weekdays
			        monthsFull: [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ],
			        monthsShort: [ 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic' ],
			        weekdaysFull: [ 'Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado' ],
			        weekdaysShort: [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ],

			        // Materialize modified
			        weekdaysLetter: [ 'S', 'M', 'T', 'W', 'T', 'F', 'S' ],

			        // Today and clear
			        today: 'Hoy',
			        clear: 'Limpiar',
			        close: 'Cerrar',

				});
				$(".openNav").sideNav();
				$('select').material_select();
				CKEDITOR.replace( 'content', {
					language: 'es',
					disallowedContent : 'img{width,height}'
				});
				$('ul.tabs').tabs();
				$('.collapsible').collapsible({
					accordion: true
				});
				$('select').material_select();
				
			});
		</script>
	</head>
	<body>