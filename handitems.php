<?php
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
				<h5 class="section-title">Handitem</h5>
				<?php
					if(isset($_POST['search-user'])) {
						$newsSearched = $_POST['news'];
						echo '<script>window.location.href="/admin/handitems?id='.$newsSearched.'";</script>';
					}
				?>
				<form action="" method="post">
				<div class="row">
					<div class="input-field col s12 m9">
						<input type="text" name="news" id="news-search">
						<label for="news-search">Buscar usuario</label>
					</div>
					<div class="col s12 m3">
						<button type="submit" class="btn-large waves-effect waves-light full-width" name="search-user">Buscar</button>
					</div>
				</div>
				</form>

				<table class="striped responsive-table">
					<thead>
						<td>ID</td>
						<td>Nombre</td>
						<td>Descripción</td>
						<td>icono</td>
						<td>Orden</td>
						<td>Editar</td>
						<?php if($_SESSION['user_rank'] == "Administradores" OR $_SESSION['user_rank'] == "Colaboradores") : ?><td>Eliminar</td><?php endif; ?>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['user-lf'])) {
							$getAllNews_Query = "SELECT * FROM handitems_categorias ORDER BY id ASC LIMIT 10";
							$getAllNews = $db->prepare($getAllNews_Query);

							$getAllNews->execute();

							while($row = $getAllNews->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$nombre = $row->nombre;
								$descripcion = $row->descripcion;
								$icono = $row->icono;
								$orden = $row->orden;
								

								echo '<tr>
									<td>'.$id.'</td>
									<td>'.$nombre.'</td>
									<td>'.$descripcion.'</td>
									<td>'.$icono.'</td>
									<td>'.$orden.'</td>
									<td><a href="/admin/editar-handitems?id='.$id.'">Editar</a></td>';
									if($_SESSION['user_rank'] == "Administradores" OR $_SESSION['user_rank'] == "Colaboradores") {
										echo '<td><a href="eliminar-noticia?id='.$id.'">Eliminar</a></td>';
									}
								echo '</tr>';
							}

							if(isset($_POST['delete-news'])) {
								die($id);
								$deleteNews_Query = "DELETE FROM handitems_categorias WHERE id = ?";
								$deleteNews = $db->prepare($deleteNews_Query);

								$deleteNews->execute(array($id));
								echo '<script>window.location.href="/admin/handitems";</script>';
							}
						}

						if(isset($_GET['user-lf']) AND empty($_GET['user-lf'])) {
							echo '<script>window.location.href="/admin/handitems";</script>';
						}

						if(isset($_GET['search-user'])) {
							$getSearchedNewsQuery = $_GET['news'];
							$getSearchedNews_Query = "SELECT * FROM handitems_categorias WHERE nombre = '$getSearchedNewsQuery'";
							$getSearchedNews = $db->prepare($getSearchedNews_Query);

							$getSearchedNews->execute(array("%".$getSearchedNewsQuery."%", "%".$getSearchedNewsQuery."%"));

							while($row = $getAllNews->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$nombre = $row->nombre;
								$descripcion = $row->descripcion;
								$icono = $row->icono;
								$orden = $row->orden;
								

								echo '<tr>
									<td>'.$id.'</td>
									<td>'.$nombre.'</td>
									<td>'.$descripcion.'</td>
									<td>'.$icono.'</td>
									<td>'.$orden.'</td>
									<td><a href="/admin/editar-usuario?id='.$id.'">Editar</a></td>';
									if($_SESSION['user_rank'] == "Administradores" OR $_SESSION['user_rank'] == "Colaboradores") {
										echo '<td><a href="eliminar-noticia?id='.$id.'">Eliminar</a></td>';
									}
								echo '</tr>';
							}

							if($getSearchedNews->rowCount() < 1) {
								echo '<h3 style="font-weight: 200;">No hay handitem similar a "<span style="font-weight: 500;">'.$getSearchedNewsQuery.'</span>"</h3>';
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>