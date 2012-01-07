<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
   <head>
      <?php
      $title = "Test de reciprocidad";
      $descr = "Descubre gente a la que que sigues pero no te sigue a t�";
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

         $tmhOAuth = new tmhOAuth(array(
                     'consumer_key' => ConsumerKey,
                     'consumer_secret' => ConsumerSecret
                 ));
         $tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
         $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

         //Twittear
         if ($_COOKIE['twitear'] != "no") {
            $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/update'), array(
                'status' => "Usando las Twit-Herramientas \"Test de reciprocidad\": Descubre gente a la que que sigues pero no te sigue a ti. " . KCY,
            ));
         }

         // Conseguir Followers
	get_followers();

         //Conseguir Firends
	get_friends();

//      if ($followers['num'] == 5000 || $friends['num'] == 5000) {
//        echo("<p align=\"center\">Atenci�n: Tienes m�s de 5000 followers o sigues a m�s de 5000 personas.<br>
//            La operaci�n se realizar� con los 5000 m�s recientes.</p>");
//      }
         //Comparar
         foreach ($friends['ids'] as $hamijo) {
            if (!in_array($hamijo, $followers['ids'])) {
               $traidores[] = $hamijo;
            }
         }
         If (count($traidores) != 0) {

            $tmhOAuth->request('GET', $tmhOAuth->url('1/users/lookup'), array(
                'user_id' => implode(",", array_slice($traidores, 0, 100)),
            ));
            $traidores_data = json_array($tmhOAuth->response['response']);
            if (count($traidores) > 100) {
               Echo("<p align=\"center\">Sigues a m�s de 100 eg�latras.<br />
                              S�lo se mostrar�n los 100 m�s recientes</p>");
            }
         }
         ?>
         <form name="unfollow" action="?action=unfollow" method="POST">
            <table cellspacing="15px" cellpadding="0" style="border: 1px solid #8ec1da; background-color: #c0deed" align="center">
               <tbody>
                  <tr><th colspan="2" style="text-align: center">Tienes <?= count($traidores) ?> eg�latras entre tus followers:</th></tr>
                  <tr>
                     <td>&nbsp;</td>
                     <th>Nombre (@usuario)</th>
                     <th>Dejar de seguir</th>
                  </tr>
                  <?
                  if (!is_array($traidores_data)) {
                     $traidores_data = array();
                  }
                  foreach ($traidores_data as $traidor_data) {
				  $traidor_data = array_object($traidor_data);
                     ?>
                     <tr>
                        <td>
                           <a title="<?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->description) ?>" hreflang="en" target="_blank" href="http://twitter.com/<?= $traidor_data->screen_name ?>"><img border="0" width="48" height="48" style="vertical-align: middle;" src="<?= $traidor_data->profile_image_url ?>" alt="Imagen"></a>
                        </td>
                        <td>
                           <address title="<?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->description) ?>">
                              <span><?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->name) ?> (<a hreflang="en" target="_blank" href="http://twitter.com/<?= $traidor_data->screen_name ?>">@<?= $traidor_data->screen_name ?></a>) <span style="color:gray">- <?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->location) ?></span></span>

                           </address>
                           <span>
                              <span style="font-size:smaller; color:#666666">
                                 <?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $traidor_data->status->text) ?>&nbsp; <br /><?= $traidor_data->status->created_at ?>
                              </span>

                           </span>
                        </td>
                        <td style="text-align: center">
                           <input type="checkbox" name="<?= $traidor_data->id ?>">
                        </td>
                     </tr>
                     <?
                  }
                  ?>
                  <tr>
                     <th colspan="3" style="text-align: center">
                        <input type="submit" value="Dejar de seguir a los usuarios seleccionados">
                     </th>
                  </tr>
               </tbody></table>
            <?
         } elseif ($_GET['action'] == "unfollow") {

            session_start();

            if (!isset($_SESSION['access_token'])) {
               header("Location: /");
            }

            require ("../includes/config.php");
            require ("../includes/tmhOAuth.php");

            $tmhOAuth = new tmhOAuth(array(
                        'consumer_key' => ConsumerKey,
                        'consumer_secret' => ConsumerSecret
                    ));
            $tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
            $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

//            $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
//            $credenciales = json_array($tmhOAuth->response['response']);

		 //Conseguir Firends
		if (isset($_SESSION["friends"]["ids"]) && is_array($_SESSION["friends"]["ids"]) && count($_SESSION["friends"]["ids"]) != 0) {
			$friends = $_SESSION["friends"];
		} else {
			$tmhOAuth->request('GET', $tmhOAuth->url('1/friends/ids'), array(
				'id' => $_SESSION["access_token"]["user_id"]
			));
			$friends = array('ids' => json_array($tmhOAuth->response['response']), 'num' => count(json_array($tmhOAuth->response['response'])));
			if (!is_array($friends['ids'])) {
				$friends['ids'] = array();
			}
			$_SESSION["friends"] = $friends;
		}

            $unfollowear = array();
            foreach ($friends['ids'] as $hamijo) {
               if ($_POST[$hamijo] == "on") {
                  $unfollowear[] = $hamijo;
               }
            }

            $tmhOAuth->request('GET', $tmhOAuth->url('1/users/lookup'), array(
                'user_id' => implode(",", $unfollowear),
            ));
            $unfollowear_data = json_array($tmhOAuth->response['response']);
            if (!is_array($unfollowear_data)) {
               $unfollowear_data = array();
            }
            ?>
            <table cellspacing="15px" cellpadding="0" style="border: 1px solid #8ec1da; background-color: #c0deed; width: auto" align="center">
               <tbody>
                  <tr><td>

                        <?
                        foreach ($unfollowear_data as $unfollowed) {
						$unfollowed = array_object($unfollowed);
                           $tmhOAuth->request('POST', $tmhOAuth->url('1/friendships/destroy'), array(
                               'id' => $unfollowed->id,
                           ));

                           echo("Has dejado de seguir a <b>" . iconv("UTF-8", "ISO-8859-1//TRANSLIT", $unfollowed->name) . "</b> (@<a href=\"http://twitter.com/{$unfollowed->screen_name}\">{$unfollowed->screen_name}</a>)<br />");
                        }
                        echo("</td></tr><tr><td style=\"text-align: center\"><button onclick=\"location.href='?action='\">Volver</button>");
                        ?>
                     </td></tr></tbody></table>
            <?
         } else {
            ?>
            <hr>
            <p align="center">
               <? include("../includes/ads.inc"); ?>
            </p>
            <hr>
            <table align="center" cellspacing="50px"><tbody>
                  <tr>
                     <td><img src="http://img.twit-herramientas.com/reciprocidad.jpg" alt="Reciprocidad"></td>
                     <td>
                        <p>El "Test de reciprocidad" es una herramienta que coteja los usuarios que sigues con los que te siguen a t�.<br />
                           De esta manera puedes averiguar facilmente que usuarios seguiste pero no te devolvieron el follow, y analizar si merece la pena seguir sigui�ndolos.
                        </p>
                     </td>
                  </tr>
                  <tr><th colspan="2"><button onclick="location.href='?action=start'"><? $mensajes = array("�Empezar!", "�Dale Ca�a!", "Ver mi colecci�n de eg�latras", "�Ens��amelos!", "Ok pipol, press estart");
               echo($mensajes[rand(0, count($mensajes) - 1)]); ?></button></th></tr>
               </tbody></table>
            <hr>
         </form>
         <?
      }
      ?>

      <? include("../includes/footer.inc"); ?>

   </body>
</html>
