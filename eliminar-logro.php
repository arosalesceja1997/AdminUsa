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
		$news_id = (int)$_GET['id'];
	}	else {
		echo '<script>window.location.href="/admin/logros";</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<div style="width: 75%; margin: auto; text-align: center;">
					<?php
						$getSelectedNews_Query = "SELECT * FROM habbo_placas WHERE id = ?";
						$getSelectedNews = $db->prepare($getSelectedNews_Query);

						$getSelectedNews->execute(array($news_id));

						if($getSelectedNews->rowCount() < 1) {
							echo '<script>window.location.href="/admin/logros";</script>';
						}

						$row = $getSelectedNews->fetch(PDO::FETCH_OBJ);
						$code = $row->codigo;
						$titulo = $row->titulo;
					?>
					<h3 class="shrink-text-730">¿Estás seguro/a que quieres eliminar este logro??</h3>
					<div style="margin: 40px 0;">
					<h5>Código</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $code; ?></h6>
					<h5>Título</h5>
					<h6 style="color: rgba(0, 0, 0, .5)"><?php echo $titulo; ?></h6>
					
					
					<div style="margin: 25px 0;">
						<form action="" method="post" style="display: inline-block;">
							<?php
								if(isset($_POST['delete-news'])) {
									$mensajeLogs = "<strong>".$_SESSION['username']."</strong> eliminó un LUF logro";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$deleteNews_Query = "DELETE FROM habbo_placas WHERE id = ?";
									$deleteNews = $db->prepare($deleteNews_Query);

									$deleteNews->execute(array($news_id));
									echo '<script>window.location.href="/admin/logros";</script>';
								}
							?>
							<button type="submit" class="btn-flat waves-effect waves-green" name="delete-news">Estoy seguro</button>
						</form>
						<a href="/admin/logros" class="btn-flat waves-effect waves-red">No</a>
					</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>