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
		$rares_id = $_GET['id'];
	}	else {
		echo '<script>window.location="/admin/rares"</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getOrderInfo_Query = "SELECT * FROM rares WHERE id = ?";
	$getOrderInfo = $db->prepare($getOrderInfo_Query);

	$getOrderInfo->execute(array($rares_id));

	if($getOrderInfo->rowCount() < 1) {
		echo '<script>window.location.href="/admin/rares";</script>';
	}

	$row = $getOrderInfo->fetch(PDO::FETCH_OBJ);

	$currentRareName = $row->nombre;
	$currentRareImg = $row->imagen;
	$currentRarePrice = $row->precio;
	$currentRareLimitedStatus = $row->limitado;

	if(isset($_POST['edit-rare'])) {
		$limitedState = $_POST['limited-state'];
		$limitedStateError = '<p class="panel-error">Tienes que elegir si el rare es ilimitado o limitado</p>';
	}
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Editar Rare: <?php echo $currentRareName; ?></h5>
				
				<div class="row">
					<form action="" method="post" enctype="multipart/form-data">
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['edit-rare'])) {
									$rareName = $_POST['rare-name'];
									$rareNameError = '<p class="panel-error">Tienes que insertar un nombre para el rare</p>';

									checkEmptyInput($rareName, $rareNameError);
								}
							?>
							<input type="text" id="rare-name" value="<?php echo $currentRareName; ?>" name="rare-name">
							<label for="rare-name" class="active">Nombre del Rare</label>
						</div>

						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['edit-rare'])) {
									$rarePrice = $_POST['rare-price'];
									$rarePriceError = '<p class="panel-error">Tienes que insertar un precio para el rare</p>';

									if($rarePrice < 0 OR !is_numeric($rarePrice)) {
										echo $rarePriceError;
									}
								}
							?>
							<input type="number" id="rare-price" value="<?php echo $currentRarePrice; ?>" name="rare-price">
							<label for="rare-price" class="active">Precio</label>
						</div>

						<div class="col s12">
							<div class="users-panel-img"><img src="<?php echo $currentRareImg; ?>" alt="<?php echo $currentRareName; ?>"></div>
						</div>

						<div class="file-field input-field col s12">
							<?php
								if(isset($_POST['edit-rare'])) {
									if(isset($_FILES['rare-img'])) {
										$allowedFormat = array(
											'jpg',
											'jpeg',
											'gif',
											'png'
										);

										$file_name = $_FILES['rare-img']['name'];
										$file_extn = strtolower(end(explode('.', $file_name)));
										$file_loc = $_FILES['rare-img']['tmp_name'];


										if(isset($_FILES['rare-img']['name']) AND !empty($_FILES['rare-img']['name']) AND in_array($file_extn, $allowedFormat) AND !empty($rareName) AND isset($rarePrice)) {
											$file_path = '../imagenes/rares/'.substr(md5(time()), 0, 10).'.'.$file_extn;
											move_uploaded_file($file_loc, $file_path);
										}	else if(isset($_FILES['rare-img']['name']) AND !empty($_FILES['rare-img']['name']) AND !in_array($file_extn, $allowedFormat)) {
											echo '<p class="panel-error">Sólo puedes subir una imagen en uno de estos formatos: ';
												echo implode(', ', $allowedFormat)."</p>";
										}

									}

								}
							?>
							<div class="btn">
								<span>Subir image</span>
								<input type="file" name="rare-img">
							</div>
							<div class="file-path-wrapper">
								<input type="text" class="file-path">
							</div>
						</div>

						<div class="switch col s12">
							<?php
								if(isset($_POST['edit-rare'])) {
									if(isset($limitedState)) {
										$limitedState = '1';
									}	else {
										$limitedState = '0';
									}
								}
							?>
							<label>
								Ilimitado
								<input type="checkbox" name="limited-state" <?php if($currentRareLimitedStatus == '1') { echo "checked"; } ?>>
								<span class="lever"></span>
								Limitado
							</label>
						</div>
						
						<div class="col s12">
							<?php
								if(isset($_POST['edit-rare']) AND !empty($rareName) AND isset($rarePrice) AND !empty($file_path) AND isset($limitedState)) {
									$addRare_Query = "UPDATE rares SET nombre = ?, precio = ?, imagen = ?, limitado = ? WHERE id = ?";
									$addRare = $db->prepare($addRare_Query);
 
									$addRare->execute(array($rareName, $rarePrice, $file_path, $limitedState, $rares_id));
									echo '<script>window.location="/admin/editar-rare?id='.$rares_id.'"</script>';
								}	else if(isset($_POST['edit-rare']) AND !empty($rareName) AND isset($rarePrice) AND isset($limitedState)) {
									$addRare_Query = "UPDATE rares SET nombre = ?, precio = ?, limitado = ? WHERE id = ?";
									$addRare = $db->prepare($addRare_Query);
 
									$addRare->execute(array($rareName, $rarePrice, $limitedState, $rares_id));
									echo '<script>window.location="/admin/editar-rare?id='.$rares_id.'"</script>';
								}
							?>
							<button type="submit" class="btn waves-effect waves-light button-margin" name="edit-rare">Cambiar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>