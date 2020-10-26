<?php
session_start();

include "inc/connection.php";
include "inc/arrays.php";
include "inc/functions.php";

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

// RECIBIR USUARIOS
$userProfile_Query = "SELECT * FROM usuarios WHERE id = ?";
$userProfile = $db->prepare($userProfile_Query);

$userProfile->execute(array($_SESSION['user_id']));


while($row = $userProfile->fetch(PDO::FETCH_OBJ)) {
	$lfid = $row->id;
	$userProfile_username = $row->nombre;
	$userProfile_rank = $row->rango;
	$userProfile_imagen = $row->imagen;
	$userProfile_perlas = $row->perlas;
	$userProfile_nacimiento = $row->fecha_de_nacimiento;
	$userProfile_look = $row->look;
	$userProfile_verificado = $row->verificado;
};

// RECIBIR VOUCHERS

$lfVoucher_Query = "SELECT * FROM vouchers";
$lfVoucher = $db->prepare($lfVoucher_Query);

$lfVoucher->execute();


while($rowVoucher = $lfVoucher->fetch(PDO::FETCH_OBJ)) {
	$voucher = $rowVoucher->codigo;
	$disponible = $rowVoucher->disponibles;
};

include "inc/head.php";
if(isset($_SESSION['user_id'])) {
	include "inc/header_logged.php";
}	else {
	include "inc/header.php";
}
?>

<!-- Events -->
<div class="events">
	<?php
		$getAllEvents_Query = "SELECT * FROM eventos LIMIT 6";
		$getAllEvents = $db->prepare($getAllEvents_Query);

		$getAllEvents->execute();

		while($eventsRow = $getAllEvents->fetch(PDO::FETCH_OBJ)) {
			$eventId = $eventsRow->id;
			$eventTitle = $eventsRow->titulo;
			$eventDate = $eventsRow->dia_del_evento;
			$eventImg = $eventsRow->imagen;
			$eventPublisher = $eventsRow->publicador;

			echo '<div class="event wow bounceInRight">
					<div class="event-img">
						<img src="'.$eventImg.'" alt="'.$eventTitle.'">
					</div>
					<div class="event-details">
						<h4 class="event-title">'.$eventTitle.'</h4>
						<h5 class="event-date">El '.$eventDate.'</h5>
						<div class="event-wrapper">
							<h5 class="event-publisher">Publicado por <strong>'.$eventPublisher.'</strong></h5>
							<h5 class="event-more-info"><button class="styled-anchor openEventModal" data-event-id="'.$eventId.'">Más info</a></h5>
						</div>
					</div>
				</div>';
		}
	?>
</div>

<?php
	$getAllEvents_Query = "SELECT * FROM eventos";
	$getAllEvents = $db->prepare($getAllEvents_Query);

	$getAllEvents->execute();
	while($eventsRow = $getAllEvents->fetch(PDO::FETCH_OBJ)) {
			$eventId = $eventsRow->id;
			$eventTitle = $eventsRow->titulo;
			$eventDate = $eventsRow->dia_del_evento;
			$eventImg = $eventsRow->imagen;
			$eventDesc = $eventsRow->descripcion;
			$eventPublisher = $eventsRow->publicador;

			echo '<div class="more-info-modal" id="more-info-modal_'.$eventId.'">
					<div class="event-modal-wrapper">
						<div class="main-box">
							<div class="event-modal-header">
								<div class="left-side">
									<h1>'.$eventTitle.'</h1>
									<h4>'.$eventDesc.'</h4>
								</div>
								<div class="right-side">
									<button class="closeModal closeEventModal"><i class="fa fa-times"></i></button>
								</div>
							</div>


						</div>
					</div>
				</div>';
		}
?>
<!-- /Events -->
<!-- /Calendario navideño -->






