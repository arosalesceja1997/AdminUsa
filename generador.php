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
				<h5 class="section-title">Generador de Kekos by José Reduus</h5>
				
				<div class="cont_cent_p">   
            <div class="box_ghabbo">
                <div class="box_usuarios_g">
                    <div id="lista_usuarios"></div>
                    <div id="nuevo_us" onclick="nuevoUsuario();"></div>
                    <div id="cont_usuarios"></div>
                </div>
            </div>
            <div class="box_ghabbo">
                <div class="campo"><input id="habbo" type="text" maxlength="15" placeholder="Hobba..."></div>
                <div id="borrar_us" onclick="borrarUsuario();"></div>
            </div>
            <div class="box_ghabbo">
                <div id="cont_g" class="cont_gen">
                    <div class="desc_genh">
                        <div class="tip">
                            <div class="f_izq" onclick="cambiarHabbo('i', 'body');"></div>
                            <div class="f_der" onclick="cambiarHabbo('d', 'body');"></div>
                        </div>
                        <div class="tip">
                            <div class="f_izq" onclick="cambiarHabbo('i', 'head');"></div>
                            <div class="f_der" onclick="cambiarHabbo('d', 'head');"></div>
                        </div>
                        <div class="tip">
                            <div class="f_izq" onclick="cambiarHabbo('i', 'gesture');"></div>
                            <div class="f_der" onclick="cambiarHabbo('d', 'gesture');"></div>
                        </div>
                        <div class="tip">
                            <div class="f_izq" onclick="cambiarHabbo('i', 'action');"></div>
                            <div class="f_der" onclick="cambiarHabbo('d', 'action');"></div>
                        </div>
                        <div class="tip">
                            <div class="f_izq" onclick="cambiarHabbo('i', 'size');"></div>
                            <div class="f_der" onclick="cambiarHabbo('d', 'size');"></div>
                        </div>
                    </div>
                    <div class="box_prev_gen">
                        <img src="" alt="" id="prev_ghabbo">
                    </div>
                </div>
            </div>
            <div class="box_ghabbo copy">Powered by SoyJoaquin. & Adapted by José Reduus</div>
        </div>
		
			</div>
		</div>
	</body>
</html>