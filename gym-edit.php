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
		$users_id = $_GET['id'];
	}	else {
		echo '<script>window.location="/admin/gym"</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getUserInfo_Query = "SELECT * FROM hobba_gym WHERE id = ?";
	$getUserInfo = $db->prepare($getUserInfo_Query);

	$getUserInfo->execute(array($users_id));

	if($getUserInfo->rowCount() < 1) {
		echo '<script>window.location.href="/admin/gym";</script>';
	}

	$row = $getUserInfo->fetch(PDO::FETCH_OBJ);

	$usuario = $row->usuario;
	$puntos = $row->puntos;
	$imagen = $row->imagen;

?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Editar Usuario: <?php echo $username; ?></h5>
				<?php
					if(isset($_POST['search-user'])) {
						$userSearched = $_POST['user'];
						echo '<script>window.location.href="/admin/usuarios?usuario='.$userSearched.'";</script>';
					}
				?>
				<form action="" method="post">
				<div class="row">
					<div class="input-field col s12 m9">
						<input type="text" name="user" id="user-search">
						<label for="user-search">Buscar Usuario</label>
					</div>
					<div class="col s12 m3">
						<button type="submit" class="btn-large waves-effect waves-light full-width" name="search-user" disabled>Buscar</button>
					</div>
				</div>
				</form>

				<div class="z-depth-1" style="padding: 20px;">
					<ul class="tabs row">
						
						<li class="tab col s3"><a href="#usuario">Usuario</a></li>
						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li class="tab col s3"><a href="#points">GYM puntos</a></li><?php endif; ?>
						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li class="tab col s3"><a href="#look">Look</a></li><?php endif; ?>
					</ul>
					
					<div id="usuario">
						<form action="" method="post">
							<div class="input-field input-margin">
								<?php
									if(isset($_POST['change-usuario'])) {
										$NombreUsuario = $_POST['usuario'];
										$NombreUsuarioError = '<p class="panel-error">Tienes que insertar un nuevo de usuario</p>';
										if($NombreUsuario != 0) {
											checkEmptyInput($NombreUsuario, $NombreUsuarioError);
										}
									}
								?>
								<input type="text" id="usuario" name="usuario" value="<?php echo $usuario; ?>">
								<label for="usuario" class="active">Nombre de usuario</label>
							</div>
						<?php
							if(isset($_POST['change-usuario'])) {
								if(!empty($NombreUsuario)) {
									$updateUsersPoints_Query = "UPDATE hobba_gym SET usuario = ? WHERE id = ?";
									$updateUsersPoints = $db->prepare($updateUsersPoints_Query);

									$updateUsersPoints->execute(array($NombreUsuario, $users_id));
									echo '<script>window.location.href="/admin/gym-edit?id='.$users_id.'";</script>';
								}
							}
						?>
						<button type="submit" class="btn waves-effect waves-light" name="change-usuario">Cambiar</button>
						</form>
					</div>

					<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?>
					<div id="points">
						<form action="" method="post">
						<div class="input-field input-margin">
							<?php
								if(isset($_POST['change-points'])) {
									$usersGYMPoints = $_POST['gym-points'];
									$usersGYMPointsError = '<p class="panel-error">Tienes que insertar la cantidad de GYM puntos que quieres que el usuario tenga.</p>';
									if($usersGYMPoints != 0) {
										checkEmptyInput($usersGYMPoints, $usersGYMPointsError);
									}
								}
							?>
							<input type="number" id="gym-points" name="gym-points" value="<?php echo $puntos; ?>">
							<label for="gym-points" class="active">GYM Puntos</label>
						</div>
						<?php
							if(isset($_POST['change-points'])) {
								if(!empty($usersGYMPoints) OR $usersGYMPoints == 0) {
									$updateUsersGYMPoints_Query = "UPDATE hobba_gym SET puntos = ? WHERE id = ?";
									$updateUsersGYMPoints = $db->prepare($updateUsersGYMPoints_Query);

									$updateUsersGYMPoints->execute(array($usersGYMPoints, $users_id));
									echo '<script>window.location.href="/admin/gym-edit?id='.$users_id.'";</script>';
								}
							}
						?>
						<button type="submit" class="btn waves-effect waves-light" name="change-points">Cambiar</button>
						</form>
					</div>
					<?php endif; ?>
					
					<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?>
					<div id="look">
						<form action="" method="post">
						<div class="input-field input-margin">
							<?php
								if(isset($_POST['change-look'])) {
									$usersGYMPoints = $_POST['user-looks'];
									$usersGYMPointsError = '<p class="panel-error">Tienes que insertar una imagen de look para poder cambiarlo</p>';
									if(!empty($usersGYMPoints)) {
										checkEmptyInput($usersGYMPoints, $usersGYMPointsError);
									}
								}
							?>
							<input type="text" id="user-look" name="user-looks" value="<?php echo $imagen; ?>">
							<label for="gym-points" class="active">Hobba Ropa</label>
						</div>
						<?php
							if(isset($_POST['change-look'])) {
								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> editó a ".$usersGYMPoints." de los usuarios GYM";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

								if(!empty($usersGYMPoints)) {
									$updateUsersGYMPoints_Query = "UPDATE hobba_gym SET imagen = ? WHERE id = ?";
									$updateUsersGYMPoints = $db->prepare($updateUsersGYMPoints_Query);

									$updateUsersGYMPoints->execute(array($usersGYMPoints, $users_id));
									echo '<script>window.location.href="/admin/gym-edit?id='.$users_id.'";</script>';
								}
							}
						?>
						<button type="submit" class="btn waves-effect waves-light" name="change-look">Cambiar</button>
						</form>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</body>
</html>