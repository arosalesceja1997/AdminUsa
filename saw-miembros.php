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

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">SAW miembros</h5>

				<table class="striped responsive-table">
					<thead>
						<td>Nombre</td>
						<td>Rango</td>
						<td>Editar</td>
						<td>Eliminar</td>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['usuario'])) {
							$getAllSAWMembers_Query = "SELECT id, nombre, rango FROM saw_miembros ORDER BY id DESC";
							$getAllSAWMembers = $db->prepare($getAllSAWMembers_Query);

							$getAllSAWMembers->execute();

							while($row = $getAllSAWMembers->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$username = $row->nombre;
								$rank = $row->rango;

								echo '<tr>
									<td>'.$username.'</td>
									<td>'.$rank.'</td>
									<td><a href="/admin/editar-saw-miembro?id='.$id.'">Editar</a></td>
									<td><a href="/admin/eliminar-saw-miembro?id='.$id.'">Eliminar</a></td>
								</tr>';
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>