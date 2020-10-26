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
		$rares_id = (int)$_GET['id'];
	}	else {
		echo '<script>window.location.href="/admin/rares";</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<div style="width: 75%; margin: auto; text-align: center;">
					<?php
						$getSelectedRare_Query = "SELECT nombre, precio, limitado FROM rares WHERE id = ?";
						$getSelectedRare = $db->prepare($getSelectedRare_Query);

						$getSelectedRare->execute(array($rares_id));

						if($getSelectedRare->rowCount() < 1) {
							echo '<script>window.location.href="/admin/noticias";</script>';
						}

						$row = $getSelectedRare->fetch(PDO::FETCH_OBJ);
						$rareName = $row->nombre;
						$rarePrice = $row->precio;
						$rareLimitedStatus = $row->limitado;
					?>
					<h3 class="shrink-text-730">¿Estás seguro/a que quieres eliminar este rare?</h3>
					<div style="margin: 40px 0;">
					<h5>Nombre</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $rareName; ?></h6>
					<h5>Precio</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $rarePrice; ?></h6>
					<h5>Limitado</h5>

					<?php
						if($rareLimitedStatus == '1') {
							$rareLimitedStatus = '<span style="color: #e74c3c;">Limitado</span>';
						}	else if($rareLimitedStatus == '0') {
							$rareLimitedStatus = '<span style="color: #3498db;">Unlimitado</span>';
						}
					?>

					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $rareLimitedStatus; ?></h6>
					
					<div style="margin: 25px 0;">
						<form action="" method="post" style="display: inline-block;">
							<?php
								if(isset($_POST['delete-rare'])) {
									$deleteRare_Query = "DELETE FROM rares WHERE id = ?";
									$deleteRare = $db->prepare($deleteRare_Query);

									$deleteRare->execute(array($rares_id));
									echo '<script>window.location.href="/admin/rares";</script>';
								}
							?>
							<button type="submit" class="btn-flat waves-effect waves-green" name="delete-rare">Sí</button>
						</form>
						<a href="/admin/rares" class="btn-flat waves-effect waves-red">No</a>
					</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>