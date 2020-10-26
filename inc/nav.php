
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


	<ul id="user-dropdown" class="dropdown-content">
			<li><a href="/ajustes">Ajustes</a></li>
			<li class="divider"></li>
			<li><a href="/logout?logout">Cerrar Sesión</a></li>
		</ul>
		<nav class="white" id="main-nav">
			<div class="nav-wrapper">
				<a href="" class="left brand-logo openNav waves-effect waves-teal" data-activates="slide-out"><i class="fa fa-bars"></i></a>

				<ul id="nav-mobile" class="right hide-on-med-and-down">
					<li><a href="#" class="dropdown-button waves-effect waves-teal" data-activates="user-dropdown" data-beloworigin="true"><?php echo $_SESSION['username']; ?> <i class="fa fa-caret-down"></i></a></li>
				</ul>
			</div>

			<ul id="slide-out" class="side-nav">
				<div class="side-nav-header">
					<div class="row">
						<div class="col s4 staff-img">
							<img src="<?php echo "../".$_SESSION['user_img']; ?>" alt="">
						</div>
						<div class="col s8">
							<span class="black-text truncate">
								<?php echo $_SESSION['username']; ?>
							</span>
						</div>
					</div>
				</div>

				<div class="divider"></div>
				<li><a href="/admin" class="waves-effect waves-teal"><i class="fa fa-tachometer"></i> Dashboard</a></li>
				<?php if($_SESSION['user_rank'] == "Equipo de Entretenimiento" OR $_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Equipo de Redes Sociales") : ?><li><a href="/admin/usuarios" class="waves-effect waves-teal"><i class="fa fa-user"></i> Usuarios</a></li><?php endif; ?>

<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href=/admin/lista-enables class="waves-effect waves-teal"><i class="fa fa-cog"></i> Enables</a></li><?php endif; ?>

<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href=/admin/generador class="waves-effect waves-teal"><i class="fa fa-cog"></i> Generador</a></li><?php endif; ?>

<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href=/admin/verificacion class="waves-effect waves-teal"><i class="fa fa-cog"></i> Verificación</a></li><?php endif; ?>

<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href=/admin/lista-transform class="waves-effect waves-teal"><i class="fa fa-cog"></i> Transform</a></li><?php endif; ?>

<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li><a href=/admin/lista-handitems class="waves-effect waves-teal"><i class="fa fa-cog"></i> Handitems</a></li><?php endif; ?>

<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li><a href=/admin/lista-furnis class="waves-effect waves-teal"><i class="fa fa-list"></i> Furnis</a></li><?php endif; ?>

<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li><a href=/admin/gym class="waves-effect waves-teal"><i class="fa fa-cubes"></i> GYM</a></li><?php endif; ?>

<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li><a href=/admin/calendario class="waves-effect waves-teal"><i class="fa fa-calendar"></i> GYM</a></li><?php endif; ?>


				<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Equipo de Información" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href="#!" class="waves-effect waves-teal dropdown-button" data-activates="news-dropdown" data-beloworigin="true"><i class="fa fa-newspaper-o"></i> Noticias</a></li>
				<ul id="news-dropdown" class="dropdown-content no-padding">
					<li><a href="/admin/publicar-noticia">Publicar Noticia</a></li>
					<li><a href="/admin/noticias">Noticias</a></li>
					<?php if($nombreCEO == "user_reportero" OR $_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" ) : ?><li><a href="/admin/aprobar-noticia">Aprobar noticia</a></li><?php endif; ?>


				</ul><?php endif; ?>
				<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Equipo de Redes Sociales" OR $_SESSION['user_rank'] == "Game Managers") : ?><li><a href="/admin/eventos" class="waves-effect waves-teal dropdown-button" data-activates="events-dropdown" data-beloworigin="true"><i class="fa fa-calendar"></i> Eventos</a></li>
				<ul id="events-dropdown" class="dropdown-content no-padding">
					<li><a href="/admin/agregar-evento">Añadir evento</a></li>
					<li><a href="/admin/eventos">Eventos</a></li>
				</ul><?php endif; ?>
				<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href="#" class="waves-effect waves-teal dropdown-button" data-activates="hobba-dropdown" data-beloworigin="true"><i class="fa fa-h-square"></i> Equipo de Hobba</a></li>
				<ul id="hobba-dropdown" class="dropdown-content no-padding">
					<li><a href="/admin/agregar-hobba-miembro">Añadir miembro</a></li>
					<li><a href="/admin/hobba-miembros">Miembros</a></li>
				</ul><?php endif; ?>
				<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href="#" class="waves-effect waves-teal dropdown-button" data-activates="saw-dropdown" data-beloworigin="true"><i class="fa fa-users"></i> SAW Equipo</a></li>
				<ul id="saw-dropdown" class="dropdown-content no-padding">
					<li><a href="/admin/agregar-saw-miembro">Añadir miembro</a></li>
					<li><a href="/admin/saw-miembros">Miembros</a></li>
				</ul><?php endif; ?>
				
				<?php if($_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Administrador") : ?><li><a href="/admin/web-logs" class="waves-effect waves-teal"><i class="fa fa-sign-in"></i> Logs</a></li><?php endif; ?>
				<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href="#" class="waves-effect waves-teal dropdown-button" data-activates="lufantasie-tienda-dropdown" data-beloworigin="true"><i class="fa fa-shopping-cart"></i> LUFansite Tienda</a></li>
				<ul id="lufantasie-tienda-dropdown" class="dropdown-content no-padding">
					<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li><a href="/admin/agregar-rare">Añadir rare</a></li>
					<li><a href="/admin/rares">Rares</a></li><?php endif; ?>
					<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href="/admin/ordenes">Ordenes</a></li><?php endif; ?>
				</ul>
				<?php endif; ?>
				<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados" OR $_SESSION['user_rank'] == "Coordinación") : ?><li><a href="/admin/mensajes" class="waves-effect waves-teal"><i class="fa fa-envelope"></i> Mensajes</a></li><?php endif; ?>

				<?php if($_SESSION['user_rank'] == "Administrador" OR $_SESSION['user_rank'] == "Encargados") : ?><li><a href=/admin/logros class="waves-effect waves-teal"><i class="fa fa-shield"></i> Logros</a></li><?php endif; ?>
					<li><a href=/admin/logros class="waves-effect waves-teal"><i class="fa fa-shield"></i> Logros</a></li>


				<div class="divider"></div>
				
				<li><a href="/ajustes" class="waves-effect waves-teal"><i class="fa fa-cog"></i> Ajustes</a></li>
				<li><a href="/logout?logout" class="waves-effect waves-teal"><i class="fa fa-sign-out"></i> Salir</a></li>
			</ul>
		</nav>