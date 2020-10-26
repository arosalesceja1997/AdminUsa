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
	}	else if($_SESSION['user_rank'] != "Reporteros" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Equipo de Redes Sociales") {
		header("Location: /admin");
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getUsersInfo_Query = "SELECT nombre FROM usuarios WHERE id = ?";
	$getUsersInfo = $db->prepare($getUsersInfo_Query);

	$getUsersInfo->execute(array($_SESSION['user_id']));

	$usersRow = $getUsersInfo->fetch(PDO::FETCH_OBJ);
	$username = $usersRow->nombre;

	if(isset($_POST['post-event'])) {
		$eventPublisher = $_POST['publisher'];
		$eventPublisherError = '<p class="panel-error">Tienes que insertar el publicador</p>';
	}
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Añadir Evento</h5>
					<form action="" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['post-event'])) {
									$eventTitle = $_POST['title'];
									$eventTitleError = '<p class="panel-error">Tienes que insertar un título</p>';

									checkEmptyInput($eventTitle, $eventTitleError);
								}
							?>
							<input type="text" id="title" name="title" value="<?php if(isset($eventTitle)) { echo $eventTitle; } ?>">
							<label for="title" class="<?php if(isset($eventTitle)) { echo "active"; } ?>">Título del Evento</label>
						</div>
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['post-event'])) {
									$eventDate = $_POST['date'];
									$eventDateError = '<p class="panel-error">Tienes que elegir un día para el evento</p>';

									checkEmptyInput($eventDate, $eventDateError);
								}
							?>
							<input type="date" class="datepicker" id="date" name="date" value="<?php if(isset($eventDate)) { echo $eventDate; } ?>">
							<label for="date" class="<?php if(isset($eventDate)) { echo "active"; } ?>">Día</label>
						</div>
						<div class="col s12">
							<?php
								if(isset($_POST['post-event'])) {
									$content = $_POST['content'];
									$contentError = '<p class="panel-error">Tienes que insertar contenido</p>';
									checkEmptyInput($content, $contentError);
								}
							?>
							<textarea id="texteditor" name="content"></textarea>
						</div>
						<div class="file-field input-field col s12">
							<p class="panel-warning">Tamaño recomendado: 320x90</p>
							<?php
								if(isset($_POST['post-event'])) {
									if(isset($_FILES['event-img'])) {
										if(empty($_FILES['event-img']['name'])) {
											echo '<p class="panel-error">Tienes que subir una imagen para el evento</p>';
										}	else {
											$allowedFormat = array(
												'jpg',
												'jpeg',
												'gif',
												'png'
											);

											$file_name = $_FILES['event-img']['name'];
											$file_extn = strtolower(end(explode('.', $file_name)));
											$file_loc = $_FILES['event-img']['tmp_name'];

											if(in_array($file_extn, $allowedFormat) AND !empty($eventTitle) AND !empty($eventDate) AND !empty($eventPublisher) AND !empty($content)) {
												$file_path = '../imagenes/event-img/'.substr(md5(time()), 0, 10).'.'.$file_extn;
												move_uploaded_file($file_loc, $file_path);
											}	else if(!in_array($file_extn, $allowedFormat)) {
												echo '<p class="panel-error">Sólo puedes subir una imagen en uno de estos formatos: ';
													echo implode(', ', $allowedFormat)."</p>";
											}

										}

									}
								}
							?>
							<div class="btn waves-effect waves-light">
								<span>Subir imagen</span>
								<input type="file" name="event-img">
							</div>
							<div class="file-path-wrapper">
								<input type="text" class="file-path">
							</div>
						</div>
						<div class="input-field col s12">
							<?php
								if(isset($_POST['post-event'])) {
									checkEmptyInput($eventPublisher, $eventPublisherError);
								}
							?>
							<input type="text" id="publisher" value="<?php if(isset($eventPublisher)) { echo $eventPublisher; } else { echo $username; } ?>" name="publisher">
							<label for="publisher" class="active">Publicado por</label>
						</div>
						</div>
						<?php
							if(isset($_POST['post-event']) AND !empty($eventTitle) AND !empty($eventDate) AND !empty($file_path) AND !empty($eventPublisher) AND !empty($content)) {


								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> agregó un evento";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	


								$postEvent_Query = "INSERT INTO eventos (titulo, dia_del_evento, imagen, descripcion, publicador) VALUES (?, ?, ?, ?, ?)";
								$postEvent = $db->prepare($postEvent_Query);

								$postEvent->execute(array($eventTitle, $eventDate, $file_path, $content, $eventPublisher));
								echo '<script>window.location="/admin/agregar-evento"</script>';

							}
						?>
						<button type="submit" class="btn waves-effect waves-light button-margin" name="post-event">Publicar</button>
					</form>
			</div>
		</div>
	</body>
</html>