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
				<h5 class="section-title">Logs </h5>

				<table class="striped responsive-table">
					<thead>
						<td><strong>Últimos movimientos en el panel de LUFantasie.</strong></td>
					</thead>
					<tbody>
						<?php
							$getAllUsers_Query = "SELECT * FROM logs ORDER BY id DESC LIMIT 50";
							$getAllUsers = $db->prepare($getAllUsers_Query);

							$getAllUsers->execute();

							while($row = $getAllUsers->fetch(PDO::FETCH_OBJ)) {
								$usuario = $row->usuario;
								$mensaje = $row->mensaje;

								echo '<tr>
									<td>'.$mensaje.'</td>
								</tr>';
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>