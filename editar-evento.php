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

	if(isset($_GET['id']) AND !empty($_GET['id'])) {
		$events_id = $_GET['id'];
	}	else {
		echo '<script>window.location="/admin/eventos"</script>';
	}

	$getSelectedEvent_Query = "SELECT * FROM eventos WHERE id = ?";
	$getSelectedEvent = $db->prepare($getSelectedEvent_Query);

	$getSelectedEvent->execute(array($events_id));

	$eventsRow = $getSelectedEvent->fetch(PDO::FETCH_OBJ);
	$eventsTitle = $eventsRow->titulo;
	$eventsDate = $eventsRow->dia_del_evento;
	$eventsImg = $eventsRow->imagen;
	$eventsPublisher = $eventsRow->publicador;

	if(isset($_POST['edit-event'])) {
		$eventPublisher = $_POST['publisher'];
		$eventPublisherError = '<p class="panel-error">Tienes que insertar el publicador</p>';
	}
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Editar Evento: <?php echo $eventsTitle; ?></h5>
					<form action="" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['edit-event'])) {
									$eventTitle = $_POST['title'];
									$eventTitleError = '<p class="panel-error">Tienes que insertar un título</p>';

									checkEmptyInput($eventTitle, $eventTitleError);
								}
							?>
							<input type="text" id="title" name="title" value="<?php if(isset($eventTitle)) { echo $eventTitle; } else { echo $eventsTitle; } ?>">
							<label for="title" class="active">Título del Evento</label>
						</div>
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['edit-event'])) {
									$eventDate = $_POST['date'];
									$eventDateError = '<p class="panel-error">Tienes que elegir un día para el evento</p>';

									checkEmptyInput($eventDate, $eventDateError);
								}
							?>
							<input type="date" class="datepicker" id="date" name="date" value="<?php if(isset($eventDate)) { echo $eventDate; } else { echo $eventsDate; } ?>">
							<label for="date" class="active">Día</label>
						</div>
						<div class="col s12">
							<div class="users-panel-img"><img src="<?php echo $eventsImg; ?>" alt="<?php echo $eventsTitle; ?>"></div>
						</div>
						<div class="file-field input-field col s12">
							<p class="panel-warning">Tamaño recomendado: 320x90</p>
							<?php
								if(isset($_POST['edit-event'])) {
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

											if(in_array($file_extn, $allowedFormat) AND !empty($eventTitle) AND !empty($eventDate) AND !empty($eventPublisher)) {
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
								if(isset($_POST['edit-event'])) {
									checkEmptyInput($eventPublisher, $eventPublisherError);
								}
							?>
							<input type="text" id="publisher" value="<?php if(isset($eventPublisher)) { echo $eventPublisher; } else { echo $eventsPublisher; } ?>" name="publisher">
							<label for="publisher" class="active">Publicado por</label>
						</div>
						</div>
						<?php
							if(isset($_POST['edit-event']) AND !empty($eventTitle) AND !empty($eventDate) AND !empty($file_path) AND !empty($eventPublisher)) {

								$postEvent_Query = "UPDATE eventos SET titulo = ?, dia_del_evento = ?, imagen = ?, publicador = ? WHERE id = ?";
								$postEvent = $db->prepare($postEvent_Query);

								$postEvent->execute(array($eventTitle, $eventDate, $file_path, $eventPublisher, $events_id));
								echo '<script>window.location="/admin/editar-evento?id='.$events_id.'"</script>';
							}	else if(isset($_POST['edit-event']) AND !empty($eventTitle) AND !empty($eventDate) AND !empty($eventPublisher)) {
									$postEvent_Query = "UPDATE eventos SET titulo = ?, dia_del_evento = ?, publicador = ? WHERE id = ?";
									$postEvent = $db->prepare($postEvent_Query);

									$postEvent->execute(array($eventTitle, $eventDate, $eventPublisher, $events_id));
									echo '<script>window.location="/admin/editar-evento?id='.$events_id.'"</script>';
								}
						?>
						<button type="submit" class="btn waves-effect waves-light button-margin" name="edit-event">Cambiar</button>
					</form>
			</div>
		</div>
	</body>
</html>