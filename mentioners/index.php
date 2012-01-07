<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
   <head>
      <?php
      $title = "Usuarios que te mencionan";
      $descr = "Averigua qué usuarios hablan más contigo o de ti";
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
                'status' => utf8_encode("Usando las Twit-Herramientas \"Usuarios que te mencionan\": Averigua qué usuarios hablan más contigo o de ti. ") . KCY,
            ));
         }
		 
		
		$tmhOAuth->request('GET', $tmhOAuth->url('1/statuses/mentions'), array(
			'count' => 200
		));
		
		$usersdata = Array();
		$usermentions = Array();
		$mentions = json_array($tmhOAuth->response['response']);
		
		foreach($mentions as $mention) {
		
		if (!isset($usersdata[$mention['user']['id']])) $usersdata[$mention['user']['id']] = $mention['user'];
		$usermentions[$mention['user']['id']]++;
		}
		
		arsort($usermentions);
		
		?>
		  <table cellspacing="15px" cellpadding="0" style="border: 1px solid #8ec1da; background-color: #c0deed" align="center">
               <tbody>
                  <tr><th colspan="2" style="text-align: center">Cuentas que aparecen en tus 200 últimas menciones:</th></tr>
                  <tr>
                     <td>&nbsp;</td>
                     <th>Nombre (@usuario)</th>
                     <th>Menciones</th>
                  </tr>
                  <?
                  if (!is_array($traidores_data)) {
                     $traidores_data = array();
                  }
					foreach($usermentions as $user_id => $mentions_count) {
							$user_data = array_object($usersdata[$user_id]);
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
                                 <?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $user_data->description) ?>&nbsp; <br /><?= $user_data->status->created_at ?>
                              </span>

                           </span>
                        </td>
                        <td style="text-align: center">
                           <h2><?= $mentions_count; ?></h2>
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
                     <p>¡Averigua quiénes son los usuarios que más veces te han mencionado últimamente!<br /><br />Esta herramienta te mostrará de forma ordenada a los usuarios que más veces te hayan mencionado últimamente, aunque no se incluyen perfiles privados a los que no puedas acceder.</p>
                  </td>
               </tr>
               <tr><th colspan="2"><button onclick="location.href='?action=start'"><? $mensajes = array("¡Empezar!", "¡Dale Caña!", "¡Enga, muéstrame mis megafans!", "¡Enséñamelos!", "Ok pipol, press estart");
         echo($mensajes[rand(0, count($mensajes) - 1)]); ?></button></th></tr>
            </tbody></table>
         <hr>
      <? } ?>

      <? include("../includes/footer.inc"); ?>

   </body>
</html>
