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

		<div class="container">
			<div class="section">
				<h5 class="section-title">Calendario</h5>
				<a href="agregar-calendario" class="btn waves-effect waves-light">Crear nuevo día</a>

				<table class="striped responsive-table">
					<thead>
						<td>ID</td>
						<td>Día</td>
						<td>Editar</td>
						<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><td>Eliminar</td><?php endif; ?>
					</thead>
					<tbody>
						<?php
						if(!isset($_GET['noticia'])) {
							$getAllNews_Query = "SELECT * FROM calendario ORDER BY id DESC LIMIT 31";
							$getAllNews = $db->prepare($getAllNews_Query);

							$getAllNews->execute();

							while($row = $getAllNews->fetch(PDO::FETCH_OBJ)) {
								$id = $row->id;
								$dia = $row->numero_dia;
								$contenido = $row->contenido;

								echo '<tr>
									<td>#'.$id.'</td>
									<td> Día '.$dia.'</td>
									<td><a href="/admin/editar-calendario?id='.$id.'">Editar</a></td>';
									if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") {
										echo '<td><a href="eliminar-calendario
?id='.$id.'">Eliminar</a></td>';
									}
								echo '</tr>';
							}

							if(isset($_POST['delete-news'])) {
								die($id);
								$deleteNews_Query = "DELETE FROM calendario WHERE id = ?";
								$deleteNews = $db->prepare($deleteNews_Query);

								$deleteNews->execute(array($id));
								echo '<script>window.location.href="/admin/calendario";</script>';
							}
						}

						if(isset($_GET['noticia']) AND empty($_GET['noticia'])) {
							echo '<script>window.location.href="/admin/calendario";</script>';
						}

						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>