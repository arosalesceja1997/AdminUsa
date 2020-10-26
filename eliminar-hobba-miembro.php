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
		$members_id = (int)$_GET['id'];
	}	else {
		echo '<script>window.location.href="/admin/hobba-miembros";</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<div style="width: 75%; margin: auto; text-align: center;">
					<?php
						$getSelectedMember_Query = "SELECT nombre, rango FROM hobba_miembros WHERE id = ?";
						$getSelectedMember = $db->prepare($getSelectedMember_Query);

						$getSelectedMember->execute(array($members_id));

						if($getSelectedMember->rowCount() < 1) {
							echo '<script>window.location.href="/admin/hobba-miembros";</script>';
						}

						$row = $getSelectedMember->fetch(PDO::FETCH_OBJ);
						$name = $row->nombre;
						$rank = $row->rango;
					?>
					<h3 class="shrink-text-730">¿Estás seguro/a que quieres eliminar este miembro?</h3>
					<div style="margin: 40px 0;">
					<h5>Nombre</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $name; ?></h6>
					<h5>Rango</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $rank; ?></h6>
					
					<div style="margin: 25px 0;">
						<form action="" method="post" style="display: inline-block;">
							<?php
								if(isset($_POST['delete-member'])) {
									$mensajeLogs = "<strong>".$_SESSION['username']."</strong> eliminó a un miembro de Hobba";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$deleteMember_Query = "DELETE FROM hobba_miembros WHERE id = ?";
									$deleteMember = $db->prepare($deleteMember_Query);

									$deleteMember->execute(array($members_id));
									echo '<script>window.location.href="/admin/hobba-miembros";</script>';
								}
							?>
							<button type="submit" class="btn-flat waves-effect waves-green" name="delete-member">Sí</button>
						</form>
						<a href="/admin/hobba-miembros" class="btn-flat waves-effect waves-red">No</a>
					</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>