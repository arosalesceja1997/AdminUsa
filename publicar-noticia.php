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
				<h5 class="section-title">Publicar Noticia</h5>
					<a href="banners" class="btn waves-effect waves-light" target="_blank">Ver banners</a>
				<div class="row">
						<form action="" method="post">
						<?php
							if(isset($_POST['publish-news'])) {
								$title = $_POST['title'];
								$titleError = '<p class="panel-error">Tienes que insertar un título</p>';
								checkEmptyInput($title, $titleError);
							}
						?>
						<div class="input-field col s12 m6">
							<input type="text" id="title" name="title">
							<label for="title">Título de la Noticia</label>
						</div>
						<?php
							if(isset($_POST['publish-news'])) {
								$category = $_POST['category'];
								$categoryError = '<p class="panel-error">Tienes que elegir una categoría</p>';
								checkEmptyInput($category, $categoryError);
							}
						?>
						<div class="input col s12 m6">
							<select name="category">
								<option value="" disabled selected>Selecciona una categoría</option>
								<option value="LUFantasie">LUFantasie</option>
								<option value="Vip">Usuarios VIP</option>
								<option value="Hobba">Hobba</option>
								<option value="Resumen de la semana">Resumen de la semana</option>
								<option value="Hobba Awards">Hobba Awards 2020</option>
							</select>
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
							if(isset($_POST['publish-news'])) {
								$author = $_POST['author'];
								$authorError = '<p class="panel-error">Tiene que haber un autor</p>';
								checkEmptyInput($author, $authorError);
							}
						?>
						<div class="input-field col s12">
							<input type="text" value="<?php echo $_SESSION['username'] ?>" id="author" name="author">
							<label for="autor">Autor</label>
						</div>
						<?php
							if(isset($_POST['publish-news']) AND !empty($title) AND !empty($category) AND !empty($content) AND !empty($author)) {

								$publishNews_Query = "INSERT INTO noticias (titulo, categoria, contenido, publicado, autor) VALUES (?, ?, ?, ?, ?)";
								$publishNews = $db->prepare($publishNews_Query);

								$publishNews->execute(array($title, $category, $content, date("j")." de ".$translatedMonth." del ".date("Y"), $author));
								if($publishNews) {
									echo '<script>window.location.href="/admin/publicar-noticia";</script>';
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