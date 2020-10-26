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
				<h5 class="section-title">Añadir handitem</h5>

				<div class="row">
					<form action="" method="post" enctype="multipart/form-data">
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['add-member'])) {
									$name = $_POST['name'];
									$nameError = '<p class="panel-error">Tienes que insertar un número de handitem</p>';
									checkEmptyInput($name, $nameError);
								}
							?>
							<input type="text" id="name" name="name">
							<label for="name">Número/nombre</label>
						</div>
						<div class="input col s12 m6">
							<?php
								if(isset($_POST['add-member'])) {
									$rank = $_POST['rank'];
									$rankError = '<p class="panel-error">Tienes que elegir una categoría</p>';
									checkEmptyInput($rank, $rankError);
								}
							?>
							<select name="rank">
								<option value="" disabled selected>Selecciona la categoría</option>
									<option value="Otros">Otros</option>
									<option value="Alimento">Alimento</option>
									<option value="Flores">Flores</option>
									<option value="Transform">Transform (caso especial)</option>
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
												$file_path = '../imagenes/handitems/'.substr(md5(time()), 0, 10).'.'.$file_extn;
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
								if(isset($_POST['add-member']) AND !empty($name) AND !empty($file_path) AND !empty($rank)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> agregó un comando :handitem";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$addMember_Query = "INSERT INTO handitems (handitem, imagen, categoria) VALUES (?, ?, ?)";
									$addMember = $db->prepare($addMember_Query);

									$addMember->execute(array($name, $file_path, $rank));
									echo '<script>window.location="/admin/lista-handitems"</script>';

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