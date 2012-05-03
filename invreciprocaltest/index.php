<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
<head>
<?php
	$descr="Descubre, de entre tus followers, a quien no sigues";
	$title="Test de reciprocidad inverso";
	include "../includes/head.inc";
	?> 
</head>
<body>
	<!-- Módulo wrapper -->
	<div class="contenedor"> 
		<!-- Contenido -->
		<div class="contenidoLogin">
			<?php include "../includes/encabezado_login.php";

			if($_GET['action'] == "start") {
				session_start();
				if(!isset($_SESSION['access_token'])) {
					header("Location: /");
				}
				require "../includes/config.php";
				require "../includes/tmhOAuth.php";
				$tmhOAuth=new tmhOAuth(array('consumer_key'=>ConsumerKey, 'consumer_secret'=>ConsumerSecret));
				$tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
				$tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

				//Twittear
				if($_COOKIE['twitear'] != "no") {
					$tmhOAuth->request('POST',$tmhOAuth->url('1/statuses/update'), array('status'=>"Usando las Twit-Herramientas \"Test de reciprocidad inverso\": Descubre, de entre tus followers, a quien no sigues.".KCY, ));
				}

				// Conseguir Followers
				get_followers();

				//Conseguir Firends
				get_friends();

			//-- PANTALLA RESULTADO -->				
				if ($followers['num'] == 5000 || $friends['num'] == 5000) {
					echo("<p class=\"p1\" style=\"color:#ff0000; font-size:18px;\" align=\"center\"><b>Atención: Tienes más de 5000 followers o sigues a más de 5000 cuentas.</b></p>
						  <p class=\"p1\" style=\"color:#fc9764; font-size:16px;\" align=\"center\"><b>Por el momento la operación sólo se realiza con 5000 de cada lista.<br />No usar listas completas, arroja resultados erróneos para cuentas mayores a 5000.</b></p><p>&nbsp;</p>");
				}

				//Comparar
				$traidores = array();
				foreach ($followers['ids'] as $follower) {
					if(!in_array($follower, $friends['ids'])) {
						$traidores[] = $follower;
					}
				}
				if(count($traidores) != 0) {
					$tmhOAuth->request('GET', $tmhOAuth->url('1/users/lookup'), array('user_id' => implode(",", array_slice($traidores, 0, 100)), ));
					$traidores_data = json_array($tmhOAuth->response['response']);
					if(!is_array($traidores_data)) {
						$traidores_data = array();
					} //else { // MOSTRAR MENSAJE DE FELICITACIÓN NINGÚN FOLLOWBACK ADEUDADO
						
						//Felicitaciones: You are following back everyone. Nice! 
						//Botón volver
					//}

					if (count($traidores) > 100) {
						$mensaje_100 = "<br />Sólo se mostrarán los 100 más recientes<br />Para completar la cantidad, repita la operación.";
					}
				} else $traidores_data = Array();
			?>		
			<form name="follow" action="?action=follow" method="POST">
				<!-- PANTALLA 2: Lista usuarios que esperan FollowBack --> 
				<table align="center" cellspacing="15px" cellpadding="0" style="border:1px solid #8ec1da; background-color:#c0deed; width:500px; table-layout:fixed;">
				<col width="10%" /><col width="80%" /><col width="10%" />
					<tr>
						<td colspan="3" cellspacing="2px" style="text-align:center; border:1px solid #8ec1da;"><h3 style="padding:0; margin:0;">
															<?php echo count($traidores); ?> usuarios te siguen, pero tú a ellos no.</h3>
															<p style="padding:0; margin:0; font-size:12px;"><?php echo $mensaje_100; ?></p></th>
					</tr>
					<?php if(count($traidores) != 0) {echo "
					<tr>
						<td>&nbsp;</td>
						<th style='text-align:left'><input type='submit' style='padding:4px'; value='&emsp;Seguir a los usuarios seleccionados&emsp;'></th>
						<td  bgcolor='#FFFF00' style='text-align:center'>Todo<p /><input name='checktodos' type='checkbox' /><p />Nada</td>
					</tr>"; } ?>
				<!-- Fin del Encabezado PANTALLA 2 --> 

			<?php	foreach($traidores_data as $traidor_data) {
					$traidor_data = array_object($traidor_data);
			?>
					<tr>
						<td><a title="<?php echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->description); ?>" hreflang="en" target="_blank" href="http://twitter.com/<?php echo $traidor_data->screen_name; ?>"><img border="0" width="48" height="48" style="vertical-align: middle;" src="<?php echo $traidor_data->profile_image_url; ?>" alt="Imagen"></a></td>
                        <td><address title="<?php echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->description); ?>"><span><?php echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->name); ?>
									(<a hreflang="en" target="_blank" href="http://twitter.com/<?php echo $traidor_data->screen_name; ?>">@<?php echo $traidor_data->screen_name; ?></a>) 
									 <span style="color:gray">- <?php echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->location); ?></span></span></address>
									<span><span style="font-size:smaller; color:#666666"><?php echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->status->text); ?>&nbsp;
									<br /><?= $traidor_data->status->created_at ?></span></span></td>
						<td style="text-align:center"><input type="checkbox" name="<?php echo $traidor_data->id ?>"></td>
					</tr>
		<?php	}
				if(count($traidores) != 0) {echo "<tr>
						<td>&nbsp;</td>
						<th style='text-align:left'><input type='submit' style='padding:4px'; value='&emsp;Seguir a los usuarios seleccionados&emsp;'></th>
						<td  bgcolor='#FFFF00' style='text-align:center'>Todo<p /><input name='checktodos' type='checkbox' /><p />Nada</td>
					</tr>"; } ?>
				</table>
		<?php
			} elseif ($_GET['action'] == "follow") {
				session_start();
				if(!isset($_SESSION['access_token'])) {
					header("Location: /");
				}

				require "../includes/config.php";
				require "../includes/tmhOAuth.php";

				$tmhOAuth=new tmhOAuth(array('consumer_key'=>ConsumerKey, 'consumer_secret'=>ConsumerSecret));
				$tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
				$tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

				// Conseguir Followers
				if(isset($_SESSION["followers"]["ids"]) && is_array($_SESSION["followers"]["ids"]) && count($_SESSION["followers"]["ids"]) != 0) {
					$followers = $_SESSION["followers"];
				} else {
					$tmhOAuth->request('GET', $tmhOAuth->url('1/followers/ids'), array('id'=>$_SESSION["access_token"]["user_id"]));
					$followers = array('ids' => json_array($tmhOAuth->response['response']), 'num' => count(json_array($tmhOAuth->response['response'])));		
					if (!is_array($followers['ids'])) {
						$followers['ids'] = array();
					}
					$_SESSION["followers"] = $followers;
				}

				//Unfollowear
				$followear = array();
				foreach($followers['ids'] as $follower) {
					if($_POST[$follower] == "on") {$followear[]=$follower;}
				}
				$tmhOAuth->request('GET',$tmhOAuth->url('1/users/lookup'),array('user_id'=>implode(",",$followear),));
				$followear_data=json_array($tmhOAuth->response['response']);
				if(!is_array($followear_data)) {
				$followear_data=array();
			} ?>
				<!-- TABLA 2: Resultado del Follow -->
				<table align="center" cellspacing="8px" cellpadding="0" style="border:1px solid #8ec1da; background-color:#c0deed; scroling:yes; width:400px; table-layout:fixed;">
				<!-- CABECERA --> 
				<col width="55%" /><col width="45%" />
				<tbody>
					<tr>
						<th colspan="2" height="40" valign="middle" style="border:1px solid #8ec1da;"><p style="font-size:16px; color:#3e81a8;">Ahora sigues a:</p></th>
					</tr>
	<?php	foreach ($followear_data as $followed) {
				$tmhOAuth->request('POST',$tmhOAuth->url('1/friendships/create'),array('id'=>$followed->id,)); ?>
					<tr>
						<td align="right"><b><?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$followed['name']); ?></b>&emsp;&emsp;</td>
						<td>@<a href="http://twitter.com/<?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$followed['screen_name']); ?>"><?php echo $followed['screen_name'] ?></a></td>
					</tr>
	 <?php } ?>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="button" style="padding:6px" onclick="location.href='./'" value="&emsp;Volver&emsp;" /></td>
					</tr>
				</tbody>
			</table>
            <?php } else { ?>

            <!-- hr>
			<p align="center">< ?php include("../includes/ads.inc"); ? ></p>
            <hr -->

			<!-- PANTALLA INICIAL -->
			<h1 class="p1" style="font-size:24px;">Test de Reciprocidad Inverso</h1>
			<table align="center" cellspacing="40px" style="width:600px;">
				<tr>
					<td><img src="../imagenes/invreciprocidad.jpg" alt="Reciprocidad"></td>
					<td><p class="p2">Es la herramienta contraria 
						<br />al test de reciprocidad:</p>
						<p class="p2">Busca usuarios que te siguen,
						<br />pero todavía no sigues.
						<p class="p2">Con ella puedes dar
						<br />tus FollowBacks atrasados.</p><td>
				</tr>
				<tr>
					<th colspan="2"><input type="button" style="padding:6px;" onclick="location.href='?action=start'" value="&ensp;<?php $mensajes = array("¡ Empezar !","¡ Dale Caña !","¡ Enséñamelos !"," Ok pipol, press estart "); echo($mensajes[rand(0,count($mensajes)-1)]); ?>" /></th>
				</tr>
			</table>
			<!-- FIN PANTALLA INICIAL -->
		</form>
  <?php } ?>

		</div>
		<!-- FIN Contenido -->	
	</div>
	<!-- FIN Módulo wrapper -->	

	<?php include "../includes/pie_login.php"; ?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
 
	//Checkbox
	$("input[name=checktodos]").change(function(){
		$('input[type=checkbox]').each( function() {			
			if($("input[name=checktodos]:checked").length == 1){
				this.checked = true;
			} else {
				this.checked = false;
			}
		});
	});
 
});
</script>
</body>
</html>