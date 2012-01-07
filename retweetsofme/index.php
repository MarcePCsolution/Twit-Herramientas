<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
   <head>
      <?php
      $title = "Retwits de mis twits";
      $descr = "Descubre cuáles de tus twits han sido retwiteados";
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

         //Twittear
         if ($_COOKIE['twitear'] != "no") {
            $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/update'), array(
                'status' => utf8_encode("Usando las Twit-Herramientas \"Retwits de mis twits\": Averigua cuáles de tus twits han sido retwiteados. ") . KCY,
            ));
         }
		 
		
		$tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/retweets_of_me'), array(
			'count' => 100
		));
		
		$usersdata = Array();
		$usermentions = Array();
		$retweets = json_array($tmhOAuth->response['response']);
		
		
		?>
		  <table cellspacing="15px" cellpadding="0" style="border: 1px solid #8ec1da; background-color: #c0deed" align="center">
               <tbody>
                  <tr><th colspan="2" style="text-align: center">Mostrando tus 100 últimos twits con retwits:</th></tr>
                  <?
                  if (!is_array($traidores_data)) {
                     $traidores_data = array();
                  }
					foreach($retweets as $retweet) {
							$user_data = array_object($retweet['user']);
							$retweet_data = array_object($retweet);
                     ?>
                     <tr>
                        <td>
                           <a title="<?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $user_data->description) ?>" hreflang="en" target="_blank" href="http://twitter.com/<?= $user_data->screen_name ?>"><img border="0" width="48" height="48" style="vertical-align: middle;" src="<?= $user_data->profile_image_url ?>" alt="Imagen"></a>
                        </td>
                        <td>
                           <span>
                              <span style="font-size:smaller; color:#666666">
							  <a target="_blank" href="http://twitter.com/<?= $user_data->screen_name ?>/status/<?= $retweet_data->id ?>"><?= $retweet_data->created_at ?></a><br />
                                 <?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $retweet_data->text) ?>&nbsp;
                              </span>

                           </span>
                        </td>
                     </tr>
                     <?
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
                     <p>¡Descubre cuáles de tus twits han sido retwiteados! Aquí te mostraremos una lista con tus 100 últimos twits con retwits.<br /><br />Eso sí, para ver los retwits tienes que cliquear sobre las fechas.</p>
                  </td>
               </tr>
               <tr><th colspan="2"><button onclick="location.href='?action=start'"><? $mensajes = array("¡Empezar!", "¡Dale Caña!", "¡Quiero verlos!", "¡Enséñamelos!", "Ok pipol, press estart");
         echo($mensajes[rand(0, count($mensajes) - 1)]); ?></button></th></tr>
            </tbody></table>
         <hr>
      <? } ?>

      <? include("../includes/footer.inc"); ?>

   </body>
</html>
