<?php
	session_start();

	require_once "../inc/connection.php";
	require_once "../inc/functions.php";
	// include "/inc/arrays.php";

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
	}	else if($_SESSION['user_rank'] != "Reporteros" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Coordinación") {
		header("Location: /admin");
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Crea un nuevo logro</h5>

				<div class="row">
						<form action="" method="post">
						<?php
							if(isset($_POST['publish-news'])) {
								$code = $_POST['code'];
								$codeError = '<p class="panel-error">Tienes que insertar un código para este <b>logro</b></p>';
								checkEmptyInput($code, $codeError);
							}
						?>
						<div class="input-field col s12 m12">
							<input type="text" id="title" name="code">
							<label for="title">Código para el logro</label>
						</div>

						<?php
							if(isset($_POST['publish-news'])) {
								$title = $_POST['title'];
								$titleError = '<p class="panel-error">Tienes que insertar un título para este logro</p>';
								checkEmptyInput($title, $titleError);
							}
						?>
						<div class="input-field col s12 m12">
							<input type="text" value="" id="author" name="title">
							<label for="autor">Título del logro</label>
						</div>
						
						<?php
							if(isset($_POST['publish-news'])) {
								$desc = $_POST['desc'];
								$descError = '<p class="panel-error">Tienes que insertar una descripción para este logro</p>';
								checkEmptyInput($desc, $descError);
							}
						?>
						<div class="input-field col s12 m12">
							<input type="text" value="" id="author" name="desc">
							<label for="autor">Descripción del logro</label>
						</div>

						<?php
							if(isset($_POST['publish-news'])) {
								$imagen = $_POST['imagen'];
								$imagenError = '<p class="panel-error">Tienes que insertar una imagen para este logro</p>';
								checkEmptyInput($imagen, $imagenError);
							}
						?>
						<div class="input-field col s12 m12">
							<input type="text" value="" id="author" name="imagen">
							<label for="autor">Imagen del logro</label>
						</div>

						<?php
							if(isset($_POST['publish-news']) AND !empty($code) AND !empty($title) AND !empty($desc) AND !empty($imagen)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> agregó el logro ".$code."";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

								$publishNews_Query = "INSERT INTO habbo_placas (codigo, titulo, descripcion, imagen) VALUES (?, ?, ?, ?)";
								$publishNews = $db->prepare($publishNews_Query);

								$publishNews->execute(array($code, $title, $desc, $imagen));
								if($publishNews) {
									echo 'Se ha añadido el logro <b>'.$code.'</b>';
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