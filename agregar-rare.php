<?php
	session_start();

	require_once "../inc/connection.php";
	require_once "../inc/functions.php";
	include "/inc/arrays.php";

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
	}	else if($_SESSION['user_rank'] != "Administradores" AND $_SESSION['user_rank'] != "Colaboradores") {
		header("Location: /admin");
	}

	include "inc/head.php";
	include "inc/nav.php";

	if(!isset($_SESSION['user_id']) OR $_SESSION['userAllowed'] != true) {
		header("Location: /");
	}	else if($_SESSION['user_rank'] != "Reporteros" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Coordinación") {
		header("Location: /admin");
	}
?>

<div class="container">
			<div class="section">
				<h5 class="section-title">Añadir rare</h5>
				
				
				<form action="" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['add-rare'])) {
									$rareName = $_POST['name'];
									$rareNameError = '<p class="panel-error">Tienes que insertar un nombre para el rare</p>';

									checkEmptyInput($rareName, $rareNameError);
								}
							?>
							<input type="text" id="name" name="name" value="<?php if(isset($rareName)) { echo $rareName; } ?>">
							<label for="name" class="<?php if(isset($rareName)) { echo "active"; } ?>">Nombre</label>
						</div>
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['add-rare'])) {
									$rarePrice = $_POST['price'];
									$rarePriceError = '<p class="panel-error">Tienes que insertar un precio para el rare</p>';

									if($rarePrice < 0 OR !is_numeric($rarePrice)) {
										echo $rarePriceError;
									}
								}
							?>
							<input type="number" id="price" name="price" value="<?php if(isset($rarePrice)) { echo $rarePrice; } ?>">
							<label for="price" class="<?php if(isset($rarePrice)) { echo "active"; } ?>">Precio</label>
						</div>
						<div class="file-field input-field col s12">
							<?php
								if(isset($_POST['add-rare'])) {
									if(isset($_FILES['rare-img'])) {
										if(empty($_FILES['rare-img']['name'])) {
											echo '<p class="panel-error">Tienes que subir una imagen para el rare</p>';
										}	else {
											$allowedFormat = array(
												'jpg',
												'jpeg',
												'gif',
												'png'
											);

											$file_name = $_FILES['rare-img']['name'];
											$file_extn = strtolower(end(explode('.', $file_name)));
											$file_loc = $_FILES['rare-img']['tmp_name'];


											if(in_array($file_extn, $allowedFormat) AND !empty($rareName) AND $rarePrice >= 0 AND is_numeric($rarePrice)) {
												$file_path = '../imagenes/rares/'.substr(md5(time()), 0, 10).'.'.$file_extn;
												move_uploaded_file($file_loc, $file_path);
											}	else if(!in_array($file_extn, $allowedFormat)) {
												echo '<p class="panel-error">Sólo puedes subir una imagen en uno de estos formatos: ';
													echo implode(', ', $allowedFormat)."</p>";
											}

										}

									}
								}
							?>
							<div class="btn">
								<span>Subir imagen</span>
								<input type="file" name="rare-img">
							</div>
							<div class="file-path-wrapper">
								<input type="text" class="file-path">
							</div>
						</div>
						<div class="switch col s12">
							<?php
								if(isset($_POST['add-rare'])) {
									if(isset($limitedState)) {
										$limitedState = 1;
									}	else {
										$limitedState = 0;
									}
								}
							?>
							<label>
								Ilimitado
								<input type="checkbox" name="limited-state">
								<span class="lever"></span>
								Limitado
							</label>
						</div>
						<div class="col s12 button-margin">
							<?php
								if(isset($_POST['add-rare'])) {
									if(!empty($rareName) AND $rarePrice >= 0 OR is_numeric($rarePrice) AND !empty($file_path) AND !empty($limitedState)) {
										$addRare_Query = "INSERT INTO rares (nombre, precio, imagen, limitado) VALUES (?, ?, ?, ?)";
										$addRare = $db->prepare($addRare_Query);

										$addRare->execute(array($rareName, $rarePrice, $file_path, $limitedState));
									}
								}
							?>
							<button type="submit" class="btn waves-effect waves-light" name="add-rare">Añadir</button>	
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>