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


	include "inc/head.php";
	include "inc/nav.php";
?>

		<div class="container">
			<div class="section">
				<h4 class="section-title">Banners</h5>
				
				<h3 class="section-title">GATO TE VE OK</h3>
				<img src="https://i.imgur.com/XTs4lbG.png" alt="">


				<h3 class="section-title">LTD</h3>
				<img src="https://i.imgur.com/fNFd05p.png" alt="">
				<img src="https://i.imgur.com/ZAw8iPd.png" alt="">
				<img src="https://i.imgur.com/OFaWo92.png" alt="">
				<img src="https://i.imgur.com/0fqqm76.png" alt="">

				<h3 class="section-title">Nueva noticia</h3>
				<img src="https://i.imgur.com/1U0CNJY.png" alt="">
				<img src="https://i.imgur.com/bsV2Y58.png" alt="">
				<img src="https://i.imgur.com/GgmnVRk.png" alt="">
				<img src="https://i.imgur.com/qGw53Bp.png" alt="">

				<h3 class="section-title">Equipo Hobba</h3>
				<img src="https://i.imgur.com/elQlgoj.png" alt="">
				<img src="https://i.imgur.com/turZbpI.png">
				<img src="https://i.imgur.com/hVTDvMl.png" alt="">
				<img src="https://i.imgur.com/jJnT0AD.png" alt="">
				<img src="https://i.imgur.com/zEJ5yb1.png" alt="">
				<img src="https://i.imgur.com/mYij4DR.png" alt="">
				<img src="https://i.imgur.com/LZrvszw.png" alt="">	
				<img src="https://i.imgur.com/QDktpAd.png" alt="">
				<img src="https://i.imgur.com/oTqHu8I.png">
				<img src="https://i.imgur.com/0anE3mb.png" alt="">
				

				<h3 class="section-title">Expulsiones Hobba</h3>
				<img src="https://i.imgur.com/yoLhDYP.png" alt="">
				<img src="https://i.imgur.com/hr9dNoE.png" alt="">
				

				<h3 class="section-title">Más novedades</h3>
				<img src="https://i.imgur.com/lxgq1XY.png" alt="">
				<img src="https://i.imgur.com/zLRu9zQ.png">
				<img src="https://i.imgur.com/9wyUznX.png" alt="">
				<img src="https://i.imgur.com/SAridXn.png">

				<h3 class="section-title">Gymkhana</h3>
				<img src="https://i.imgur.com/0spth3z.png" alt="">

				<h3 class="section-title">ROTW Y GOTW</h3>
				<img src="https://i.imgur.com/DhztlXe.png" alt="">
				<img src="https://i.imgur.com/iS6VTpe.png" alt="">
				<h3 class="section-title">Gymkhana</h3>
				<img src="" alt="">

				<h3 class="section-title">Furnis</h3>
				<img src="https://i.imgur.com/b8iqy0i.png" alt="">
				<img src="https://i.imgur.com/PPE2ufj.png" alt="">

				<h3 class="section-title">Concursos</h3>
				<img src="https://i.imgur.com/YBrFn41.png" alt="">
				<img src="https://i.imgur.com/0PmtYnH.png" alt="">

				<h3 class="section-title">Resumen Semanal</h3>
				<img src="https://i.imgur.com/Eulc5jw.png" alt="">
				<img src="https://i.imgur.com/xSZjfsg.png" alt="">
				<img src="https://i.imgur.com/0NW7RPs.png" alt="">
				<img src="https://i.imgur.com/TnXYcQq.png" alt="">

				<h3 class="section-title">Ganadores</h3>
				<img src="https://i.imgur.com/h8CPGfi.png" alt="">
				<img src="https://i.imgur.com/OQAqV3C.png" alt="">

				<h3 class="section-title">Build Wars</h3>
				<img src="https://i.imgur.com/6jFj6Uv.png" alt="">

				<h3 class="section-title">Nuevo Evento</h3>
				<img src="https://i.imgur.com/nnLuzQh.png" alt="">
				<img src="https://i.imgur.com/O5oAAaD.png" alt="">

				<h3 class="section-title">Eventos Hobba</h3>
				<img src="https://i.imgur.com/tpK5UJb.png" alt="">
				<img src="https://i.imgur.com/W51T3DQ.png" alt="">
				<img src="https://i.imgur.com/8ZtIfUk.png" alt="">
				

				<h3 class="section-title">Hobba VIP</h3>
				<img src="https://i.imgur.com/LFOW50A.png" alt="">
				<img src="https://i.imgur.com/HZSFEpL.png" alt="">
				<img src="https://i.imgur.com/D8vLsPV.png" alt="">
				<img src="" alt="">


				
			</div>
		</div>
	</body>
</html>