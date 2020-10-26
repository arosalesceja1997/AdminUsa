<?php
	session_start();

	require_once "../inc/connection.php";
	require_once "../inc/functions.php";

	if(!isset($_SESSION['user_id']) OR $_SESSION['userAllowed'] != true) {
		header("Location: /");
	}	else if($_SESSION['user_rank'] != "Equipo de Información" AND $_SESSION['user_rank'] != "Administrador" AND $_SESSION['user_rank'] != "Encargados" AND $_SESSION['user_rank'] != "Coordinación") {
		header("Location: /admin");
	}
	include "inc/head.php";
	include "inc/nav.php";
?>

				<?php
					$userAprobed_Query = "SELECT * FROM usuarios WHERE id = ?";
					$userAprobed = $db->prepare($userAprobed_Query);

					$userAprobed->execute(array($_SESSION['user_id']));

					while($row = $userAprobed->fetch(PDO::FETCH_OBJ)) {
						$lfid = $row->id;
						$nombreReportero = $row->nombre;
						$nombreCEO = $row->ceo;
					}
					
				?>


		<div class="container">
			<div class="section">
				<h5 class="section-title">Noticias pendientes de revisión</h5>

				<table class="striped responsive-table">
					<thead>
						<td>ID</td>
						<td>Título</td>
						<td>Categoría</td>
						<td>Publicado</td>
						<td>Autor</td>
						<td>Editar</td>
						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><td>Eliminar</td><?php endif; ?>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['noticia'])) {
							$getAllNews_Query = "SELECT id, titulo, categoria, publicado, autor FROM noticias WHERE aprobado = 0 ORDER BY id DESC LIMIT 10";
							$getAllNews = $db->prepare($getAllNews_Query);

							$getAllNews->execute();

							while($row = $getAllNews->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$title = $row->titulo;
								$category = $row->categoria;
								$published = $row->publicado;
								$author = $row->autor;

								echo '<tr>
									<td>'.$id.'</td>
									<td>'.$title.'</td>
									<td>'.$category.'</td>
									<td>'.$published.'</td>
									<td>'.$author.'</td>
									';
									if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $nombreCEO == 'user_reportero') {
										echo '<td><a href="verificar-noticia?id='.$id.'">Aprobar</a></td>';
									}
									if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") {
										echo '<td><a href="eliminar-noticia?id='.$id.'">Eliminar</a></td>';
									}
								echo '</tr>';
							}

							if(isset($_POST['delete-news'])) {
								die($id);
								$deleteNews_Query = "DELETE FROM noticias WHERE id = ?";
								$deleteNews = $db->prepare($deleteNews_Query);

								$deleteNews->execute(array($id));
								echo '<script>window.location.href="/admin/noticias";</script>';
							}
						}

						if(isset($_GET['noticia']) AND empty($_GET['noticia'])) {
							echo '<script>window.location.href="/admin/noticias";</script>';
						}

						if(isset($_GET['noticia'])) {
							$getSearchedNewsQuery = $_GET['noticia'];
							$getSearchedNews_Query = "SELECT id, titulo, categoria, publicado, autor FROM noticias WHERE titulo LIKE ? OR contenido LIKE ?";
							$getSearchedNews = $db->prepare($getSearchedNews_Query);

							$getSearchedNews->execute(array("%".$getSearchedNewsQuery."%", "%".$getSearchedNewsQuery."%"));

							while($usersRow = $getSearchedNews->fetch(PDO::FETCH_OBJ)) {
								$newsId = $usersRow->id;
								$newsTitle = $usersRow->titulo;
								$newsCategory = $usersRow->categoria;
								$newsPublishedDate = $usersRow->publicado;
								$newsAuthor = $usersRow->autor;

								echo '<tr>
									<td>'.$newsId.'</td>
									<td>'.$newsTitle.'</td>
									<td>'.$newsCategory.'</td>
									<td>'.$newsPublishedDate.'</td>
									<td>'.$newsAuthor.'</td>
									<td><a href="/admin/editar-noticia?id='.$newsId.'">Editar</a></td>';
									if($_SESSION['user_rank'] == "Administradores" OR $_SESSION['user_rank'] == "Colaboradores") {
										echo '<td><button data-target="'.$newsId.'" class="modal-trigger">Eliminar</a></td>';
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