<div class="row main-content">
	<?php include "inc/sidebar.php"; ?>


	<div class="right-column col-md-8">
		<div class="double-right-column-boxes" id="tops">
			<div class="right-column-small-box wow fadeInRight">
				<div class="big-box-icon top"><i class="fa fa-gift"></i></div>
				<div class="sidebar-title">
				<div class="small-box-title">
					¡Se acaba el 2019!
				</div>
				</div>
				<div class="top-users">
					<div class="shop-bg">
						<div class="badge">
							<img src="https://habboo-a.akamaihd.net/c_images/album1584/ES91G.gif">
						</div>
						<div class="title">Ayudante de Santa</div>
						<div class="desc">Que su dulce espíritu llene de luz y amor tu corazón. ¡Feliz Navidad!	</div>
					</div>
					<div class="shop-bg">
						<div class="badge">
							<img src="https://habboo-a.akamaihd.net/c_images/album1584/ES97G.gif">
						</div>
						<div class="title">Reno de cristal</div>
						<div class="desc">El adorno más especial y delicado para esta Navidad.	</div>
					</div>
				</div>
				<div class="divider"></div>
				<?php
					$chechUserCount_Query = "SELECT * FROM users_badges WHERE code_badge = 'GIFT2' AND id_user = ?";
					$checkUserRank = $db->prepare($chechUserCount_Query);

					$checkUserRank->execute(array($_SESSION['user_id']));

					$displayUserCount = $checkUserRank->rowCount();

				?>
				
				<?php if ($displayUserCount >= 1): ?>
					<button>Ya tienes este regalo</button>
				<?php endif ?>

				<?php if ($displayUserCount == 0): ?>
					<?php
						if(isset($_POST['recibir-regalo'])) {
								$GIFT2 = 'GIFT2';
								$publishNews_Query = "INSERT INTO users_badges (code_badge, id_user) VALUES (?, ?)";
								$publishNews = $db->prepare($publishNews_Query);

								$publishNews->execute(array($GIFT2, $_SESSION['user_id']));

								$GIFT3 = 'GIFT3';
								$publishNews2_Query = "INSERT INTO users_badges (code_badge, id_user) VALUES (?, ?)";
								$publishNews2 = $db->prepare($publishNews2_Query);

								$publishNews2->execute(array($GIFT3, $_SESSION['user_id']));

								if($publishNews AND $publishNews2) {
									echo '<script>window.location.href="/tienda";</script>';
								}
							}
					?>
					<form method="post">
					<button type="submit" name="recibir-regalo">Reclamar regalo</button>
					</form>
				<?php endif ?>

			</div>

			<div class="right-column-small-box wow fadeInRight">
				<div class="sidebar-title">
				<div class="big-box-icon top"><i class="fa fa-pencil"></i></div>
				<div class="small-box-title">
				  Estilos para tu nombre
				</div>
				</div>
				<div class="alert-body alert-error">En este momento no hay ningún estilo publicado</div>
				<!--INICIO LOGRO-->
				<!-- <div class="shop-bg">
					<span class="name-effect">
						<font class="effect_3" style="font-family: power;"> LUFantasie</font>
					</span>
					<button class="btn-buy">
						0 <img src="https://i.imgur.com/xa4q8wj.png" style="margin-top: 0px">
					</button>
				</div> -->
				<!--FIN LOGRO-->
			</div>
		</div>


		<div class="main-box wow fadeInRight">
			<div class="box-icon info"><i class="fa fa-shield"></i></div>
			<div class="box-title important-bg">
				<h5 class="title">Logros semanales</h5>
			</div>
			<!--INICIO LOGRO-->
			<?php
					$chechUserCount_Query = "SELECT * FROM users_badges WHERE code_badge = 'STORE1' AND id_user = ?";
					$checkUserRank = $db->prepare($chechUserCount_Query);

					$checkUserRank->execute(array($_SESSION['user_id']));

					$displayUserCount = $checkUserRank->rowCount();

				?>
				<div class="store-logro">
					<div class="logro">
						<img src="https://habboo-a.akamaihd.net/c_images/album1584/ES96G.gif">
						<div class="price">10 patos</div>
					</div>
					<?php if ($displayUserCount == 0): ?>
						<form method="post">
							<?php if (isset($_POST['comprar-logro'])): ?>
								<?php 

								// dar placa a usuario
								$STORE1 = 'STORE1';
								$store1Buy_Query = "INSERT INTO users_badges (code_badge, id_user) VALUES (?, ?)";
								$store1Buy = $db->prepare($store1Buy_Query);

								$store1Buy->execute(array($STORE1, $_SESSION['user_id']));
								
								// query para quitar patos 

								$patosQuitados = $userProfile_perlas - 10;

								$restarPatos_Query = "UPDATE usuarios SET perlas = ? WHERE id = ?";
								$restarPatos = $db->prepare($restarPatos_Query);

								$restarPatos->execute(array($patosQuitados, $_SESSION['user_id']));

								?>
							<?php endif ?>
							<button type="submit" name="comprar-logro" class="btn-no-comprado">Comprar</button>
						</form>
					<?php endif ?>
					<?php if ($displayUserCount >= 1): ?>
						<button type="submit" class="btn-comprado">Ya lo tienes</button>
					<?php endif ?>
					
				</div>
			<!--FIN LOGRO-->

			<!--INICIO LOGRO-->
			<?php
					$chechUserCount_Query = "SELECT * FROM users_badges WHERE code_badge = 'STORE2' AND id_user = ?";
					$checkUserRank = $db->prepare($chechUserCount_Query);

					$checkUserRank->execute(array($_SESSION['user_id']));

					$displayUserCount = $checkUserRank->rowCount();

				?>
				<div class="store-logro">
					<div class="logro">
						<img src="https://habboo-a.akamaihd.net/c_images/album1584/UK093.gif">
						<div class="price">5 patos</div>
					</div>
					<?php if ($displayUserCount == 0): ?>
						<form method="post">
							<?php if (isset($_POST['comprar-logro'])): ?>
								<?php 

								// dar placa a usuario
								$STORE2 = 'STORE2';
								$store1Buy_Query = "INSERT INTO users_badges (code_badge, id_user) VALUES (?, ?)";
								$store1Buy = $db->prepare($store1Buy_Query);

								$store1Buy->execute(array($STORE2, $_SESSION['user_id']));
								
								// query para quitar patos 

								$patosQuitados = $userProfile_perlas - 5;

								$restarPatos_Query = "UPDATE usuarios SET perlas = ? WHERE id = ?";
								$restarPatos = $db->prepare($restarPatos_Query);

								$restarPatos->execute(array($patosQuitados, $_SESSION['user_id']));

								?>
							<?php endif ?>
							<button type="submit" name="comprar-logro" class="btn-no-comprado">Comprar</button>
						</form>
					<?php endif ?>
					<?php if ($displayUserCount >= 1): ?>
						<button type="submit" class="btn-comprado">Ya lo tienes</button>
					<?php endif ?>
					
				</div>
			<!--FIN LOGRO-->

			<!--INICIO LOGRO-->
			<?php
					$chechUserCount_Query = "SELECT * FROM users_badges WHERE code_badge = 'STORE3' AND id_user = ?";
					$checkUserRank = $db->prepare($chechUserCount_Query);

					$checkUserRank->execute(array($_SESSION['user_id']));

					$displayUserCount = $checkUserRank->rowCount();

				?>
				<div class="store-logro">
					<div class="logro">
						<img src="https://habboo-a.akamaihd.net/c_images/album1584/TRB65.gif">
						<div class="price">10 patos</div>
					</div>
					<?php if ($displayUserCount == 0): ?>
						<form method="post">
							<?php if (isset($_POST['comprar-logro'])): ?>
								<?php 

								// dar placa a usuario
								$STORE3 = 'STORE3';
								$store1Buy_Query = "INSERT INTO users_badges (code_badge, id_user) VALUES (?, ?)";
								$store1Buy = $db->prepare($store1Buy_Query);

								$store1Buy->execute(array($STORE3, $_SESSION['user_id']));
								
								// query para quitar patos 

								$patosQuitados = $userProfile_perlas - 10;

								$restarPatos_Query = "UPDATE usuarios SET perlas = ? WHERE id = ?";
								$restarPatos = $db->prepare($restarPatos_Query);

								$restarPatos->execute(array($patosQuitados, $_SESSION['user_id']));

								?>
							<?php endif ?>
							<button type="submit" name="comprar-logro" class="btn-no-comprado">Comprar</button>
						</form>
					<?php endif ?>
					<?php if ($displayUserCount >= 1): ?>
						<button type="submit" class="btn-comprado">Ya lo tienes</button>
					<?php endif ?>
					
				</div>
			<!--FIN LOGRO-->

			<!--INICIO LOGRO-->
			<?php
					$chechUserCount_Query = "SELECT * FROM users_badges WHERE code_badge = 'STORE4' AND id_user = ?";
					$checkUserRank = $db->prepare($chechUserCount_Query);

					$checkUserRank->execute(array($_SESSION['user_id']));

					$displayUserCount = $checkUserRank->rowCount();

				?>
				<div class="store-logro">
					<div class="logro">
						<img src="https://habboo-a.akamaihd.net/c_images/album1584/DE10C.gif">
						<div class="price">5 patos</div>
					</div>
					<?php if ($displayUserCount == 0): ?>
						<form method="post">
							<?php if (isset($_POST['comprar-logro'])): ?>
								<?php 

								// dar placa a usuario
								$STORE4 = 'STORE4';
								$store1Buy_Query = "INSERT INTO users_badges (code_badge, id_user) VALUES (?, ?)";
								$store1Buy = $db->prepare($store1Buy_Query);

								$store1Buy->execute(array($STORE4, $_SESSION['user_id']));
								
								// query para quitar patos 

								$patosQuitados = $userProfile_perlas - 5;

								$restarPatos_Query = "UPDATE usuarios SET perlas = ? WHERE id = ?";
								$restarPatos = $db->prepare($restarPatos_Query);

								$restarPatos->execute(array($patosQuitados, $_SESSION['user_id']));

								?>
							<?php endif ?>
							<button type="submit" name="comprar-logro" class="btn-no-comprado">Comprar</button>
						</form>
					<?php endif ?>
					<?php if ($displayUserCount >= 1): ?>
						<button type="submit" class="btn-comprado">Ya lo tienes</button>
					<?php endif ?>
					
				</div>
			<!--FIN LOGRO-->



			</div>

<div class="main-box wow fadeInRight">
			<div class="box-icon info"><i class="fa fa-cube"></i></div>
			<div class="box-title info-bg">
				<h5 class="title">Paquetes</h5>
			</div>

			<div class="alert-body alert-error">En este momento no hay ningún paquete de temporada publicado</div>

<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
    $('#popover_lf').popover({ trigger: "hover" });
});
</script>
</div>
			</div>
		</div>
		<?php include "inc/footer.php"; ?>
