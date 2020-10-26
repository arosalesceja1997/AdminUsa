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
	}	else if($_SESSION['user_rank'] != "Equipo de Redes Sociales" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Equipo de Entretenimiento") {
		header("Location: /admin");
	}

	if(isset($_GET['id']) AND !empty($_GET['id'])) {
		$users_id = $_GET['id'];
	}	else {
		echo '<script>window.location="/admin/usuarios"</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getUserInfo_Query = "SELECT nombre, correo_e, rango, perlas, gymkana, baneado, imagen, puntos_web FROM usuarios WHERE id = ?";
	$getUserInfo = $db->prepare($getUserInfo_Query);

	$getUserInfo->execute(array($users_id));

	if($getUserInfo->rowCount() < 1) {
		echo '<script>window.location.href="/admin/usuarios";</script>';
	}

	$row = $getUserInfo->fetch(PDO::FETCH_OBJ);
	$username = $row->nombre;
	$email = $row->correo_e;
	$rank = $row->rango;
	$perls = $row->perlas;
	$gymPoints = $row->gymkana;
	$banned = $row->baneado;
	$imagen = $row->imagen;
	$puntosweb = $row->puntos_web;

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
						<button type="submit" class="btn-large waves-effect waves-light full-width" name="search-user">Buscar</button>
					</div>
				</div>
				</form>

				<div class="z-depth-1" style="padding: 20px;">
					<ul class="tabs row">

						<?php if($_SESSION['user_rank'] == "Colaboradores") : ?><li class="tab col s3"><a href="#rank">Rango</a></li><?php endif; ?>
						<li class="tab col s3"><a href="#perls">Patos</a></li>

						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li class="tab col s3"><a href="#points">GYM puntos</a></li><?php endif; ?>

						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li class="tab col s3"><a href="#ban">Banear</a></li><?php endif; ?>

						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li class="tab col s3"><a href="#rank">Rango</a></li><?php endif; ?>

						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li class="tab col s3"><a href="#look">Look</a></li><?php endif; ?>

						<li class="tab col s3"><a href="#logro">Dar logro</a></li>
						<li class="tab col s3"><a href="#puntosweb">Puntos Web</a></li>
					</ul>
					<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?>
					<div id="rank">
						<div class="input-field">
							<?php
								$getAllRanks_Query = "SELECT * FROM rangos";
								$getAllRanks = $db->prepare($getAllRanks_Query);

								$getAllRanks->execute();

								if(isset($_POST['change-rank'])) {
							
									$selectedRank = $_POST['rank'];
									$selectedRankError = '<p class="panel-error">Tienes que elegir un rango</p>';

									checkEmptyInput($selectedRank, $selectedRankError);

									if(!empty($_POST['rank'])) {


								$mensajeRango = "<strong>".$_SESSION['username']."</strong> ha cambiado el rango de ".$username." a <strong>".$selectedRank."</strong>";
								$logsRango_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logsRangos = $db->prepare($logsRango_Query);
								$logsRangos->execute(array($mensajeRango, $_SESSION['username']));

										$changeUsersRank_Query = "UPDATE usuarios SET rango = ? WHERE id = ?";
										$changeUsersRank = $db->prepare($changeUsersRank_Query);

										$changeUsersRank->execute(array($selectedRank, $users_id));
										echo '<script>window.location.href="/admin/editar-usuario?id='.$users_id.'";</script>';
									}
								}
							?>
							<form action="" method="post">
								<select name="rank">
									<?php
										while($rankRow = $getAllRanks->fetch(PDO::FETCH_OBJ)) {
											$rankName = $rankRow->nombre;
											$rankName_individual = $rankRow->nombre_individual;

											if($rankName == $rank) {
												$selected = "selected";
											}	else {
												$selected = "";
											}

											echo '<option value="'.$rankName_individual.'" '.$selected.'>'.$rankName.'</option>';
										}
										
									?>
								</select>

								<button type="submit" name="change-rank" class="btn waves-effect waves-light">Cambiar</button>
							</form>
						</div>
					</div><?php endif; ?>
					<div id="perls">
						<form action="" method="post">
							<div class="input-field input-margin">
								<?php
									if(isset($_POST['change-perls'])) {
										$usersPerls = $_POST['perls'];
										$usersPerlsError = '<p class="panel-error">Tienes que insertar la cantidad de patos que quieres que el usuario tenga.</p>';
										if($usersPerls != 0) {
											checkEmptyInput($usersPerls, $usersPerlsError);
										}
									}
								?>
								<input type="number" id="perls" name="perls" value="<?php echo $perls; ?>">
								<label for="perls" class="active">Patos</label>
							</div>
						<?php
							if(isset($_POST['change-perls'])) {

					
								// $mensajePatos = "".$_SESSION['username']." ha cambiado las perlas de ".$username." a ".$usersPerls." patos";
								$mensajePatos = "".$_SESSION['username']." ha cambiado los patos de ".$username." a ".$usersPerls."";
								$logsPatos_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logsPatos = $db->prepare($logsPatos_Query);
								$logsPatos->execute(array($mensajePatos, $_SESSION['username']));
							

								if(!empty($usersPerls) OR $usersPerls == 0) {
									$updateUsersPoints_Query = "UPDATE usuarios SET perlas = ? WHERE id = ?";
									$updateUsersPoints = $db->prepare($updateUsersPoints_Query);

									$updateUsersPoints->execute(array($usersPerls, $users_id));
									echo '<script>window.location.href="/admin/editar-usuario?id='.$users_id.'";</script>';
								}
							}
						?>
						<button type="submit" class="btn waves-effect waves-light" name="change-perls">Cambiar</button>
						</form>
					</div>
					<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Colaboradores") : ?>
					<div id="ban">
						<?php
							if(isset($_POST['modify-ban-state'])) {
								$ban_action = $_POST['ban-action'];
								echo $ban_action;

								if($ban_action == "on") {
									$ban_action = 1;
								}	else {
									$ban_action = 0;
								}

								$updateUsersBanState_Query = "UPDATE usuarios SET baneado = ? WHERE id = ?";
								$updateUsersBanState = $db->prepare($updateUsersBanState_Query);

								$updateUsersBanState->execute(array($ban_action, $users_id));
							}
						?>
						<form action="" method="post">
						<div class="switch input-margin">
							<label>
								No baneado
								<input type="checkbox" name="ban-action" <?php if($banned == 1) { echo "checked"; } ?>>
								<span class="lever"></span>
								Baneado
							</label>
						</div>

						<button type="submit" class="btn waves-effect waves-light" name="modify-ban-state">Cambiar</button>
						</form>
					</div>
					<?php endif; ?>
					<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Colaboradores") : ?>
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
							<label for="gym-points" class="active">LUFantasie Look</label>
						</div>
						<?php
							if(isset($_POST['change-look'])) {
								if(!empty($usersGYMPoints)) {
									$updateUsersGYMPoints_Query = "UPDATE usuarios SET imagen = ? WHERE id = ?";
									$updateUsersGYMPoints = $db->prepare($updateUsersGYMPoints_Query);

									$updateUsersGYMPoints->execute(array($usersGYMPoints, $users_id));
									echo '<script>window.location.href="/admin/editar-usuario?id='.$users_id.'";</script>';
								}
							}
						?>
						<button type="submit" class="btn waves-effect waves-light" name="change-look">Cambiar</button>
						</form>
					</div>
					<?php endif; ?>
					
					<div id="logro">
						<form action="" method="post">
						<div class="input-field input-margin">
							<?php
								if(isset($_POST['give-logro'])) {
									$logro = $_POST['logro'];
									$logroError = '<p class="panel-error">Tienes que insertar el código del <b>logro</b></p>';
									if(!empty($logro)) {
										checkEmptyInput($logro, $logroError);
									}
								}
							?>
							<input type="text" id="user-look" name="logro" placeholder="Código de placa">
							<label for="gym-points" class="active">Dar LUFantasie logro</label>
						</div>
						<?php
							if(isset($_POST['give-logro']) AND !empty($logro)) {

								$mensajeLogros = "<strong>".$_SESSION['username']."</strong> dio el logro <strong>".$logro."</strong> a <strong>".$username."</strong>";
								$logsLogros_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logsLogros = $db->prepare($logsLogros_Query);
								$logsLogros->execute(array($mensajeLogros, $_SESSION['username']));								

								$publishNews_Query = "INSERT INTO users_badges (code_badge, id_user) VALUES (?, ?)";
								$publishNews = $db->prepare($publishNews_Query);

								$publishNews->execute(array($logro, $users_id));
								if($publishNews) {
									echo '<script>window.location.href="/admin/logros";</script>';
								}
							}
						?>
						<button type="submit" class="btn waves-effect waves-light" name="give-logro">Dar logro</button>
						</form>
					</div>
			
		
					<div id="puntosweb">
						  <div class="row">
						    <div class="col s12 m5">
						      <div class="card-panel teal">
						        <span class="white-text">
						        	El nuevo sistema de <strong>Puntos WEB</strong> con solo darle al botón de Sumar o Restar este añadirá o quitará un(1) y solamente un(1) punto al usuario que esté editando. Úsalo con cuidado porque estos puntos no tienen recuperación
						        </span>
						      </div>
						    </div>
						  </div>
						<form action="" method="post">
						<?php
							$sumaPuntos = $puntosweb + 1;
							if(isset($_POST['give-web-plus'])) {

								$publishNews_Query = "UPDATE usuarios SET puntos_web = ? WHERE id = ?";
								$publishNews = $db->prepare($publishNews_Query);

								$publishNews->execute(array($sumaPuntos, $users_id));
								if($publishNews) {
									echo 'Se le ha dado un +1 punto web a este usuario';
								}
							}
						?>
						<?php
							$restaPuntos = $puntosweb - 1;
							if(isset($_POST['give-web-rest'])) {

								$publishNews_Query = "UPDATE usuarios SET puntos_web = ? WHERE id = ?";
								$publishNews = $db->prepare($publishNews_Query);

								$publishNews->execute(array($restaPuntos, $users_id));
								if($publishNews) {
									echo 'Se le ha dado un -1 punto web a este usuario';
								}
							}
						?>
						<button type="submit" class="btn waves-effect waves-light" name="give-web-plus">Sumar +1 punto Web</button>
						<button type="submit" class="btn waves-effect waves-light" name="give-web-rest">Restar -1 punto Web</button>
						</form>
					</div>
					
				</div>
			</div>
		</div>
	</body>
</html>