<?php
	session_start();

	require_once "../inc/connection.php";
	require_once "../inc/functions.php";

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

	if(isset($_GET['id']) AND !empty($_GET['id'])) {
		$events_id = (int)$_GET['id'];
	}	else {
		echo '<script>window.location.href="/admin/eventos";</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<div style="width: 75%; margin: auto; text-align: center;">
					<?php
						$getSelectedEvent_Query = "SELECT titulo, dia_del_evento, publicador FROM eventos WHERE id = ?";
						$getSelectedEvent = $db->prepare($getSelectedEvent_Query);

						$getSelectedEvent->execute(array($events_id));

						if($getSelectedEvent->rowCount() < 1) {
							echo '<script>window.location.href="/admin/eventos";</script>';
						}

						$row = $getSelectedEvent->fetch(PDO::FETCH_OBJ);
						$eventTitle = $row->titulo;
						$eventDate = $row->dia_del_evento;
						$eventPublisher = $row->publicador;
					?>
					<h3 class="shrink-text-730">¿Estás seguro/a que quieres eliminar este Evento?</h3>
					<div style="margin: 40px 0;">
					<h5>Título</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $eventTitle; ?></h6>
					<h5>Día del Evento</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $eventDate; ?></h6>
					<h5>Publicador</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $eventPublisher; ?></h6>
					
					<div style="margin: 25px 0;">
						<form action="" method="post" style="display: inline-block;">
							<?php
								if(isset($_POST['delete-event'])) {

									$mensajeLogs = "<strong>".$_SESSION['username']."</strong> eliminó un evento";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$deleteEvent_Query = "DELETE FROM eventos WHERE id = ?";
									$deleteEvent = $db->prepare($deleteEvent_Query);

									$deleteEvent->execute(array($events_id));
									echo '<script>window.location.href="/admin/eventos";</script>';
								}
							?>
							<button type="submit" class="btn-flat waves-effect waves-green" name="delete-event">Sí</button>
						</form>
						<a href="/admin/eventos" class="btn-flat waves-effect waves-red">No</a>
					</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>