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
				<h5 class="section-title">Añadir miembro a LUFantasie</h5>

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
									$getAllSAWRanks_Query = "SELECT nombre FROM rangos";
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
							if(isset($_POST['publish-news'])) {
								$author = $_POST['author'];
								$authorError = '<p class="panel-error">Tiene que haber una imagen</p>';
								checkEmptyInput($author, $authorError);
							}
						?>
						<div class="input-field col s12 m12">
							<input type="text" value="" id="author" name="author">
							<label for="autor">Imagen</label>
						</div>
						</div>
						<div class="col s12">
							<?php
								if(isset($_POST['add-member']) AND !empty($name) AND !empty($rank) AND !empty($file_path)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> le dio rango a ".$name." de ".$rank."";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

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