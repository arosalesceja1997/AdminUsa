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
		echo '<script>window.location="/admin/saw-miembros"</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getSelectedSAWMember_Query = "SELECT * FROM saw_miembros WHERE id = ?";
	$getSelectedSAWMember = $db->prepare($getSelectedSAWMember_Query);

	$getSelectedSAWMember->execute(array($members_id));
	$usersRow = $getSelectedSAWMember->fetch(PDO::FETCH_OBJ);

	$usersUsername = $usersRow->nombre;
	$usersRank = $usersRow->rango;
	$usersImg = $usersRow->imagen;
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Editar Hobba miembro</h5>

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
							<input type="text" id="name" name="name" value="<?php echo $usersUsername; ?>">
							<label for="name" class="active">Nombre</label>
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
								<?php
									$getAllSAWRanks_Query = "SELECT nombre FROM saw_rangos";
									$getAllSAWRanks = $db->prepare($getAllSAWRanks_Query);

									$getAllSAWRanks->execute();
									while($rankRow = $getAllSAWRanks->fetch(PDO::FETCH_OBJ)) {
										$rankName = $rankRow->nombre;
										if($usersRank == $rankName) {
											$selected = "selected";
										}	else {
											$selected = "";
										}
										echo '<option value="'.$rankName.'" '.$selected.'>'.$rankName.'</option>';
									}
								?>
							</select>
						</div>
						<div class="col s12">
							<?php
								echo '<div class="users-panel-img"><img src="'.$usersImg.'" alt="'.$usersUsername.'"></div>';
								if(isset($_POST['add-member'])) {
									if(isset($_FILES['member-img'])) {
										$allowedFormat = array(
											'jpg',
											'jpeg',
											'gif',
											'png'
										);

										$file_name = $_FILES['member-img']['name'];
										$file_extn = strtolower(end(explode('.', $file_name)));
										$file_loc = $_FILES['member-img']['tmp_name'];


										if(isset($_FILES['member-img']['name']) AND !empty($_FILES['member-img']['name']) AND in_array($file_extn, $allowedFormat)) {
											$file_path = '../imagenes/saw-equipo/'.substr(md5(time()), 0, 10).'.'.$file_extn;
											move_uploaded_file($file_loc, $file_path);
										}	else if(isset($_FILES['member-img']['name']) AND !empty($_FILES['member-img']['name']) AND !in_array($file_extn, $allowedFormat)) {
											echo '<p class="panel-error">Sólo puedes subir una imagen en uno de estos formatos: ';
												echo implode(', ', $allowedFormat)."</p>";
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
									$addMember_Query = "UPDATE saw_miembros SET nombre = ?, rango = ?, imagen = ? WHERE id = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($name, $rank, $file_path, $members_id));
									echo '<script>window.location="/admin/editar-saw-miembro?id='.$members_id.'"</script>';
								}	else if(isset($_POST['add-member']) AND !empty($name) AND !empty($rank)) {
									$addMember_Query = "UPDATE saw_miembros SET nombre = ?, rango = ? WHERE id = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($name, $rank, $members_id));
									echo '<script>window.location="/admin/editar-saw-miembro?id='.$members_id.'"</script>';
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