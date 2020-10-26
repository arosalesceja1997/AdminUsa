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

	include "inc/head.php";
	include "inc/nav.php";

	$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

	$start = ($page > 1) ? ($page * 20) - 20 : 0;

	if($page < 1) {
		echo '<script>window.location.href="/admin/mensajes";</script>';	
	}
?>

		<div class="container">
			<div class="section">
				<h5 class="section-title">Mensajes</h5>
				
				<ul class="collapsible popout" data-collapsible="accordion">
					<?php
						$getAllMessages_Query = "SELECT SQL_CALC_FOUND_ROWS * FROM mensajes ORDER BY id DESC LIMIT {$start}, 20";
						$getAllMessages = $db->prepare($getAllMessages_Query);

						$getAllMessages->execute();

						$total = $db->query("SELECT FOUND_ROWS() as total")->fetch()['total'];
						$pages = ceil($total / 20);

						while($row = $getAllMessages->fetch(PDO::FETCH_OBJ)) {
							$messageName = $row->nombre;
							$messageEmail = $row->correo;
							$messageRank = $row->rango;
							$message = $row->mensaje;

							echo '<li><div class="collapsible-header"><i class="fa fa-envelope"></i> Mensaje enviado por: '.$messageName.'</div>
									<div class="collapsible-body">
										<p>Correo electrónico: '.$messageEmail.'<br />
										Rango: '.$messageRank.'</p>

										<p>'.$message.'</p>
									</div></li>';
						}

						if($page * 20 > $total + 20) {
							echo '<script>window.location.href="/admin/mensajes";</script>';
						}
					?>
				</ul>
				
				<ul class="pagination">
				<?php for($x = 1; $x <= $pages; $x++) : ?>
					<li class="waves-effect<?php if($page === $x) { echo ' active'; } ?>"><a href="?page=<?php echo $x; ?>"><?php echo $x; ?></a></li>
				<?php endfor; ?>
				</ul>
			</div>
		</div>
	</body>
</html>