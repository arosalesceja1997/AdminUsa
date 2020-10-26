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

	if(isset($_GET['id']) AND !empty($_GET['id'])) {
		$members_id = (int)$_GET['id'];
	}	else {
		echo '<script>window.location="/admin/hobba-miembros"</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getSelectedHobbaMember_Query = "SELECT * FROM furnis_nuevos WHERE id = ?";
	$getSelectedHobbaMember = $db->prepare($getSelectedHobbaMember_Query);

	$getSelectedHobbaMember->execute(array($members_id));
	$usersRow = $getSelectedHobbaMember->fetch(PDO::FETCH_OBJ);

	$nombre = $usersRow->nombre;
	$descripcion = $usersRow->descripcion;
	$imagen = $usersRow->imagen;
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Añadir furni nuevo</h5>

				<div class="row">
					<form action="" method="post" enctype="multipart/form-data">
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['add-member'])) {
									$name = $_POST['name'];
									$nameError = '<p class="panel-error">Tienes que insertar nombre de furni</p>';
									checkEmptyInput($name, $nameError);
								}
							?>
							<input type="text" id="name" name="name" value="<?php echo $usersRank; ?>">
							<label for="name">Nombre</label>
						</div>

						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['add-member'])) {
									$descripcion = $_POST['descripcion'];
									$descripcionError = '<p class="panel-error">Tienes que insertar una descripción de furni</p>';
									checkEmptyInput($descripcion, $descripcionError);
								}
							?>
							<input type="text" id="name" name="descripcion">
							<label for="name">Descripción</label>
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


											if(in_array($file_extn, $allowedFormat) AND !empty($name) AND !empty($descripcion)) {
												$file_path = '../imagenes/furnis_nuevos/'.substr(md5(time()), 0, 10).'.'.$file_extn;
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
								if(isset($_POST['add-member']) AND !empty($name) AND !empty($descripcion) AND !empty($file_path)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> editó un furni";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$addMember_Query = "UPDATE furnis_nuevos SET nombre = ?, descripcion = ?, imagen = ? WHERE id = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($name, $file_path, $rank, $members_id));
									echo '<script>window.location="/admin/editar-furni?id='.$members_id.'"</script>';
								}	else if(isset(!empty($name) AND !empty($descripcion) AND !empty($file_path))) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> editó un furni";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$addMember_Query = "UPDATE furnis_nuevos SET nombre = ?, descripcion = ?, imagen = ? WHERE id = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($name, $rank, $members_id));
									echo '<script>window.location="/admin/editar-furni?id='.$members_id.'"</script>';
								}
							?>
							<button type="submit" class="btn waves-effect waves-light input-margin" name="add-member">Cambiar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>