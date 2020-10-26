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
				<h5 class="section-title">Ordenes</h5>
				<?php
					if(isset($_POST['search-user'])) {
						$userSearched = $_POST['user'];
						echo '<script>window.location.href="/admin/ordenes?usuario='.$userSearched.'";</script>';
					}
				?>
				<form action="" method="post">
				<div class="row">
					<div class="input-field col s12 m9">
						<input type="text" name="user" id="user-search">
						<label for="user-search">Buscar Usuario</label>
					</div>
					<div class="col s12 m3">
						<button type="submit" class="btn-large waves-effect waves-light full-width" name="search-user">Buscar</button>
					</div>
				</div>
				</form>

				<table class="striped responsive-table">
					<thead>
						<td>Nombre</td>
						<td>Hobba Nombre</td>
						<td>Rare ID</td>
						<td>Pagado</td>
						<td>Pagar</td>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['usuario'])) {
							$getAllOrders_Query = "SELECT id, nombre, hobba_nombre, rare_id, pagado FROM ordenes WHERE pagado = '0' ORDER BY id DESC";
							$getAllOrders = $db->prepare($getAllOrders_Query);

							$getAllOrders->execute();

							while($row = $getAllOrders->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$username = $row->nombre;
								$hobbaName = $row->hobba_nombre;
								$rareId = $row->rare_id;
								$paid = $row->pagado;

								if($paid == 0) {
									$paid = '<span style="color: #e74c3c">No pagado</span>';
								}	else if($paid == 1) {
									$paid = '<span style="color: #2ecc71">Pagado</span>';
								}

								echo '<tr>
									<td>'.$username.'</td>
									<td>'.$hobbaName.'</td>
									<td>'.$rareId.'</td>
									<td>'.$paid.'</td>
									<td><a href="/admin/pagar-usuario?id='.$id.'">Pagar</a></td>
								</tr>';
							}
						}

						if(isset($_GET['usuario']) AND empty($_GET['usuario'])) {
							echo '<script>window.location.href="/admin/ordenes";</script>';
						}

						if(isset($_GET['usuario'])) {
							$getSearchedUsername = $_GET['usuario'];
							$getSearchedUser_Query = "SELECT id, nombre, hobba_nombre, rare_id, pagado FROM ordenes WHERE nombre LIKE ? OR hobba_nombre LIKE ?";
							$getSearchedUser = $db->prepare($getSearchedUser_Query);

							$getSearchedUser->execute(array("%".$getSearchedUsername."%", "%".$getSearchedUsername."%"));

							while($ordersRow = $getSearchedUser->fetch(PDO::FETCH_OBJ)) {
								$ordersId = $ordersRow->id;
								$ordersUsername = $ordersRow->nombre;
								$ordersHobbaUsername = $ordersRow->hobba_nombre;
								$ordersRareId = $ordersRow->rare_id;
								$ordersPaid = $ordersRow->pagado;

								if($ordersPaid == 0) {
									$ordersPaid = '<span style="color: #e74c3c">No pagado</span>';
								}	else if($ordersPaid == 1) {
									$ordersPaid = '<span style="color: #2ecc71">Pagado</span>';
								}

								echo '<tr>
									<td>'.$ordersUsername.'</td>
									<td>'.$ordersHobbaUsername.'</td>
									<td>'.$ordersRareId.'</td>
									<td>'.$ordersPaid.'</td>
									<td><a href="/admin/pagar-usuario?id='.$ordersId.'">Pagar</a></td>
									<td></td>
								</tr>';
							}

							if($getSearchedUser->rowCount() < 1) {
								echo '<h3 style="font-weight: 200;">No hay usuarios similar a "<span style="font-weight: 500;">'.htmlspecialchars($getSearchedUsername).'</span>"</h3>';
							}
						}
						?>
					</tbody>
				</table>
				<?php
				if(!isset($_GET['usuario'])) {
					if($getAllOrders->rowCount() < 1) {
						echo '<div style="text-align: center;">
							<div style="font-size: 162px; color: rgba(0, 0, 0, .2); line-height: 150px; margin: 30px 0;"><i class="fa fa-check"></i></div>
							<h4 style="color: rgba(0, 0, 0, .6);">Todos los usuarios están pagados</h4>
						</div>';
					}
				}
				?>
			</div>
		</div>
	</body>
</html>