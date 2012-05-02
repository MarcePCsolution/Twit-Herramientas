<?php //Aviso de caridad
	if(stripos($_SERVER['HTTP_USER_AGENT'], "googlebot") == false && $_COOKIE['aviso'] != 1) {
		setcookie("aviso",1,time()+60*60*24*30*6); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<head>
<link type="text/css" rel="stylesheet" media="screen" href="todo.css" />
</head>
<script type="text/javascript">
alert("ATENCIÓN:\n\n\
Este mensaje aparece sólo una vez.\n\
Por favor, léalo con atención:\n\n\
Twit-Herramientas es una página\n\
que ofrece servicios\n\
en forma GRATUITA.\n\n\
Para mantenerla,\n\
mostramos publicidad.\n\n\
Si utiliza software\n\
de bloqueo de anuncios.\n\
pedimos lo desactive\n\
mientras navega esta web.\n\n\
Si alguno de los anuncios\n\
le parece interesante,\n\
le recordamos\n\
que hacer click en ellos\n\
no interrumpe la navegación,\n\
y ayuda a sostener la web;\n\
permiten que continúe gratuita,\n\
se mejore, y se actualice.\n\n\
Piense que mantener el sitio\n\
requiere inversión en gasto,\n\
y muchas horas de trabajo.\n\n\
Gracias por entender.\n\n\
Atte.: El Staff de Twit-Herramientas.");
</script>
</body>
</html>
<?php } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	$descr = "Herramientas que harán más fácil tu experiencia en Twitter";
	include "includes/head.inc"; ?>
</head>
<body>
	<!-- Módulo wrapper -->	
	<div class="contenedor">
		<!-- Contenido -->
		<div class="contenidoLogin">

			<?php include 'includes/encabezado_login.php'; ?>

			<h1 class="p1" style="font-size:26px; margin-top:20px;">Utilidades que te ayudarán en Twitter</h1>
			<h2 style="display: none">Herramientas para Twitter</h2>

			<!--Google Adsense Code -->
			<!-- div class="banner1"> // La clase no existe en este momento
				<hr>
				<p align="center"> < ?php include "includes/ads.inc"; ? > </p>
				<hr>
			</div -->

			<!-- Cuadro de Herramientas -->
			<div class="herramientas">
				<div class="fila1">
					<p><a href="reciprocaltest/">Test de Reciprocidad</a></p>
					<p>Comprueba los usuarios que sigues,<br />pero no te siguen.</p><p>¡Fuera ególatras!</p>
				</div>
				<div class="fila1">
					<p><a href="invreciprocaltest/">Test de Reciprocidad inverso</a></p>
					<p>Comprueba quiénes te siguen<br />pero tú no a ellos.<br />Quizá valga la pena seguirlos,</p><p>¡No seas ególatra!</p>
				</div>
				<div class="fila1">
					<p><a href="exfollowers/">Buscador de Ex-Followers</a></p>
					<p>Encuentra qué usuarios<br />te han dejado de seguir.</p><p>¡Tienes derecho a saberlo!</p>
				</div>
		
				<div class="fila2">
					<p><a href="viewblock/">Gestionar Bloqueados</a></p>
					<p>Muestra los usuarios que bloqueaste,<br />y permite desbloquearlos.</p>
				</div>
				<div class="fila2">
					<p><a href="inactive/">Test de Inactividad</a></p>
					<p>Averigua qué usuarios, de entre los que sigues, tweettean muy poco.</p>
				</div>
				<div class="fila2">
					<p><a href="antiquety/">Actividad & Antigüedad</a></p>
					<p>Muestra la edad y actividad de tu cuenta,<br />y de tus 100 follows más recientes.</p>
				</div>
		
				<div class="fila3">
					<p><a href="mentioners/">¿Quién me menciona?</a></p>
					<p>Averigua qué usuarios hacen famoso<br />tu nombre en la red.</p>
				</div>
				<div class="fila3">
					<p><a href="myretweets/">Mis Re-Tweets</a></p>
					<p>Averigua el éxito que alcanzaron<br />tus últimos RT.</p>
				</div>
				<div class="fila3">
					<p><a href="retweetsofme/">Re-Tweets de mis tweets</a></p>
					<p>Averigua cuáles de tus últimos tweets han sido Re-Tweeteados.</p>
				</div>
		
				<div class="fila4">
					<p><a href="followbysearch/">Seguir mediante búsqueda</a></p>
					<p>Encuentra y sigue a numerosos usuarios según un criterio de búsqueda.</p>
				</div>
				<div class="fila4">
					<p><a href="mytopics/">Mis temas favoritos</a></p>
					<p>Averigua qué palabras aparecen más<br />en tus tweets y RTs.<br />Si lo marcaste al iniciar sesión,<br />estos datos se verán en tu Perfil.</p>
				</div>
				<div class="fila4">
					<!--p><a href="mytopics/">Mis temas favoritos</a></p>
					<p>Averigua qué temas/palabras aparecen más en tus twits y retwits.<br /><br />Si lo marcaste al iniciar sesión, estos datos se twitearán en tu Perfil.</p -->
				</div>
			</div>
			<div class="cleared reset-box"></div>
			<!-- FIN Cuadro de Herramientas -->
	
			<div class="sugerencias">
				<div class="sugeSub">
					<h2 class="p1" style="font-size:24px;"> <a target="_blank" href="http://twitter.com/home?status=@MarcePCsolution - Sugerencia:  ">Sugerencias:</a></h2>
					<p class="p1">Para enviarnos una sugerencia,<br />haz click<a target="_blank" href="http://twitter.com/home?status=@MarcePCsolution - Sugerencia: " style="color:red">&emsp;> aquí <&ensp;</a>¡Gracias!</p>
				</div>
				<div class="sugeSub">
					<h2 class="p1" style="font-size:24px;"> <a target="_blank" href="/faq.php">Preguntas Frecuentes:</a></h2>
					<p class="p1">Antes de enviar una sugerencia, lee las:
					<br /><span> <a href="faq.php" target="_blank" style="color:red">Preguntas Frecuentes</a> </span></p>
				</div>
				<div class="cleared reset-box"></div>

				<div class="patrocinadores">
					<p> <a href="/contacto.php" target="_blank"><img src="imagenes/patrocinadores.png" alt="Patrocinadores"></a> </p>
					<h3> <a href="/contacto.php" target="_blank"> Anuncie en nuestro sitio </a></h3>
				</div>
			</div>
			<div class="cleared reset-box"></div>

		</div>
		<!-- FIN Contenido -->
	</div>
	<!-- FIN Módulo wrapper -->	

	<?php include "includes/pie_login.php"; ?>

</body>
</html>