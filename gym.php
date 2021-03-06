﻿<?php
	session_start();

	require_once "../inc/connection.php";
	require_once "../inc/functions.php";

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
				<h5 class="section-title">Puntos de Gymkhana</h5>
				<a href="agregar-gym" class="btn waves-effect waves-light">Crear un usuario</a>
				<?php
					if(isset($_POST['search-user'])) {
						$newsSearched = $_POST['news'];
						echo '<script>window.location.href="/admin/usuarios?user-lf='.$newsSearched.'";</script>';
					}
				?>
				<form action="" method="post">
				<div class="row">
					<div class="input-field col s12 m9">
						<input type="text" name="news" id="news-search">
						<label for="news-search">Buscar usuario</label>
					</div>
					<div class="col s12 m3">
						<button type="submit" class="btn-large waves-effect waves-light full-width" name="search-user" disabled>Buscar</button>
					</div>
				</div>
				</form>

				<table class="striped responsive-table">
					<thead>
						<td>ID</td>
						<td>Usuario	</td>
						<td>Puntaje</td>
						<td>Imagen</td>
						<td>Editar</td>
						<td>Eliminar</td>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['user-lf'])) {
							$getAllNews_Query = "SELECT * FROM hobba_gym ORDER BY id ASC";
							$getAllNews = $db->prepare($getAllNews_Query);

							$getAllNews->execute();

							while($row = $getAllNews->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$username = $row->usuario;
								$puntaje = $row->puntos;
								$imagen = $row->imagen;
								

								echo '<tr>
									<td>'.$id.'</td>
									<td>'.$username.'</td>
									<td>'.$puntaje.'</td>
									<td><img src="'.$imagen.'"></td>
									<td><a href="/admin/gym-edit?id='.$id.'">Editar</a></td>';

										echo '<td><a href="eliminar-gym?id='.$id.'">Eliminar</a></td>';
									
								echo '</tr>';
							}

							if(isset($_POST['delete-news'])) {
								die($id);
								$deleteNews_Query = "DELETE FROM hobba_gym WHERE id = ?";
								$deleteNews = $db->prepare($deleteNews_Query);

								$deleteNews->execute(array($id));
								echo '<script>window.location.href="/admin/noticias";</script>';
							}
						}

						if(isset($_GET['user-lf']) AND empty($_GET['user-lf'])) {
							echo '<script>window.location.href="/admin/noticias";</script>';
						}

						if(isset($_GET['user-lf'])) {
							$getSearchedNewsQuery = $_GET['user-lf'];
							$getSearchedNews_Query = "SELECT * FROM usuarios WHERE nombre = '$getSearchedNewsQuery'";
							$getSearchedNews = $db->prepare($getSearchedNews_Query);

							$getSearchedNews->execute(array("%".$getSearchedNewsQuery."%", "%".$getSearchedNewsQuery."%"));

							while($usersRow = $getSearchedNews->fetch(PDO::FETCH_OBJ)) {
								$id = $usersRow->id;
								$nombre = $usersRow->nombre;
								$correo = $usersRow->correo_e;
								$fecha = $usersRow->fecha_de_nacimiento;
								$rango = $usersRow->rango;
								$perlas = $usersRow->perlas;
								$ban = $usersRow->baneado;

								echo '<tr>
									<td>'.$id.'</td>
									<td>'.$nombre.'</td>
									<td>'.$correo.'</td>
									<td>'.$fecha.'</td>
									<td>'.$rango.'</td>
									<td>'.$perlas.'</td>
									<td>'.$ban.'</td>

									<td><a href="/admin/editar-usuario?id='.$id.'">Editar</a></td>';
									if($_SESSION['user_rank'] == "Administradores" OR $_SESSION['user_rank'] == "Colaboradores") {
										echo '<td><a href="eliminar-noticia?id='.$id.'">Eliminar</a></td>';
									}
								echo '</tr>';
							}

							if($getSearchedNews->rowCount() < 1) {
								echo '<h3 style="font-weight: 200;">No hay usuarios similar a "<span style="font-weight: 500;">'.$getSearchedNewsQuery.'</span>"</h3>';
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>