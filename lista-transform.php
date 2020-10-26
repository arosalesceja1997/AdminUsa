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
				<h5 class="section-title">Lista de transform</h5>
				<a href="agregar-transform" class="btn waves-effect waves-light">Crear transform</a>
				<?php
					if(isset($_POST['search-user'])) {
						$newsSearched = $_POST['news'];
						echo '<script>window.location.href="/admin/noticias?noticia='.$newsSearched.'";</script>';
					}
				?>
				<form action="" method="post">
				<div class="row">

				</div>
				</form>

				<table class="striped responsive-table">
					<thead>
						<td>ID</td>
						<td>Transform</td>
						<td>Editar</td>
						<?php if($_SESSION['user_rank'] == "Administradores" OR $_SESSION['user_rank'] == "Colaboradores") : ?><td>Eliminar</td><?php endif; ?>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['noticia'])) {
							$getAllNews_Query = "SELECT * FROM transform ORDER BY id ASC";
							$getAllNews = $db->prepare($getAllNews_Query);

							$getAllNews->execute();

							while($row = $getAllNews->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$title = $row->transform;

								echo '<tr>
									<td>'.$id.'</td>
									<td>'.$title.'</td>
									<td><a href="/admin/editar-transform?id='.$id.'">Editar</a></td>';
										echo '<td><a href="eliminar-transform?id='.$id.'">Eliminar</a></td>';
								echo '</tr>';
							}

							if(isset($_POST['delete-news'])) {
								die($id);
								$deleteNews_Query = "DELETE FROM transform WHERE id = ?";
								$deleteNews = $db->prepare($deleteNews_Query);

								$deleteNews->execute(array($id));
								echo '<script>window.location.href="/admin/lista-transform";</script>';
							}
						}

						if(isset($_GET['noticia']) AND empty($_GET['noticia'])) {
							echo '<script>window.location.href="/admin/lista-enables";</script>';
						}


						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>