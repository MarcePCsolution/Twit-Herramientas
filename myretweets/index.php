<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
   <head>
      <?php
      $title = "Mis retwits";
      $descr = "Encuentra rápido los twits que retwiteaste y la fama que alcanzaron.";
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
                'status' => utf8_encode("Usando las Twit-Herramientas \"Mis retwits\": Encuentra rápido los twits que retwiteaste y la fama que alcanzaron. ") . KCY,
            ));
         }
		 
		
		$tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/retweeted_by_me'), array(
			'count' => 100
		));
		
		$usersdata = Array();
		$usermentions = Array();
		$retweets = json_array($tmhOAuth->response['response']);
		
		?><table cellspacing="15px" cellpadding="0" style="border: 1px solid #8ec1da; background-color: #c0deed" align="center">
               <tbody>
                  <tr><th colspan="2" style="text-align: center">Mostrando tus 100 últimos retwits:</th></tr>
                  <tr>
                     <td>&nbsp;</td>
                     <th>Nombre (@usuario)</th>
                     <th>Retwits</th>
                  </tr>
                  <?
                  foreach($retweets as $retweet) {
							$retweet_data = array_object($retweet);
							$retweet = $retweet['retweeted_status'];
							$user_data = array_object($retweet['user']);
							$tweet_data = array_object($retweet);
                     ?>
                     <tr>
                        <td>
                           <a title="<?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $user_data->description) ?>" hreflang="en" target="_blank" href="http://twitter.com/<?= $user_data->screen_name ?>"><img border="0" width="48" height="48" style="vertical-align: middle;" src="<?= $user_data->profile_image_url ?>" alt="Imagen"></a>
                        </td>
                        <td>
                              <span><?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $user_data->name) ?> (<a hreflang="en" target="_blank" href="http://twitter.com/<?= $user_data->screen_name ?>">@<?= $user_data->screen_name ?></a>) <span style="color:gray">- <?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $user_data->location) ?></span></span>
							<br />
                           <span>
                              <span style="font-size:smaller; color:#666666">
                                 <?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $tweet_data->text) ?>&nbsp; <br />
								 <span style="color:#333"<a hreflang="en" target="_blank" href="http://twitter.com/<?= $user_data->screen_name ?>/status/<?= $tweet_data->id ?>"><?= $tweet_data->created_at ?></a> / Retwitteado <?= $retweet_data->created_at ?></span>
                              </span>

                           </span>
                        </td>
                        <td style="text-align: center">
                           <h2><?= $retweet_data->retweet_count ? $retweet_data->retweet_count : "??" ?></h2>
                        </td>
                     </tr>
                     <?
                  }
                  ?>
               </tbody></table><?
		
		
		
		

      } else {
         ?>
         <hr>
         <p align="center">
            <? include("../includes/ads.inc"); ?>
         </p>
         <hr>
         <table align="center"><tbody><tr>
                  <td>
                     <p>¡Recuerda esos twits que quisiste compartir con los demás y descubre cuántos retwits consiguieron!<br /><br />Los twits con más de 100 retwits se verán como "100+".</p>
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
