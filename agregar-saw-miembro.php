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
	}	else if($_SESSION['user_rank'] != "Reporteros" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Coordinación") {
		header("Location: /admin");
	}

	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Añadir SAW miembro</h5>

				<div class="row">
					<form action="" method="post" enctype="multipart/form-data">
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['add-member'])) {
									$name = $_POST['name'];
									$nameError = '<p class="panel-error">Tienes que insertar un nombre</p>';
									checkEmptyInput($name, $nameError);
								}
							?>
							<input type="text" id="name" name="name">
							<label for="name" <?php if(isset($name)) { echo 'class="active"'; } ?>>Nombre</label>
						</div>
						<div class="input col s12 m6">
							<?php
								if(isset($_POST['add-member'])) {
									$rank = $_POST['rank'];
									$rankError = '<p class="panel-error">Tienes que elegir un rango</p>';
									checkEmptyInput($rank, $rankError);
								}
							?>
							<select name="rank">
								<option value="" disabled selected>Selecciona el rango</option>
								<?php
									$getAllSAWRanks_Query = "SELECT nombre FROM saw_rangos";
									$getAllSAWRanks = $db->prepare($getAllSAWRanks_Query);

									$getAllSAWRanks->execute();
									while($rankRow = $getAllSAWRanks->fetch(PDO::FETCH_OBJ)) {
										$rankName = $rankRow->nombre;
										echo '<option value="'.$rankName.'">'.$rankName.'</option>';
									}
								?>
							</select>
						</div>
						<div class="col s12">
							<?php
								if(isset($_POST['add-member'])) {
									if(isset($_FILES['member-img'])) {
										if(empty($_FILES['member-img']['name'])) {
											echo '<p class="panel-error">Tienes que subir una imagen para el miembro</p>';
										}	else {
											$allowedFormat = array(
												'jpg',
												'jpeg',
												'gif',
												'png'
											);

											$file_name = $_FILES['member-img']['name'];
											$file_extn = strtolower(end(explode('.', $file_name)));
											$file_loc = $_FILES['member-img']['tmp_name'];


											if(in_array($file_extn, $allowedFormat) AND !empty($name) AND !empty($rank)) {
												$file_path = '../imagenes/saw-equipo/'.substr(md5(time()), 0, 10).'.'.$file_extn;
												move_uploaded_file($file_loc, $file_path);
											}	else if(!in_array($file_extn, $allowedFormat)) {
												echo '<p class="panel-error">Sólo puedes subir una imagen en uno de estos formatos: ';
													echo implode(', ', $allowedFormat)."</p>";
											}

										}

									}
								}
							?>
							<div class="file-field input-field">
								<div class="btn waves-effect waves-light">
									<span>Subir imagen</span>
									<input type="file" name="member-img">
								</div>
								<div class="file-path-wrapper">
									<input type="text" class="file-path">
								</div>
							</div>
						</div>
						<div class="col s12">
							<?php
								if(isset($_POST['add-member']) AND !empty($name) AND !empty($rank) AND !empty($file_path)) {
									$addMember_Query = "INSERT INTO saw_miembros (nombre, rango, imagen) VALUES (?, ?, ?)";
									$addMember = $db->prepare($addMember_Query);

									$addMember->execute(array($name, $rank, $file_path));
									echo '<script>window.location="/admin/agregar-saw-miembro"</script>';

								}
							?>
							<button type="submit" class="btn waves-effect waves-light input-margin" name="add-member">Añadir</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>