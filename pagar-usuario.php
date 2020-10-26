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
		$orders_id = (int)$_GET['id'];
	}	else {
		echo '<script>window.location.href="/admin/ordenes";</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<div style="width: 75%; margin: auto; text-align: center;">
					<?php
						$getSelectedOrder_Query = "SELECT id, nombre, hobba_nombre, pagado FROM ordenes WHERE id = ?";
						$getSelectedOrder = $db->prepare($getSelectedOrder_Query);

						$getSelectedOrder->execute(array($orders_id));

						if($getSelectedOrder->rowCount() < 1) {
							echo '<script>window.location.href="/admin/ordenes";</script>';
						}

						$row = $getSelectedOrder->fetch(PDO::FETCH_OBJ);
						$orderId = $row->id;
						$orderUsername = $row->nombre;
						$orderHobbaUsername = $row->hobba_nombre;
						$orderPaid = $row->pagado;
					?>
					<h3 class="shrink-text-730">Pagar <span class="light-weight-text"><?php echo $orderUsername; ?></span> / <span class="light-weight-text"><?php echo $orderHobbaUsername; ?></span></h3>
					<div style="margin: 40px 0;">
					
					
					<div style="margin: 25px 0;">
						<?php
							if(isset($_POST['pay-user'])) {
								$paidStatus = $_POST['paid-status'];

								if(isset($paidStatus)) {
									$paidStatus = '1';
								}	else {
									$paidStatus = '0';
								}

								$updateOrderPaidStarus_Query = "UPDATE ordenes SET pagado = ? WHERE id = ?";
								$updateOrderPaidStarus = $db->prepare($updateOrderPaidStarus_Query);

								$updateOrderPaidStarus->execute(array($paidStatus, $orders_id));
								echo '<script>window.location.href="/admin/ordenes";</script>';

							}
						?>
						<form action="" method="post">
							<div class="switch input-margin">
								<label>
									No pagado
									<input type="checkbox" name="paid-status" <?php if($orderPaid == '1') { echo "checked"; } ?>>
									<span class="lever"></span>
									Pagado
								</label>
							</div>
							<button type="submit" class="btn waves-effect waves-light button-margin" name="pay-user">Cambiar</button>
						</form>
					</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>