<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	$title = "Test de reciprocidad";
	$descr = "Descubre gente a la que que sigues pero no te sigue a tí";
	include("../includes/head.inc")
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
				if(!isset($_SESSION['access_token'])) {header("Location: /");}

				require "../includes/config.php";
				require "../includes/tmhOAuth.php";

				$tmhOAuth=new tmhOAuth(array('consumer_key'=>ConsumerKey,'consumer_secret'=>ConsumerSecret));
				$tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
				$tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

				//Twittear
				if($_COOKIE['twitear'] != "no") {
					$tmhOAuth->request('POST',$tmhOAuth->url('1/statuses/update'),array('status'=>"Usando las Twit-Herramientas \"Test de reciprocidad\": Descubre gente a la que que sigues pero no te sigue a ti. ".KCY,));
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
				foreach($friends['ids'] as $hamijo) {
					if(!in_array($hamijo, $followers['ids'])) {$traidores[]=$hamijo;}
				}
				If(count($traidores) != 0) {
					$tmhOAuth->request('GET',$tmhOAuth->url('1/users/lookup'),array('user_id'=>implode(",", array_slice($traidores, 0, 100)), ));
					$traidores_data = json_array($tmhOAuth->response['response']);
					// if(count($traidores) > 100) {echo("<p align=\"center\">Sigues a más de 100 ególatras.<br />Sólo se mostrarán los 100 más recientes</p>");}
					if(count($traidores) > 100) {$mensaje_100="<br /><p align=\"center\">Sigues a más de 100 ególatras.<br />Sólo se mostrarán los 100 más recientes</p>";}
				} ?>

			<!-- PANTALLA 2: RESULTADO -->				
			<form name="unfollow" action="?action=unfollow" method="POST">
			<table align="center" cellspacing="15px" cellpadding="0" style="border:1px solid #8ec1da; background-color:#c0deed; width:500px;">
				<tr>
					<th colspan="3" height="40" valign="middle" style="border:1px solid #8ec1da;"><h3 style=" font-size:16px; color:#3e81a8;">Tienes <?php echo count($traidores); ?> ególatras entre tus followers</h3>
														<p style="padding:0; margin:0; font-size:12px;"><?php echo $mensaje_100; ?></p></td>
				</tr>
					<?php if(count($traidores) != 0) {echo "
					<tr>
						<td>&nbsp;</td>
						<th style='text-align:left'><input type='submit' style='padding:3px;' value='&emsp;Dejar de seguir a los seleccionados&emsp;'></th>
						<td  bgcolor='#FFFF00' style='text-align:center'>Todo<p /><input name='checktodos' type='checkbox' /><p />Nada</td>
					</tr>"; } ?>
				<!-- Fin del Encabezado TABLA 1 --> 
		<?php	if(!is_array($traidores_data)) {$traidores_data = array();}
				foreach($traidores_data as $traidor_data) {
					$traidor_data=array_object($traidor_data);
		?>
				<tr>
					<td><a title="<?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$traidor_data->description) ?>" hreflang="en" target="_blank" href="http://twitter.com/<?php echo $traidor_data->screen_name ?>"><img border="0" width="48" height="48" style="vertical-align:middle;" src="<?php echo $traidor_data->profile_image_url ?>" alt="Imagen"></a></td>
					<td><address title="<?php echo iconv("UTF-8", "ISO-8859-1//TRANSLIT",$traidor_data->description) ?>">
                        <span><?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$traidor_data->name) ?> (<a hreflang="en" target="_blank" href="http://twitter.com/<?php echo $traidor_data->screen_name ?>">@<?php echo $traidor_data->screen_name ?></a>) <span style="color:gray">- <?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$traidor_data->location); ?></span></span></address>
						<span><span style="font-size:smaller; color:#666666"><?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$traidor_data->status->text) ?>&nbsp; <br /><?php echo $traidor_data->status->created_at ?></span></span></td>
					<td style="text-align:center"><input type="checkbox" name="<?php echo $traidor_data->id ?>"></td>
				</tr>
				<!-- PIE de TABLA resultados --> 
		<?php	} if(count($traidores) != 0) {echo "
					<tr>
						<td>&nbsp;</td>
						<th style='text-align:left'><input type='submit' style='padding:3px;' value='&emsp;Dejar de seguir a los seleccionados&emsp;'></th>
						<td  bgcolor='#FFFF00' style='text-align:center'>Todo<p /><input name='checktodos' type='checkbox' /><p />Nada</td>
					</tr>"; } ?>
			<!-- FIN PANTALLA RESULTADO -->				
			</table>
	<?php	} elseif ($_GET['action'] == "unfollow") {
				session_start();
				if(!isset($_SESSION['access_token'])) {header("Location: /");}

				require "../includes/config.php";
				require "../includes/tmhOAuth.php";

				$tmhOAuth=new tmhOAuth(array('consumer_key'=>ConsumerKey,'consumer_secret'=>ConsumerSecret));
				$tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
				$tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

//				$tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
//				$credenciales = json_array($tmhOAuth->response['response']);

				//Conseguir Firends
				if(isset($_SESSION["friends"]["ids"]) && is_array($_SESSION["friends"]["ids"]) && count($_SESSION["friends"]["ids"]) != 0) {
					$friends = $_SESSION["friends"];
				} else {
					$tmhOAuth->request('GET',$tmhOAuth->url('1/friends/ids'),array('id'=>$_SESSION["access_token"]["user_id"]));
					$friends=array('ids'=>json_array($tmhOAuth->response['response']),'num'=>count(json_array($tmhOAuth->response['response'])));
					if(!is_array($friends['ids'])) {$friends['ids']=array();}
					$_SESSION["friends"]=$friends;
				}

				$unfollowear=array();
				foreach($friends['ids'] as $hamijo) {
					if($_POST[$hamijo]=="on") {$unfollowear[]=$hamijo;}
				}

				$tmhOAuth->request('GET',$tmhOAuth->url('1/users/lookup'),array('user_id'=>implode(",", $unfollowear), ));
				$unfollowear_data=json_array($tmhOAuth->response['response']);
				if(!is_array($unfollowear_data)) {$unfollowear_data=array();}
			?>
				<!-- TABLA 2: Resultado del Follow -->
				<table align="center" cellspacing="8px" cellpadding="0" style="border:1px solid #8ec1da; background-color:#c0deed; scroling:yes; width:400px; table-layout:fixed;">
				<!-- CABECERA --> 
				<col width="55%" /><col width="45%" />
					<tr>
						<th colspan="2" height="40" valign="middle" style="border:1px solid #8ec1da;"><p style="font-size:16px; color:#3e81a8;">Has dejado de seguir a:</p></th>
					</tr>
		<?php	foreach($unfollowear_data as $unfollowed) {
					$unfollowed=array_object($unfollowed);
					$tmhOAuth->request('POST',$tmhOAuth->url('1/friendships/destroy'),array('id'=>$unfollowed->id, )); ?>
					<tr>
						<td align="right"><b><?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$unfollowed->name) ?></b>&ensp;&ensp;</td>
						<td>@<a href="http://twitter.com/<?php echo $unfollowed->screen_name; ?>"><?php echo $unfollowed->screen_name; ?></a></td>
					</tr>
	  <?php	}  ?>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="button" style="padding:6px" onclick="location.href='./'" value="&emsp;Volver&emsp;" /></td>
					</tr>
				</table>
	  <?php	} else { ?>

            <!-- hr>
            <p align="center">< ? include("../includes/ads.inc"); ? ></p>
            <hr -->

			<!-- PANTALLA INICIAL -->
			<h1 class="p1" style="font-size:24px;">Test de Reciprocidad</h1>
			<table align="center" cellspacing="40px" style="width:600px;">
				<tr>
					<td width="40px">&nbsp;</td>
					<td><img src="../imagenes/reciprocidad.jpg" alt="Reciprocidad"></td>
					<td><p class="p2">Es una herramienta que compara
						<br />los usuarios que sigues,
						<br />con aquellos que te siguen.</p>
						<p class="p2">De esta manera puedes averiguar
						<br />quiénes son los remisos,
						<br />analizando a quién esperar
						<br />y a quién dejar de seguir.</p></td>
				</tr>
				<tr>
					<th colspan="3"><input type='button' style="padding:6px;" onclick="location.href='?action=start'" value="&emsp;<?php $mensajes=array("¡ Empezar !","¡ Dale Caña !"," Ver mi colección de ególatras ","¡ Enséñamelos !"," Ok pipol, press estart"); echo($mensajes[rand(0,count($mensajes)-1)]); ?>&emsp;" /></th>
				</tr>
			</table>
	</form>
		<!-- FIN PANTALLA INICIAL -->
  <?php	} ?>

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