<?php
	session_start();

	require_once "../htdocs/inc/connection.php";
	// include "inc/connection.php";

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

	$checkUserRank_Query = "SELECT nombre, imagen, rango FROM usuarios WHERE id = ?";
	$checkUserRank = $db->prepare($checkUserRank_Query);

	$checkUserRank->execute(array($_SESSION['user_id']));

	while($row = $checkUserRank->fetch(PDO::FETCH_OBJ)) {
		$userAccount_rank = $row->rango;
		$userAccount_Username = $row->nombre;
		$userAccount_img = $row->imagen;
	}

	$_SESSION['username'] = $userAccount_Username;
	$_SESSION['user_rank'] = $userAccount_rank;
	$_SESSION['user_img'] = $userAccount_img;

	$checkUserAllowed_Query = "SELECT nombre, nombre_individual, acceso FROM rangos WHERE nombre_individual = ?";
	$checkUserAllowed = $db->prepare($checkUserAllowed_Query);

	$checkUserAllowed->execute(array($userAccount_rank));

	while($rankRow = $checkUserAllowed->fetch(PDO::FETCH_OBJ)) {
		$userRank = $rankRow->acceso;
	}

	if(!isset($_SESSION['user_id']) OR $userRank != 1) {
		// header("Location: ");
	}	else {
		$_SESSION['userAllowed'] = true;
	}

	include "inc/head.php";
	include "inc/nav.php";
?>
		
		<div class="container">
			<h3 class="greeting">Bienvenido/a <span class="light-weight-text"><?php echo $userAccount_Username; ?></span>!</h3>

			<div class="section">
				<h5 class="section-title">Estatísticas</h5>
				<div class="row">
					<div class="col s12 m4">
						<div class="statistic registered-users z-depth-2">
							<i class="fa fa-sign-in statistic-icon"></i>

							<div class="statistic-details">
								<?php
									$chechUserCount_Query = "SELECT * FROM usuarios";
									$checkUserRank = $db->prepare($chechUserCount_Query);

									$checkUserRank->execute();

									$displayUserCount = $checkUserRank->rowCount();
								
									echo '<h1>'.$displayUserCount.'</h1>';

								?>
								<h6>Usuarios Registrados</h6>
							</div>
						</div>
					</div>
					
					<div class="col s12 m4">
						<div class="statistic news-published z-depth-2">
							<i class="fa fa-newspaper-o statistic-icon"></i>

							<div class="statistic-details">
								<?php
									$checkNewsCount_Query = "SELECT * FROM noticias";
									$checkNewsCount = $db->prepare($checkNewsCount_Query);

									$checkNewsCount->execute();

									$displayNewsCount = $checkNewsCount->rowCount();
								
									echo '<h1>'.$displayNewsCount.'</h1>';

								?>
								<h6>Noticias Publicadas</h6>
							</div>
						</div>
					</div>

					<div class="col s12 m4">
						<div class="statistic events z-depth-2">
							<i class="fa fa-calendar statistic-icon"></i>

							<div class="statistic-details">
								<?php
									$checkEventsCount_Query = "SELECT * FROM eventos";
									$checkEventsCount = $db->prepare($checkEventsCount_Query);

									$checkEventsCount->execute();

									$displayEventsCount = $checkEventsCount->rowCount();
								
									echo '<h1>'.$displayEventsCount.'</h1>';

								?>
								<h6>Eventos</h6>
							</div>
						</div>
					</div>

					<div class="col s12 m4">
						<div class="statistic rares z-depth-2">
							<i class="fa fa-list statistic-icon"></i>

							<div class="statistic-details">
								<?php
									$checkRaresCount_Query = "SELECT * FROM noticias WHERE aprobado = 0";
									$checkRaresCount = $db->prepare($checkRaresCount_Query);

									$checkRaresCount->execute();

									$displayRaresCount = $checkRaresCount->rowCount();
								
									echo '<h1>'.$displayRaresCount.'</h1>';

								?>
								<h6>Noticias por aprobar</h6>
							</div>
						</div>
					</div>

					<div class="col s12 m4">
						<div class="statistic orders z-depth-2">
							<i class="fa fa-shield statistic-icon"></i>

							<div class="statistic-details">
								<?php
									$checkOrdersCount_Query = "SELECT * FROM habbo_placas";
									$checkOrdersCount = $db->prepare($checkOrdersCount_Query);

									$checkOrdersCount->execute();

									$displayOrdersCount = $checkOrdersCount->rowCount();
								
									echo '<h1>'.$displayOrdersCount.'</h1>';

								?>
								<h6>Logros subidos</h6>
							</div>
						</div>
					</div>

					<div class="col s12 m4">
						<div class="statistic messages z-depth-2">
							<i class="fa fa-envelope statistic-icon"></i>

							<div class="statistic-details">
								<?php
									$checkMessagesCount_Query = "SELECT * FROM mensajes";
									$checkMessagesCount = $db->prepare($checkMessagesCount_Query);

									$checkMessagesCount->execute();

									$displayMessagesCount = $checkMessagesCount->rowCount();
								
									echo '<h1>'.$displayMessagesCount.'</h1>';

								?>
								<h6>Mensajes enviados</h6>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</body>
</html>