<?php
	session_start();

	require_once "../inc/connection.php";
	require_once "../inc/functions.php";
	include "/inc/arrays.php";

	if(isset($_SESSION['user_id'])) {
		$checkUserBanned_Query = "SELECT baneado FROM usuarios WHERE id = ?";
		$checkUserBanned = $db->prepare($checkUserBanned_Query);

		$checkUserBanned->execute(array($_SESSION['user_id']));
		$bannedColumn = $checkUserBanned->fetch(PDO::FETCH_OBJ);

		$banned = $bannedColumn->baneado;
		if($banned == 1) {
			echo '<script>window.location.href="/logout?logout";</script>';
		}
	}
	if(!isset($_SESSION['user_id']) OR $_SESSION['userAllowed'] != true) {
		header("Location: /");
	}	else if($_SESSION['user_rank'] != "Equipo de Información" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Coordinación") {
		header("Location: /admin");
	}

	if(date("F") == "January") {
		$translatedMonth = "Enero";
	}	else if(date("F") == "February") {
		$translatedMonth = "Febrero";
	}	else if(date("F") == "March") {
		$translatedMonth = "Marzo";
	}	else if(date("F") == "April") {
		$translatedMonth = "Abril";
	}	else if(date("F") == "May") {
		$translatedMonth = "Mayo";
	}	else if(date("F") == "June") {
		$translatedMonth = "Junio";
	}	else if(date("F") == "July") {
		$translatedMonth = "Julio";
	}	else if(date("F") == "August") {
		$translatedMonth = "Agosto";
	}	else if(date("F") == "September") {
		$translatedMonth = "Septiembre";
	}	else if(date("F") == "October") {
		$translatedMonth = "Octubre";
	}	else if(date("F") == "November") {
		$translatedMonth = "Noviembre";
	}	else if(date("F") == "December") {
		$translatedMonth = "Diciembre";
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Publicar día de calendario</h5>

				<div class="row">
						<form action="" method="post">
						<?php
							if(isset($_POST['publish-news'])) {
								$title = $_POST['title'];
								$titleError = '<p class="panel-error">Tienes que insertar un número de día</p>';
								checkEmptyInput($title, $titleError);
							}
						?>
						<div class="input-field col s12 m6">
							<input type="text" id="title" name="title">
							<label for="title">Día Calendario</label>
						</div>
						<?php
							if(isset($_POST['publish-news'])) {
								$content = $_POST['content'];
								$contentError = '<p class="panel-error">Tienes que insertar contenido</p>';
								checkEmptyInput($content, $contentError);
							}
						?>
						<div class="col s12">
							<textarea id="texteditor" name="content"></textarea>
						</div>


						<?php
							if(isset($_POST['publish-news']) AND !empty($title) AND !empty($content)) {

								$publishNews_Query = "INSERT INTO calendario (numero_dia, contenido) VALUES (?, ?)";
								$publishNews = $db->prepare($publishNews_Query);

								$publishNews->execute(array($title, $content));
								if($publishNews) {
									echo '<script>window.location.href="/admin/calendario";</script>';
								}
							}
						?>
						<div class="col s12">
							<button type="submit" class="btn waves-effect waves-light" name="publish-news">Publicar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>