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
				<h5 class="section-title">Rares Limitados</h5>

				<table class="striped responsive-table">
					<thead>
						<td>Nombre</td>
						<td>Precio</td>
						<td>Limitado</td>
						<td>Editar</td>
						<td>Eliminar</td>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['usuario'])) {
							$getAllLimitedRares_Query = "SELECT id, nombre, precio, limitado FROM rares WHERE limitado = '1' ORDER BY id ASC";
							$getAllLimitedRares = $db->prepare($getAllLimitedRares_Query);

							$getAllLimitedRares->execute();

							while($limitedRow = $getAllLimitedRares->fetch(PDO::FETCH_OBJ)) {
								$rareId = $limitedRow->id;
								$rareName = $limitedRow->nombre;
								$rarePrice = $limitedRow->precio;
								$rareLimitedStatus = $limitedRow->limitado;

								if($rareLimitedStatus == '1') {
									$rareLimitedStatus = '<span style="color: #e74c3c;">Limitado</span>';
								}

								echo '<tr>
									<td>'.$rareName.'</td>
									<td>'.$rarePrice.'</td>
									<td>'.$rareLimitedStatus.'</td>
									<td><a href="/admin/editar-rare?id='.$rareId.'">Editar</a></td>
									<td><a href="/admin/eliminar-rare?id='.$rareId.'">Eliminar</a></td>
								</tr>';
							}
						}
						?>
					</tbody>
				</table>
			</div>

			<div class="divider"></div>

			<div class="section">
				<h5 class="section-title">Rares Unlimitados</h5>

				<table class="striped responsive-table">
					<thead>
						<td>Nombre</td>
						<td>Precio</td>
						<td>Limitado</td>
						<td>Editar</td>
						<td>Eliminar</td>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['usuario'])) {
							$getAllLimitedRares_Query = "SELECT id, nombre, precio, limitado FROM rares WHERE limitado = '0' ORDER BY id ASC";
							$getAllLimitedRares = $db->prepare($getAllLimitedRares_Query);

							$getAllLimitedRares->execute();

							while($limitedRow = $getAllLimitedRares->fetch(PDO::FETCH_OBJ)) {
								$rareId = $limitedRow->id;
								$rareName = $limitedRow->nombre;
								$rarePrice = $limitedRow->precio;
								$rareLimitedStatus = $limitedRow->limitado;

								if($rareLimitedStatus == '0') {
									$rareLimitedStatus = '<span style="color: #3498db;">Unlimitado</span>';
								}

								echo '<tr>
									<td>'.$rareName.'</td>
									<td>'.$rarePrice.'</td>
									<td>'.$rareLimitedStatus.'</td>
									<td><a href="/admin/editar-rare?id='.$rareId.'">Editar</a></td>
									<td><a href="/admin/eliminar-rare?id='.$rareId.'">Eliminar</a></td>
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