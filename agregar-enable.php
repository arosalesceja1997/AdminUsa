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
				<h5 class="section-title">Publicar enable</h5>

				<div class="row">
						<form action="" method="post">
						<?php
							if(isset($_POST['publish-news'])) {
								$title = $_POST['title'];
								$titleErr = '<p class="panel-error">Tienes que insertar un número de enable</p>';
								checkEmptyInput($title, $titleError);
							}
						?>
						<div class="input-field col s12 m12">
							<input type="text" id="title" name="title">
							<label for="title">Número de enable</label>
						</div>

						<?php
							if(isset($_POST['publish-news'])) {
								$author = $_POST['author'];
								$authorError = '<p class="panel-error">Tiene que haber una imagen</p>';
								checkEmptyInput($author, $authorError);
							}
						?>
						<div class="input-field col s12 m12">
							<input type="text" value="" id="author" name="author">
							<label for="autor">Imagen</label>
						</div>
						<?php
							if(isset($_POST['publish-news']) AND !empty($title) AND !empty($author)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> agregó un comando :enable";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	


								$publishNews_Query = "INSERT INTO enables (enable, imagen) VALUES (?, ?)";
								$publishNews = $db->prepare($publishNews_Query);

								$publishNews->execute(array($title, $author));
								if($publishNews) {
									echo '<script>window.location.href="/admin/agregar-enable";</script>';
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