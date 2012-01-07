<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
   <head>
      <?php
      $title = "Antigüedad";
      $descr = "Consulta tu antigüedad y viciadez al Twitter";
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

				
		get_userinfo();
		
		$time_now=strtotime($tmhOAuth->response['headers']['date']);
		$time_created=strtotime($userinfo['created_at']);
		$diff = $time_diff = $time_now-$time_created;
		
		$seconds = $diff%60;
		$diff=($diff-$diff%60)/60;
		$minutes = $diff%60;
		$diff=($diff-$diff%60)/60;
		$hours = $diff%24;
		$diff=($diff-$diff%24)/24;
		$days = $diff%365;
		$diff=($diff-$diff%365)/365;
		$years = $diff;
		
		$howold = Array();
		if ($years) $howold[]="$years años";
		if ($days) $howold[]="$days días";
		if ($hours) $howold[]="$hours horas";
		if ($minutes) $howold[]="$minutes minutos";
		if ($seconds) $howold[]="$seconds segundos";
		
		$howold = array_slice($howold,0,2);
		
		if (count($howold)==1) $howold = $howold[0];
		else $howold = implode(" y ",$howold);
		
		
		if (!$userinfo['statuses_count']) $period = 'Infinito';
		else {
		$period = $time_diff/$userinfo['statuses_count'];
		
		if ($period < 60) { $period = round($period*10)/10; $period .= " segundos"; }
		elseif ($period < 3600) { $period/=60; $period = round($period*10)/10; $period .= " minutos"; }
		elseif ($period < 86400) { $period /= 3600; $period = round($period*10)/10; $period .= " horas"; }
		else { $period /= (24*3600); $period = round($period*10)/10; $period .= " días"; }
		
		}
         //Twittear
         if ($_COOKIE['twitear'] != "no") {
            $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/update'), array(
                'status' => utf8_encode("Usando las Twit-Herramientas \"Antigüedad\": $howold en Twitter y llevo {$userinfo['statuses_count']} twits (uno cada $period). ") . KCY,
            ));
         }
		 
		?>
		 <p align="center">Llevas <b><?= $howold; ?></b> en Twitter (desde <?= $userinfo['created_at']; ?>) y has hecho <b><?=$userinfo['statuses_count']; ?> twits</b>, uno cada <?= $period ?>.</p><br /><?
		 
		get_friends();
			
			?>
			<table cellspacing="15px" cellpadding="0" style="border: 1px solid #8ec1da; background-color: #c0deed" align="center">
               <tbody>
                  <tr><th colspan="2" style="text-align: center">Mostrando <?= $friends['num'] ?> follows más recientes:</th></tr>
                  <?
		
		
		$tmhOAuth->request('GET', $tmhOAuth->url('1/users/lookup'), array(
                'user_id' => implode(",", array_slice($friends['ids'],0,100)),
            ));
            $friendsdata = json_array($tmhOAuth->response['response']);
            if (!is_array($friendsdata)) {
               $friendsdata = array();
            }
			
			
			foreach($friendsdata as $friend_data) {
			
			
		$time_now=strtotime($tmhOAuth->response['headers']['date']);
		$time_created=strtotime($friend_data['created_at']);
		$diff = $time_diff = $time_now-$time_created;
		
		$seconds = $diff%60;
		$diff=($diff-$diff%60)/60;
		$minutes = $diff%60;
		$diff=($diff-$diff%60)/60;
		$hours = $diff%24;
		$diff=($diff-$diff%24)/24;
		$days = $diff%365;
		$diff=($diff-$diff%365)/365;
		$years = $diff;
		
		$howold = Array();
		if ($years) $howold[]="$years años";
		if ($days) $howold[]="$days días";
		if ($hours) $howold[]="$hours horas";
		if ($minutes) $howold[]="$minutes minutos";
		if ($seconds) $howold[]="$seconds segundos";
		
		$howold = array_slice($howold,0,2);
		
		if (count($howold)==1) $howold = $howold[0];
		else $howold = implode(" y ",$howold);
		
		
		if (!$friend_data['statuses_count']) $period = 'Infinito';
		else {
		$period = $time_diff/$friend_data['statuses_count'];
		
		if ($period < 60) { $period = round($period*10)/10; $period .= " segundos"; }
		elseif ($period < 3600) { $period/=60; $period = round($period*10)/10; $period .= " minutos"; }
		elseif ($period < 86400) { $period /= 3600; $period = round($period*10)/10; $period .= " horas"; }
		else { $period /= (24*3600); $period = round($period*10)/10; $period .= " días"; }
		
		}
			
			
				$friend_data = array_object($friend_data);
                     ?>
                     <tr>
                        <td>
                           <a title="<?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $friend_data->description) ?>" hreflang="en" target="_blank" href="http://twitter.com/<?= $friend_data->screen_name ?>"><img border="0" width="48" height="48" style="vertical-align: middle;" src="<?= $friend_data->profile_image_url ?>" alt="Imagen"></a>
                        </td>
                        <td>
                           <address title="<?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $friend_data->description) ?>">
                              <span><?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $friend_data->name) ?> (<a hreflang="en" target="_blank" href="http://twitter.com/<?= $friend_data->screen_name ?>">@<?= $friend_data->screen_name ?></a>) <span style="color:gray">- <?= iconv("UTF-8", "ISO-8859-1//TRANSLIT", $friend_data->location) ?></span></span>

                           </address>
                           <span>
                              <span style="font-size:smaller; color:#666666">
                                 <?= $friend_data->statuses_count ?> twits en <?= $howold ?> (uno cada <?= $period ?>)&nbsp; <br /><?= $friend_data->created_at ?>
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
                     <p>Este apartado te muestra la antigüedad en Twitter y el tiempo de media entre cada twit para tí y tus 100 follows más recientes. Esta herramienta es casi de decoración (tiene pocos usos), aunque sirve para saber quién es "el más viciao" a la red.<br />
					 <br />Nota: si has marcado "Twittear las herramientas que uso", tus usuarios verán tu antigüedad y el tiempo que tardas en escribir cada twit.</p>
                  </td>
               </tr>
               <tr><th colspan="2"><button onclick="location.href='?action=start'"><? $mensajes = array("¡Venga!", "¡Dale Caña!", "¡Demuéstrales que no me vicio!");
         echo($mensajes[rand(0, count($mensajes) - 1)]); ?></button></th></tr>
            </tbody></table>
         <hr>
      <? } ?>

      <? include("../includes/footer.inc"); ?>

   </body>
</html>
