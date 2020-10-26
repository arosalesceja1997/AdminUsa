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
				<h5 class="section-title">Lista de logros</h5>
				<a href="agregar-logro" class="btn waves-effect waves-light">Crear nuevo logro</a>
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
						<td>Código</td>
						<td>Título</td>
						<td>Descripción</td>
						<td>Logro</td>
						<td>Editar</td>
						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><td>Eliminar</td><?php endif; ?>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['noticia'])) {
							$getAllNews_Query = "SELECT * FROM habbo_placas ORDER BY id ASC";
							$getAllNews = $db->prepare($getAllNews_Query);

							$getAllNews->execute();

							while($row = $getAllNews->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$code = $row->codigo;
								$titulo = $row->titulo;
								$desc = $row->descripcion;
								$imagen = $row->imagen;

								echo '<tr>
									<td>'.$id.'</td>
									<td>'.$code.'</td>
									<td>'.$titulo.'</td>
									<td>'.$desc.'</td>
									<td><img src="'.$imagen.'"></img></td>
									<td><a href="/admin/editar-logro?id='.$id.'">Editar</a></td>';
										echo '<td><a href="eliminar-logro?id='.$id.'">Eliminar</a></td>';
								echo '</tr>';
							}

							if(isset($_POST['delete-news'])) {
								die($id);
								$deleteNews_Query = "DELETE FROM enables WHERE id = ?";
								$deleteNews = $db->prepare($deleteNews_Query);

								$deleteNews->execute(array($id));
								echo '<script>window.location.href="/admin/logros";</script>';
							}
						}

						if(isset($_GET['noticia']) AND empty($_GET['noticia'])) {
							echo '<script>window.location.href="/admin/logros";</script>';
						}


						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>