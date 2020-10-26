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
		$members_id = (int)$_GET['id'];
	}	else {
		echo '<script>window.location="/admin/logros"</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getSelectedHobbaMember_Query = "SELECT * FROM habbo_placas WHERE id = ?";
	$getSelectedHobbaMember = $db->prepare($getSelectedHobbaMember_Query);

	$getSelectedHobbaMember->execute(array($members_id));
	$usersRow = $getSelectedHobbaMember->fetch(PDO::FETCH_OBJ);

	$usersUsername = $usersRow->id;
	$code = $usersRow->codigo;
	$title = $usersRow->titulo;
	$desc = $usersRow->descripcion;
	$imagen = $usersRow->imagen;
	
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Estás editando el logro:  <?php echo $code;?></h5>

				<div class="row">
					<form action="" method="post" enctype="multipart/form-data">
						<div class="input-field col s12">
							<?php
								if(isset($_POST['add-member'])) {
									$titulo = $_POST['titulo'];
									$tituloError = '<p class="panel-error">Tienes que insertar un título para este logro...</p>';
									checkEmptyInput($titulo, $tituloError);
								}
							?>
							<input type="text" id="name" name="titulo" value="<?php echo $title; ?>">
							<label for="name" class="active">Título de logro</label>
						</div>
						<div class="input-field col s12">
							<?php
								if(isset($_POST['add-member'])) {
									$desc = $_POST['desc'];
									$descError = '<p class="panel-error">Tienes que insertar una descripción para este logro...</p>';
									checkEmptyInput($desc, $descError);
								}
							?>
							<input type="text" id="name" name="desc" value="<?php echo $desc; ?>">
							<label for="name" class="active">Descripción de logro</label>
						</div>
						<div class="input-field col s12">
							<?php
								if(isset($_POST['add-member'])) {
									$imagen = $_POST['imagen'];
									$imagenError = '<p class="panel-error">Tienes que insertar una imagen para este logro...</p>';
									checkEmptyInput($imagen, $imagenError);
								}
							?>
							<input type="text" id="name" name="imagen" value="<?php echo $imagen; ?>">
							<label for="name" class="active">Imagen de logro</label>
						</div>
						
						<div class="col s12">
							<?php
								if(isset($_POST['add-member']) AND !empty($titulo) AND !empty($desc) AND !empty($imagen)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> editó un LUF Logro";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$addMember_Query = "UPDATE habbo_placas SET titulo = ?, descripcion = ?, imagen = ? WHERE id = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($titulo, $desc, $imagen, $members_id));
									echo '<script>window.location="/admin/logros"</script>';
								}	else if(isset($_POST['add-member']) AND !empty($titulo) AND !empty($desc) AND !empty($imagen)) {
									$addMember_Query = "UPDATE habbo_placas SET titulo = ?, descripcion = ?, imagen = ? WHERE id = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($titulo, $desc, $imagen, $members_id));
									echo 'Has edito correctamente este logro';
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