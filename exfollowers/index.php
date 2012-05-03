<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
<head>
<?php
	$title = "Buscador de Ex-Followers";
	$descr = "Encuentra a la gente que te ha dejado de seguir";
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
				if(!isset($_SESSION['access_token'])) {header("Location: /");}

				require "../includes/config.php";
				require "../includes/tmhOAuth.php";
				require "../includes/db.php";

				$tmhOAuth=new tmhOAuth(array('consumer_key'=>ConsumerKey,'consumer_secret'=>ConsumerSecret));
				$tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
				$tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

				//Twittear
				if($_COOKIE['twitear'] != "no") {
					$tmhOAuth->request('POST',$tmhOAuth->url('1/statuses/update'),array('status'=>"Usando las Twit-Herramientas \"Buscador de Ex-Followers\": Encuentra a la gente que te ha dejado de seguir. ".KCY, ));
				}

				// Conseguir Followers
				if(isset($_SESSION["followers"]["ids"]) && is_array($_SESSION["followers"]["ids"]) && count($_SESSION["followers"]["ids"]) != 0) {
					$followers=$_SESSION["followers"];
				} else {
					$tmhOAuth->request('GET',$tmhOAuth->url('1/followers/ids'),array('id'=>$_SESSION["access_token"]["user_id"]));
					$followers=array('ids'=>json_array($tmhOAuth->response['response'],'ids'),'num'=>count(json_array($tmhOAuth->response['response'],'ids')));		
					if (!is_array($followers['ids'])) {$followers['ids']=array();}
					$_SESSION["followers"] = $followers;
				}

				$link=mysql_connect(host,user,passdb);
				mysql_select_db(database,$link);

				$sql1="SELECT * FROM exfollowers WHERE id={$_SESSION["access_token"]["user_id"]}";
				$resultado1=mysql_query($sql1);
				$usuario=mysql_fetch_array($resultado1);

				if(!isset($usuario["id"])) {
					$sql2="INSERT INTO exfollowers (id,followers,usos) VALUES ({$_SESSION["access_token"]["user_id"]}, '".implode(";", $followers["ids"])."', 1)";
					mysql_query($sql2);
					header("Location:?action=start");
				} else {
					foreach(explode(";",$usuario["followers"]) as $viejofollower) {
						if(!in_array($viejofollower,$followers["ids"])) {$exfollowers[]=$viejofollower;}
				}
				if(count($exfollowers) != 0) {
					$tmhOAuth->request('GET',$tmhOAuth->url('1/users/lookup'),array('user_id'=>implode(",",$exfollowers),));
			?>
				<!-- PANTALLA 2: RESULTADO -->				
				<table align="center" cellspacing="15px" cellpadding="0" style="border:1px solid #8ec1da; background-color:#c0deed; width:420px;">
					<tr>
						<th colspan="3" height="40" valign="middle" style="border:1px solid #8ec1da;"><h3 style=" font-size:16px; color:#3e81a8;"><?php echo count($exfollowers); ?> persona(s) te han dejado de seguir:</h3></th>
					</tr>
					<tr>
						<th colspan="2">Nombre (@usuario)</th>
					</tr>
			<?php
					$exfollowers_data=json_array($tmhOAuth->response['response']);
					if(!is_array($exfollowers_data)) {$exfollowers_data=array();}

					$unfollows="";
					foreach($exfollowers_data as $exfollower) {
						$unfollows.="{$exfollower[id]}".strtotime($tmhOAuth->response['headers']['date']).";";
						$exfollower=array_object($exfollower);
			?>
					<tr>
						<td><a title="<?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$exfollower->description); ?>" hreflang="en" target="_blank" href="http://twitter.com/<?php echo $exfollower->screen_name; ?>"><img border="0" width="48" height="48" style="vertical-align:middle;" src="<?php echo $exfollower->profile_image_url; ?>" alt="Imagen"></a></td>
						<td><address title="<?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$exfollower->description); ?>">
							<span><?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$exfollower->name); ?> (<a hreflang="en" target="_blank" href="http://twitter.com/<?php echo $exfollower->screen_name; ?>">@<?php echo $exfollower->screen_name; ?></a>) <span style="color:gray">- <?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$exfollower->location); ?></span></span></address>
							<span><span style="font-size:smaller; color:#666666"><?php echo iconv("UTF-8","ISO-8859-1//TRANSLIT",$exfollower->status->text); ?>&nbsp;<br /><?php echo $exfollower->status->created_at; ?></span></span></td>
						<!-- td style="text-align:center"><input type="checkbox" name="<?php echo $traidor_data->id ?>"></td -->
					</tr>
		  <?php	} //echo php_error(); ?>
				</table>
	  <?php	} else {
				//-- PANTALLA 2 ALTERNATIVA: Aviso ningún #FollowBack adeudado -->				
				echo "<p class='p1' style='font-size:24px; margin-top:80px;'>Tienes los mismos followers que en la última consulta.</p><br /><br />";


/*-PRUEBA-->*/	//echo print_r($unfollows);
/*-PRUEBA-->*/	//echo print_r($usuario);

			}
			$usos=$usuario["usos"]+1;
			if(!strlen($unfollows)) {
				mysql_query("UPDATE exfollowers SET followers='".implode(";",$followers["ids"])."',usos='".$usos."'WHERE exfollowers.id='{$_SESSION["access_token"]["user_id"]}'");
			} else {
				mysql_query("UPDATE exfollowers SET unfollows=concat(unfollows,'".mysql_real_escape_string($unfollows)."'),followers='".implode(";",$followers["ids"])."',usos='".$usos."' WHERE exfollowers.id='{$_SESSION["access_token"]["user_id"]}'");
			}
		} ?>
				<br /><p align="center"><input type='button' onclick="location.href='?action='" value="&emsp;Volver&emsp;" style="padding:6px;" /></p>
<?php } else { ?>
				<!-- FIN PANTALLA 2: RESULTADO -->

				<!-- hr>
				<p align="center">< ?php include "../includes/ads.inc"; ? ></p>
				<hr -->

				<!-- PRIMER PANTALLA -->
				<h1 class="p1" style="font-size:24px;">Buscador de Ex-Followers</h1>
				<table align="center" cellspacing="30px" style="width:700px;">
					<tr>
						<td width="40px">&nbsp;</td>
						<td><img src="../imagenes/lupa.jpg" alt="Reciprocidad"></td>
						<td style="margin-top:-20px;"><p class="p2">Es una herramienta que muestra
							<br />qué usuarios te dejaron de seguir.</p>
							<h3 class="p2">Funcionamiento:</h3>
							<p class="p2">La primera vez que la uses, registrará
							<br />los followers que tienes en ese momento.</p>	
							<p class="p2">A partir de entonces, cada vez que la uses
							<br />listará a quiénes te dejaron de seguir,
							<br />tomando como referencia
							<br />la última vez que utilizaste la herramienta.</p></td>
					</tr>
					<tr>
						<th colspan="3"><input type='button' onclick="location.href='?action=start'" value='&emsp;<?php $mensajes=array("¡ Empezar !","¡ Dale Caña !","¡ Dime quién me ha dejado de seguir !","¡ Enséñamelos !","Ok pipol, press estart");
										echo $mensajes[rand(0,count($mensajes)-1)]; ?>&emsp;' style="padding:6px;" /></th>
					</tr>
				</table>
				<!-- FIN PRIMER PANTALLA -->
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