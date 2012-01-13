<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
   <head>
      <?php
      $title = "Temas favoritos";
      $descr = "Averigua qué temas/palabras aparecen más en tus twits y retwits.";
      include("../includes/head.inc")
      ?>
   </head>
   <body>
      <?php
      include("../includes/header.inc");
      if ($_GET['action'] == "start") {

         session_start();

         if (!isset($_SESSION['access_token'])) {
            header("Location: /");
         }

         require ("../includes/config.php");
         require ("../includes/tmhOAuth.php");
         require ("../includes/db.php");

         $tmhOAuth = new tmhOAuth(array(
                     'consumer_key' => ConsumerKey,
                     'consumer_secret' => ConsumerSecret
                 ));
         $tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
         $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

		 
		
		$tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/user_timeline'), array(
			'count' => 200,
			'exclude_replies' => 1,
			'trim_user' => 1,
			'user_id' =>  $_SESSION["access_token"]["user_id"],
			'include_rts' => 1
		));
		
		$topwords = Array();
		$twitcount = 0;
			
		$twits = json_decode($tmhOAuth->response['response'],true);
		
		if (!is_array($twits)) die("Por razones desconocidas, hay que recargar para hacer funcionar la herramienta. Pulsa F5 o recarga la página para hacer servir esta herramienta.<br /><br />Ha habido un error. Inténtalo más tarde o llama por teléfono a nuestro equipo de Abejas asesinas para eliminar el problema.");
		
		foreach($twits as $twit) {
		
				
		$text = strtolower(utf8_decode($twit['text']));
		if (substr($text,0,strlen("usando las twit-herramientas"))=="usando las twit-herramientas") continue;
		$twitcount ++;
		$twitwords = Array();
				
		$text = preg_replace("'http\:\/\/t\.co\/([a-zA-Z0-9]+)'","",$text);
		
		if (preg_match_all("'([\@\_a-zA-Z0-9ñÑáÁéÉíÍóÓúÚüÜ\#]+)'",$text,$matches)) {
		foreach($matches[1] as $word) {
		if (strlen($word)>4 && $word != "http")
		if (!isset($twitwords[base64_encode($word)])) {
		$twitwords[base64_encode($word)]=true;
		$topwords[base64_encode($word)]++;
		}
		}
		}
		}
		
		arsort($topwords);
		$topwords = array_slice($topwords,0,100);
		
		$temas = Array();
		
		?><table cellspacing="" cellpadding="15px" style="border: 1px solid #8ec1da; background-color: #c0deed" align="center">
               <tbody>
                  <tr><th colspan="3" style="text-align: center">Mostrando <?= count($topwords) ?> temas/palabras más usadas:</th></tr>
				  <?
		$pos = 0;
		foreach($topwords as $word => $count) {
		$pos++;
		$word = base64_decode($word);
		if ($pos <= 3) $temas[]=$word;
		$porcentaje = round($count/$twitcount*1000)/10;
		?>
		<tr onmouseover='this.style.backgroundColor="#9ed1ea"' onmouseout='this.style.backgroundColor="#c0deed"'><td><?= $pos ?>. </td><td><?= $word ?></td><td><?= $porcentaje ?>%</td></tr>
		<?
		}
		
		$temas = implode(", ",$temas)."...";
		
         //Twittear
		 if (strlen($temas)>6) {
         if ($_COOKIE['twitear'] != "no") {
            $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/update'), array(
                'status' => utf8_encode("Usando las Twit-Herramientas \"Temas favoritos\": Los míos son $temas ") . KCY,
            ));
         }
		 }

		
                  ?>
               </tbody></table>	
	<?
      } else {
         ?>
         <hr>
         <p align="center">
            <? include("../includes/ads.inc"); ?>
         </p>
         <hr>
         <table align="center"><tbody><tr>
                  <td>
                     <p>¡Conoce los temas de los que más hablas o las palabras que más utilizas!<br /><br />Esta herramienta te muestra una tabla con las 100 palabras que más usas en tus twits (eso sí, se excluyen palabras con menos de 5 letras, pues suelen ser "que", "y", "en", etc.</p>
                  </td>
               </tr>
               <tr><th colspan="2"><button onclick="location.href='?action=start'"><? $mensajes = array("¡Empezar!", "¡Dale Caña!", "¡A toda máquina!", "¡Enséñamelas!", "Ok pipol, press estart");
         echo($mensajes[rand(0, count($mensajes) - 1)]); ?></button></th></tr>
            </tbody></table>
         <hr>
      <? } ?>

      <? include("../includes/footer.inc"); ?>

   </body>
</html>
