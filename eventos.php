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
	}	else if($_SESSION['user_rank'] != "Reporteros" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Equipo de Redes Sociales") {
		header("Location: /admin");
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Eventos</h5>

				<table class="striped responsive-table">
					<thead>
						<td>Título</td>
						<td>Día del Evento</td>
						<td>Publicador</td>
						<td>Editar</td>
						<td>Eliminar</td>
					</thead>
					<tbody>
						<?php
							$getAllEvents_Query = "SELECT id, titulo, dia_del_evento, publicador FROM eventos";
							$getAllEvents = $db->prepare($getAllEvents_Query);

							$getAllEvents->execute();

							while($row = $getAllEvents->fetch(PDO::FETCH_OBJ)) {
								$eventId = $row->id;
								$eventTitle = $row->titulo;
								$eventDate = $row->dia_del_evento;
								$eventPublisher = $row->publicador;

								echo '<tr>
									<td>'.$eventTitle.'</td>
									<td>'.$eventDate.'</td>
									<td>'.$eventPublisher.'</td>
									<td><a href="/admin/editar-evento?id='.$eventId.'">Editar</a></td>
									<td><a href="/admin/eliminar-evento?id='.$eventId.'">Eliminar</a></td>
								</tr>';
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>