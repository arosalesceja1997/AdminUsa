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
		echo '<script>window.location="/admin/lista-enables"</script>';
	}

	include "inc/head.php";
	include "inc/nav.php";

	$getSelectedHobbaMember_Query = "SELECT * FROM enables WHERE id = ?";
	$getSelectedHobbaMember = $db->prepare($getSelectedHobbaMember_Query);

	$getSelectedHobbaMember->execute(array($members_id));
	$usersRow = $getSelectedHobbaMember->fetch(PDO::FETCH_OBJ);

	$usersUsername = $usersRow->id;
	$usersRank = $usersRow->enable;
	$usersImg = $usersRow->imagen;
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Editar enable <?php echo $usersRank;?></h5>

				<div class="row">
					<form action="" method="post" enctype="multipart/form-data">
						<div class="input-field col s12 m6">
							<?php
								if(isset($_POST['add-member'])) {
									$name = $_POST['name'];
									$nameError = '<p class="panel-error">Tienes que insertar un  número de enable</p>';
									checkEmptyInput($name, $nameError);
								}
							?>
							<input type="text" id="name" name="name" value="<?php echo $usersRank; ?>">
							<label for="name" class="active">Número de enable</label>
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
											$file_path = '../imagenes/enables/'.substr(md5(time()), 0, 10).'.'.$file_extn;
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
								if(isset($_POST['add-member']) AND !empty($name) AND !empty($file_path)) {
									$addMember_Query = "UPDATE enables SET enable = ?, imagen = ? WHERE id = ?";
									$addMember = $db->prepare($addMember_Query);
 
								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> editó un comando :enable";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$addMember->execute(array($name, $file_path, $members_id));
									echo '<script>window.location="/admin/editar-enable?id='.$members_id.'"</script>';
								}	else if(isset($_POST['add-member']) AND !empty($name)) {

								$mensajeLogs = "<strong>".$_SESSION['username']."</strong> editó un comando :enable";
								$logs_Query = "INSERT INTO logs (mensaje, usuario) VALUES (?, ?)";
								$logs = $db->prepare($logs_Query);
								$logs->execute(array($mensajeLogs, $_SESSION['username']));	

									$addMember_Query = "UPDATE enables SET enable = ?, imagen = ? WHERE id = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($name, $members_id));
									echo '<script>window.location="/admin/editar-enable?id='.$members_id.'"</script>';
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