<?
function json_array($arr,$key=null) {
if ($key === null) return json_decode($arr,true);
else {
$arr = json_decode($arr,true);
return $arr[$key];
}
}
function json_object($arr,$key=null) {
if ($key === null) return json_decode($arr);
else {
$arr = json_decode($arr);
return $arr[$key];
}
}
function array_object($arr) {
return json_decode(json_encode($arr));
}

function get_userinfo() {
global $userinfo,$tmhOAuth;
		$tmhOAuth->request('GET', $tmhOAuth->url('1/users/lookup'), array(
			'user_id' => $_SESSION["access_token"]["user_id"]
		));
         $userinfo = json_array($tmhOAuth->response['response']);
		 $userinfo = $userinfo[0];
}

function get_followers() {
global $followers,$tmhOAuth;
	if (isset($_SESSION["followers"]["ids"]) && is_array($_SESSION["followers"]["ids"]) && count($_SESSION["followers"]["ids"]) != 0) {
		$followers = $_SESSION["followers"];
	} else {
		$tmhOAuth->request('GET', $tmhOAuth->url('1/followers/ids'), array(
			'id' => $_SESSION["access_token"]["user_id"]
		));
		$followers = array('ids' => json_array($tmhOAuth->response['response'],'ids'), 'num' => count(json_array($tmhOAuth->response['response'],'ids')));		
		
		if (!is_array($followers['ids'])) {
			$followers['ids'] = array();
		}
		$_SESSION["followers"] = $followers;
}
}

function get_friends() {
global $friends,$tmhOAuth;
	if (isset($_SESSION["friends"]["ids"]) && is_array($_SESSION["friends"]["ids"]) && count($_SESSION["friends"]["ids"]) != 0) {
		$friends = $_SESSION["friends"];
	} else {
		$tmhOAuth->request('GET', $tmhOAuth->url('1/friends/ids'), array(
			'id' => $_SESSION["access_token"]["user_id"]
		));
		$friends = array('ids' => json_array($tmhOAuth->response['response'],'ids'), 'num' => count(json_array($tmhOAuth->response['response'],'ids')));
		if (!is_array($friends['ids'])) {
			$friends['ids'] = array();
		}
		$_SESSION["friends"] = $friends;
	}
}

?>
<!-- Histats.com  START  (standard)-->
<script type="text/javascript">document.write(unescape("%3Cscript src=%27http://s10.histats.com/js15_giftop.js%27 type=%27text/javascript%27%3E%3C/script%3E"));</script>
<a href="http://www.histats.com" target="_blank" title="contador de visitas" ><script  type="text/javascript" >
   try {Histats.startgif(1,1296087,4,10011,"div#histatsC {position: absolute;top:0px;left:0px;}body>div#histatsC {position: fixed;}");
      Histats.track_hits();} catch(err){};
   </script></a>
<noscript><style type="text/css">div#histatsC {position: absolute;top:0px;left:0px;}b</style>
<a href="http://www.histats.com" alt="contador de visitas" target="_blank" ><div id="histatsC"><img border="0" src="http://s4is.histats.com/stats/i/1296087.gif?1296087&103"></div></a>
</noscript>
<!-- Histats.com  END  -->

<p align="center"><a href="/"><img src="http://img.th.wazzia.com/logo.png" alt="Logo"></a></p>
<a href="/" onclick="if(!confirm('¿Deseas volver al menú principal?'))return false"><img style="position:fixed;bottom:0%; right:2%" src="http://img.th.wazzia.com/icon.png" alt="Icono"></a>
<? echo('<div style="margin-top: 100px">'); ?>
<!-- <?print_r($_SESSION);?> -->
