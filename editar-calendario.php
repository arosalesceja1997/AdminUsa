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

	$getNewsInfo_Query = "SELECT * FROM calendario WHERE id = ?";
	$getNewsInfo = $db->prepare($getNewsInfo_Query);

	$getNewsInfo->execute(array($news_id));

	if($getNewsInfo->rowCount() < 1) {
		echo '<script>window.location.href="/admin/calendario";</script>';
	}

	$row = $getNewsInfo->fetch(PDO::FETCH_OBJ);

	$dia = $row->numero_dia;
	$content = $row->contenido;
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Editar día: <?php echo $dia; ?></h5>
				<?php
					if(isset($_POST['search-news'])) {
						$newsSearched = $_POST['news'];
						echo '<script>window.location.href="/admin/noticias?noticia='.$newsSearched.'";</script>';
					}
				?>
				<div class="row">
				<form action="" method="post">
					<div class="input-field col s12 m6">
						<?php
							if(isset($_POST['edit-news'])) {
								$newsTitle = $_POST['title'];
								$newsTitleError = '<p class="panel-error">Tienes que insertar un día del mes</p>';

								checkEmptyInput($newsTitle, $newsTitleError);
							}
						?>
						<input type="text" class="active" id="title" name="title" value="<?php echo $dia; ?>">
						<label for="title">Día del mes</label>
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
					<div class="col s12">
						<?php
							if(!empty($newsTitle) AND !empty($newsContent)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> editó el día ".$title." al calendario";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

								$updateNews_Query = "UPDATE calendario SET numero_dia = ?, contenido = ? WHERE id = ?";
								$updateNews = $db->prepare($updateNews_Query);

								$updateNews->execute(array($newsTitle, $newsContent, $news_id));
								echo '<script>window.location.href="/admin/calendario";</script>';
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