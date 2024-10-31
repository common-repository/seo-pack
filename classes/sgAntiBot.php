<?php
 
if (!class_exists('SgSeoAntiBot')) {
	class SgSeoAntiBot {
		
		private $sgabWhitelist = array();
		private $sgabConfig = array();
		private $sgIPs = array('185.72.157.169', 
								'185.72.157.170', 
								'185.72.157.171', 
								'185.72.157.172', 
								);
							
		public function __construct() {
					
			
			//123.123.123.* instead of 123.123.123.123
			$this->sgabConfig['short_mask'] = 1;
			
			//got checkscreen(debug)
			$this->sgabConfig['antibot_log'] = 0; 
			
			//successfully passed checkscreen(debug)
			$this->sgabConfig['antibot_log2'] = 0; 
			
			//whitelist by default
			$this->sgabWhitelist['yandex.com'] = array('yandex.ru', 'yandex.net', 'yandex.com'); // yandex
			$this->sgabWhitelist['Googlebot'] = array('googlebot.com', 'google.com'); // google index
			$this->sgabWhitelist['Google-Site-Verification'] = array('googlebot.com', 'google.com'); // google webmaster
			$this->sgabWhitelist['Mail.RU_Bot'] = array('mail.ru', 'smailru.net'); // Mail.ru
			$this->sgabWhitelist['bingbot'] = array('search.msn.com'); // Bing.com
			$this->sgabWhitelist['AppEngine-Google'] = array('.googleusercontent.com'); //  freenom.com
			// social networks
			$this->sgabWhitelist['vkShare'] = array('.vk.com'); // VK
			$this->sgabWhitelist['facebookexternalhit'] = array('31.13.'); // Facebook
			$this->sgabWhitelist['OdklBot'] = array('.odnoklassniki.ru'); // ok.ru
			$this->sgabWhitelist['MailRuConnect'] = array('.smailru.net'); // mail.ru
			$this->sgabWhitelist['Twitterbot'] = array('199.16.15'); // Twitter
			$this->sgabWhitelist['TelegramBot'] = array('149.154.16'); // Telegram
			$this->sgabWhitelist['AdsBot-Google'] = array('.'); // AdsBot-Google

			//$this->sgabWhitelist['googleweblight'] = array('google.com');
			//$this->sgabWhitelist['BingPreview'] = array('search.msn.com'); // Bing
			//$this->sgabWhitelist['uptimerobot'] = array('uptimerobot.com');
			//$this->sgabWhitelist['pingdom'] = array('pingdom.com');
			//$this->sgabWhitelist['HostTracker'] = array('.'); 
			//$this->sgabWhitelist['Yahoo! Slurp'] = array('.yahoo.net'); //  Yahoo
			//$this->sgabWhitelist['SeznamBot'] = array('.seznam.cz'); //  seznam.cz
			//$this->sgabWhitelist['Pinterestbot'] = array('.pinterest.com'); // 
			//$this->sgabWhitelist['Mediapartners'] = array('googlebot.com', 'google.com'); // AdSense bot
			$this->sgIPs[] = $_SERVER['SERVER_ADDR'];
			
			$file = __DIR__ . DIRECTORY_SEPARATOR . 'wl.dat';
			if (is_file($file)) {	
				$handle = fopen($file, "r");
				$content = @fread($handle, filesize($file));
				fclose($handle);
				
				$useragents = explode(",", $content);
				
				foreach ($useragents as $useragent) {
					$this->sgabWhitelist[trim($useragent)] = array('.');
				}
				
			}
			
		}
		
		public function analyze() {

			$this->sgabConfig['host'] = isset($_SERVER['HTTP_HOST']) ? preg_replace("/[^0-9a-z-.:]/","", $_SERVER['HTTP_HOST']) : '';
			$this->sgabConfig['useragent'] = isset($_SERVER['HTTP_USER_AGENT']) ? trim(strip_tags($_SERVER['HTTP_USER_AGENT'])) : '';
			$this->sgabConfig['uri'] = trim(strip_tags($_SERVER['REQUEST_URI']));
			$this->sgabConfig['referer'] = isset($_SERVER['HTTP_REFERER']) ? trim(strip_tags($_SERVER['HTTP_REFERER'])) : '/';

			if ($this->sgabConfig['useragent'] == '') exit; 
			
			if (isset($_SERVER['HTTP_FORWARDED'])) {
				$this->sgabConfig['ip'] = $_SERVER['HTTP_FORWARDED'];
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$this->sgabConfig['ip'] = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$this->sgabConfig['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
				$this->sgabConfig['ip'] = $_SERVER['HTTP_X_FORWARDED'];
			} else {
				$this->sgabConfig['ip'] = $_SERVER['REMOTE_ADDR'];
			}
			
			$this->sgabConfig['ip'] = strip_tags($this->sgabConfig['ip']);
			
			if (mb_stripos($this->sgabConfig['ip'], ',', 0, 'utf-8')!== false) {
				$this->sgabConfig['ip'] = explode(',', $this->sgabConfig['ip']); 
				$this->sgabConfig['ip'] = trim($this->sgabConfig['ip'][0]);
			}
			if (mb_stripos($this->sgabConfig['ip'], ':', 0, 'utf-8')!== false) {
				$this->sgabConfig['ip'] = explode(':', $this->sgabConfig['ip']); 
				$this->sgabConfig['ip'] = trim($this->sgabConfig['ip'][0]);
			}
			$this->sgabConfig['ip'] = preg_replace("/[^0-9.]/","",$this->sgabConfig['ip']);

			if (filter_var($this->sgabConfig['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
				echo 'IPv4 only';
				exit;
			}


			$ab_config_ip_array = explode('.', $this->sgabConfig['ip']);
			$this->sgabConfig['ip_short'] = $ab_config_ip_array[0].'.'.$ab_config_ip_array[1].'.'.$ab_config_ip_array[2].'.*';


			if (file_exists(__DIR__.'/blackbot/'.$this->sgabConfig['ip'].'.txt') OR file_exists(__DIR__.'/blackbot/'.$this->sgabConfig['ip_short'].'.txt')) {
				echo 'BlackBot';
				exit;
			}

			$this->sgabConfig['whitebot'] = 0;

			if ((file_exists(__DIR__.'/whitebot/'.$this->sgabConfig['ip'].'.txt')) OR 
				(file_exists(__DIR__.'/whitebot/'.$this->sgabConfig['ip_short'].'.txt')) OR 
				(in_array($this->sgabConfig['ip'], $this->sgIPs)) OR 
				(stripos($this->sgabConfig['uri'], 'webanalyze') !== false)) 
			{
				$this->sgabConfig['whitebot'] = 1;
			}


			if ($this->sgabConfig['whitebot'] == 0) {
				foreach ($this->sgabWhitelist as $ab_line => $ab_sign) {
					if (mb_stripos($this->sgabConfig['useragent'], $ab_line, 0, 'utf-8') !== false) {
						$this->sgabConfig['whitebot'] = 1; 
						break;
					}
				}

				if ($this->sgabConfig['whitebot'] == 1) {
					$this->sgabConfig['whitebot'] = 0;
					$this->sgabConfig['ptr'] = gethostbyaddr($this->sgabConfig['ip']);
					foreach ($ab_sign as $ab_line) {
						if (mb_stripos($this->sgabConfig['ptr'], $ab_line, 0, 'utf-8') !== false) {
							if ($ab_line != '.') {
								if ($this->sgabConfig['short_mask'] != 1) {
									$this->sgabConfig['ip_short'] = $this->sgabConfig['ip'];
								}
								file_put_contents(__DIR__.'/whitebot/'.$this->sgabConfig['ip_short'].'.txt', $this->sgabConfig['ip'].' '.$this->sgabConfig['ptr'].' '.$this->sgabConfig['useragent'], LOCK_EX);
							}
							$this->sgabConfig['whitebot'] = 1; 
							break;
						}
					}
				}
			}

			$this->sgabConfig['antibot_ok'] = md5($this->sgabConfig['host'].$this->sgabConfig['useragent'].$this->sgabConfig['ip']);

			$this->sgabConfig['antibot'] = isset($_COOKIE['antibot']) ? trim($_COOKIE['antibot']) : '';

			if(isset($_POST['submit']) AND isset($_POST['antibot'])) {
				$this->sgabConfig['antibot'] = isset($_POST['antibot']) ? trim(strip_tags($_POST['antibot'])) : 0;
				setcookie('antibot', $this->sgabConfig['antibot'], time()+86400, '/', $this->sgabConfig['host']);
				if (!isset($this->sgabConfig['ptr'])) {
					$this->sgabConfig['ptr'] = gethostbyaddr($this->sgabConfig['ip']);
				}

			}


			if ($this->sgabConfig['whitebot'] == 0 AND $this->sgabConfig['antibot_ok'] != $this->sgabConfig['antibot']) {
				header('Content-Type: text/html; charset=UTF-8');
				header('X-Robots-Tag: noindex');
				header('X-Frame-Options: DENY');
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				$this->showCheckScreen();
				if ($this->sgabConfig['antibot_log'] == 1) {
					if (!isset($this->sgabConfig['ptr'])) {
						$this->sgabConfig['ptr'] = gethostbyaddr($this->sgabConfig['ip']);
					}
					file_put_contents(__DIR__.'/botlog1.txt', $this->sgabConfig['ip'].' '.$this->sgabConfig['ptr'].' '.$this->sgabConfig['host'].' '.$this->sgabConfig['useragent']."\n", FILE_APPEND | LOCK_EX);
				}
				exit;
			}

			if ($this->sgabConfig['antibot_log2'] == 1) {
				if ($this->sgabConfig['whitebot'] == 0 AND $this->sgabConfig['antibot_ok'] == $this->sgabConfig['antibot']) {
					if (!isset($this->sgabConfig['ptr'])) {
						$this->sgabConfig['ptr'] = gethostbyaddr($this->sgabConfig['ip']);
					}
					file_put_contents(__DIR__.'/botlog2.txt', $this->sgabConfig['ip'].' '.$this->sgabConfig['ptr'].' '.$this->sgabConfig['host'].' '.$this->sgabConfig['useragent']."\n", FILE_APPEND | LOCK_EX);
				}
			}

		}

		
		public function showCheckScreen() {
			$page = '<!DOCTYPE HTML>
	<html lang="en-US">
	<head>
	  <meta charset="UTF-8" />
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
	  <meta name="robots" content="noindex, nofollow" />
	  <meta name="referrer" content="unsafe-url" />
	  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	  <title>Just a moment...</title>
	  <style type="text/css">
		html, body {width: 100%; height: 100%; margin: 0; padding: 0;}
		body {background-color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 100%;}
		h1 {font-size: 1.5em; color: #404040; text-align: center;}
		p {font-size: 1em; color: #404040; text-align: center; margin: 10px 0 0 0;}
		#spinner {margin: 0 auto 30px auto; display: block;}
		.attribution {margin-top: 20px;}
		@-webkit-keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
		@keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
		.bubbles { background-color: #404040; width:15px; height: 15px; margin:2px; border-radius:100%; -webkit-animation:bubbles 0.6s 0.07s infinite ease-in-out; animation:bubbles 0.6s 0.07s infinite ease-in-out; -webkit-animation-fill-mode:both; animation-fill-mode:both; display:inline-block; }
	  </style>
	  
	</head>
	<body>
	<script>
	if (window.location.hostname !== window.atob("'. base64_encode($this->sgabConfig['host']) .'")) {
	window.location = window.atob("'. base64_encode('http://'.$this->sgabConfig['host'].$this->sgabConfig['uri']).'");
	}

	function timer(){
	 var obj=document.getElementById(\'timer\');
	 obj.innerHTML--;
	 if(obj.innerHTML==0){
	setTimeout(function(){},1000);
	document.getElementById("btn").innerHTML = window.atob(\''. base64_encode('<form action="" method="post"><input name="antibot" type="hidden" value="'.$this->sgabConfig['antibot_ok'].'"><input type="submit" name="submit" value="Click to continue"></form>').'\');
	}
	 else{setTimeout(timer,1000);}
	}
	setTimeout(timer,1000);
	</script>
	  <table width="100%" height="100%" cellpadding="20">
		<tr>
		  <td align="center" valign="middle">
			  <div class="cf-browser-verification cf-im-under-attack">
	  <noscript><h1 style="color:#bd2426;">Please turn JavaScript on and reload the page.</h1></noscript>
	  <div id="cf-content">
		<div>
		  <div class="bubbles"></div>
		  <div class="bubbles"></div>
		  <div class="bubbles"></div>
		</div>
		<h1>Checking your browser before accessing '.$this->sgabConfig['host'].'.</h1>
		<p>This process is automatic. Your browser will redirect to your requested content shortly.</p>
		<p id="btn">Please allow up to <span id="timer">5</span> seconds&hellip;</p>
	  </div>
	</div>
	<div class="attribution">
	<p><a href="https://seoguarding.com/" target="_blank" style="font-size: 12px;">BadBot protection by SeoGuarding.com</a></p>
	<p>Your IP: '. $this->sgabConfig['ip'] .'</p>
			  </div>
		  </td>
		</tr>
	  </table>

	<script type="text/javascript" src="https://www.siteguarding.com/antibot/check.php?id='.crc32($this->sgabConfig['antibot_ok']).'&rand='.time().'"></script>
	<script>
	if (typeof SGAntiBot == "undefined"){
	var script = document.createElement(\'script\');
	script.src = "https://www.siteguarding.com/antibot/check.php?id='.crc32($this->sgabConfig['antibot_ok']).'&rand='.time().'";
	document.getElementsByTagName(\'head\')[0].appendChild(script);
	}
	if (typeof antibot != "undefined") {
	if (antibot == window.atob("'.base64_encode($this->sgabConfig['antibot_ok']).'")) {
	var d = new Date();
	d.setTime(d.getTime() + (1*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = "antibot=" + antibot + "; " + expires + "; path=/;";
	setTimeout(location.reload.bind(location), 0);
	}
	}
	</script>
	<center>'.$this->sgabConfig['counter'].'</center>
	</body>
	</html>
	';
			
			print $page;

		}
		
	}
}
$sgab = new SgSeoAntiBot();
$sgab->analyze();