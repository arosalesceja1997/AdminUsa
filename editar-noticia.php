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
	}	else if($_SESSION['user_rank'] != "Reporteros" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Coordinación") {
		header("Location: /admin");
	}

	if(isset($_GET['id'])) {
		$news_id = (int)$_GET['id'];
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getNewsInfo_Query = "SELECT titulo, categoria, contenido, autor FROM noticias WHERE id = ?";
	$getNewsInfo = $db->prepare($getNewsInfo_Query);

	$getNewsInfo->execute(array($news_id));

	if($getNewsInfo->rowCount() < 1) {
		echo '<script>window.location.href="/admin/noticias";</script>';
	}

	$row = $getNewsInfo->fetch(PDO::FETCH_OBJ);

	$title = $row->titulo;
	$categoria = $row->categoria;
	$content = $row->contenido;
	$author = $row->autor;
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Editar Noticia: <?php echo $title; ?></h5>
				<?php
					if(isset($_POST['search-news'])) {
						$newsSearched = $_POST['news'];
						echo '<script>window.location.href="/admin/noticias?noticia='.$newsSearched.'";</script>';
					}
				?>
				<form action="" method="post">
				<div class="row">
					<div class="input-field col s12 m9">
						<input type="text" name="news" id="news-search">
						<label for="news-search">Buscar noticia</label>
					</div>
					<div class="col s12 m3">
						<button type="submit" class="btn-large waves-effect waves-light full-width" name="search-news">Buscar</button>
					</div>
				</div>
				</form>
				<div class="row">
				<form action="" method="post">
					<div class="input-field col s12 m6">
						<?php
							if(isset($_POST['edit-news'])) {
								$newsTitle = $_POST['title'];
								$newsTitleError = '<p class="panel-error">Tienes que insertar un título</p>';

								checkEmptyInput($newsTitle, $newsTitleError);
							}
						?>
						<input type="text" class="active" id="title" name="title" value="<?php echo $title; ?>">
						<label for="title">Título de la Noticia</label>
					</div>
					<div class="input col s12 m6">
						<?php
							if(isset($_POST['edit-news'])) {
								$newsCategoryRetrieved = $_POST['category'];
								$newsCategoryError = '<p class="panel-error">Tienes que elegir una categoría</p>';

								checkEmptyInput($newsCategoryRetrieved, $newsCategoryError);
							}
						?>
				
						<select name="category">
								<option value="" disabled selected>Selecciona una categoría</option>
								<option value="LUFantasie">LUFantasie</option>
								<option value="Hobba">Hobba</option>
								<option value="Resumen de la semana">Resumen de la semana</option>
							</select>
					</div>
					<div class="col s12">
						<?php
							if(isset($_POST['edit-news'])) {
								$newsContent = $_POST['content'];
								$newsContentError = '<p class="panel-error">Tienes que insertar contenido</p>';

								checkEmptyInput($newsContent, $newsContentError);
							}
						?>
						<textarea id="texteditor" name="content"><?php echo $content; ?></textarea>
					</div>
					<div class="input-field col s12">
						<?php
							if(isset($_POST['edit-news'])) {
								$newsAuthor = $_POST['author'];
								$newsAuthorError = '<p class="panel-error">Tiene que haber un autor</p>';

								checkEmptyInput($newsAuthor, $newsAuthorError);
							}
						?>
						<input type="text" value="<?php echo $author; ?>" id="author" name="author">
						<label for="autor">Autor</label>
					</div>
					<div class="col s12">
						<?php
							if(!empty($newsTitle) AND !empty($newsCategoryRetrieved) AND !empty($newsContent) AND !empty($newsAuthor)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> editó la noticia ".$newsTitle."";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

								$updateNews_Query = "UPDATE noticias SET titulo = ?, categoria = ?, contenido = ?, autor = ? WHERE id = ?";
								$updateNews = $db->prepare($updateNews_Query);

								$updateNews->execute(array($newsTitle, $newsCategoryRetrieved, $newsContent, $newsAuthor, $news_id));
								echo '<script>window.location.href="/admin/editar-noticia?id='.$news_id.'";</script>';
							}
						?>
						<button type="submit" class="btn waves-effect waves-light" name="edit-news">Publicar</button>
					</div>
				</form>
				</div>
			</div>
		</div>
	</body>
</html>