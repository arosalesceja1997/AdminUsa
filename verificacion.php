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
				<h5 class="section-title">Solicitudes de verificación</h5>
				
				<ul class="collapsible popout" data-collapsible="accordion">
					<?php
						$getAllMessages_Query = "SELECT SQL_CALC_FOUND_ROWS * FROM verificacion_solicitud WHERE aprobado = 0 ORDER BY id DESC LIMIT {$start}, 20";
						$getAllMessages = $db->prepare($getAllMessages_Query);

						$getAllMessages->execute();

						$total = $db->query("SELECT FOUND_ROWS() as total")->fetch()['total'];
						$pages = ceil($total / 20);

						while($row = $getAllMessages->fetch(PDO::FETCH_OBJ)) {
							$verificarId = $row->id;
							$usuarioHobba = $row->usuario_hobba;
							$usuarioLF = $row->usuario_lufantasie;
							$regalo = $row->regalo;
						?>
						<li><div class="collapsible-header"><i class="fa fa-envelope"></i> Solicitud de: <?php echo $usuarioLF; ?></div>
						<div class="collapsible-body">
						<p>Usuario de Hobba: <?php echo $usuarioHobba; ?><br />
						Usuario de LUFantasie: <?php echo $usuarioLF; ?><br /> 
					    ¿Envió regalo?: <?php echo $regalo;?><br>
					    	<?php if ($usuarioHobba == $usuarioLF): ?>
					    		<form method="post">
					    			<?php
								if(isset($_POST['verificar'])) {
									$aprobarVerificacion_Query = "UPDATE verificacion_solicitud SET aprobado = 1 WHERE id = ?";
									$aprobarVerificacion = $db->prepare($aprobarVerificacion_Query);
 
									$aprobarVerificacion->execute(array($verificarId));
									
									$addMember_Query = "UPDATE usuarios SET verificado = 1 WHERE nombre = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($usuarioHobba));
									echo '<script>window.location="verificacion"</script>';
								}	else if(isset($_POST['verificar'])) {
									$addMember_Query = "UPDATE usuarios SET verificado = 1 WHERE nombre = ?";
									$addMember = $db->prepare($addMember_Query);
 
									$addMember->execute(array($usuarioHobba));
									echo '<script>window.location="verificacion"</script>';

									
								}
							?>
									<button class="btn waves-effect waves-light" type="submit" name="verificar">Verificar</button>
								</form>	
					    	<?php endif ?>	
					    	<?php if ($usuarioHobba <> $usuarioLF): ?>
					    		<button class="btn waves-effect waves-light" type="text" disabled="">No se puede verificar</button>
					    	<?php endif ?>
						</p>
								
						</div></li>
						<?php
						}

						if($page * 20 > $total + 20) {
							echo '<script>window.location.href="/admin/verificacion";</script>';
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