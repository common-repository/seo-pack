<?php

if (!class_exists('RT_SEO_Ssl')) {
	class RT_SEO_Ssl extends RT_SEO_Helper
	{
		
		var $http_urls = array();
		
		function __construct() {
			add_option('rt_seo_ssl', 0);
			add_option('rt_seo_locations', "all");
			add_option('rt_seo_activation_redirect', true);
			add_option('rt_seo_http_redirect', 0);
		}
		
		public function ssl_panel() {


		?>
		<form method="post" action="admin.php?page=rt_seo_ssl">
		<?php
			wp_nonce_field( 'name_3d2k3nd2d' );
		?>
		<div id="main" class="ui main container" style="float: left;margin-top: 20px; width:99%; max-width: 1100px!important;">
				<h2 class="ui dividing header">SSL (https)</h2>
                
                <div class="ui grid">
                <div class="sixteen wide column">
			<?php

				$this->update_settings();
				$rt_seo_ssl = get_option('rt_seo_ssl');
				$rt_seo_locations = get_option('rt_seo_locations', 'all');
				$rt_seo_http_redirect = get_option('rt_seo_http_redirect');

				if (!$is_ssl_supported = $this->is_ssl_supported()) {
					update_option('rt_seo_ssl', 0, true);
					
					$msg_data = array(
						'type' => 'warning',
						'content' => __('It appears like your server does not support SSL or SSL certificate is not valid, please ask your hosting provider to enable it or you can order your SSL certificate from <a href="' . RT_SEO_Helper::$LINKS['get_ssl'] . '" target="blank">SEOGuarding.com (installation and configuration is included)</a>', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
				} else if (defined('WP_DEBUG') && WP_DEBUG === true) {
					$msg_data = array(
						'type' => 'warning',
						'content' => __('DEBUG mode is enabled. SSL settings are disabled. To enable SSL settings please edit wp-config.php and set WP_DEBUG = false.', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
					
				} else if (!$rt_seo_ssl && !is_ssl()) {
					$msg_data = array(
						'type' => 'info',
						'content' => __('By turning on SSL, your server must support SSL (https://) or this could make your website inaccessible.' . "<br>" . 'Upon clicking "Update", you will be asked to login to your WordPress dashboard again if the protocol changes.' . "<br>" . 'To disable SSL settings please edit wp-config.php and set WP_DEBUG = true.', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
					
				} else if ($rt_seo_ssl && !is_ssl()) {
					$msg_data = array(
						'type' => 'warning',
						'size' => 'medium',
						'content' => __('Something went wrong, please contact us', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
					
				} else if (!is_ssl() && $this->is_ssl_front()) {
					$msg_data = array(
						'type' => 'ok',
						'size' => 'small',
						'content' => __('SSL is already enabled for Frontend', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
					
					$msg_data = array(
						'type' => 'warning',
						'size' => 'small',
						'content' => __('SSL is disabled for Backend', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
					
				} else if (is_ssl() && !$this->is_ssl_front()) {
					$msg_data = array(
						'type' => 'ok',
						'size' => 'small',
						'content' => __('SSL is already enabled for Backend', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
					
					$msg_data = array(
						'type' => 'warning',
						'size' => 'small',
						'content' => __('SSL is disabled for Frontend', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
					
				} else {
					$msg_data = array(
						'type' => 'ok',
						'size' => 'small',
						'content' => __('SSL is already enabled for whole website (backend and frontend)', 'rt_seo')
					);
					$this->Print_MessageBox($msg_data);
					
				} 
                
                
				$msg_data = array(
					'type' => 'info',
					'size' => 'small',
					'content' => __('<b>Tips:</b> If something goes wrong, you can enable <b>DEBUG mode</b> (<a target="_blank" href="' . RT_SEO_Helper::$LINKS['wp_debug'] . '">see how to enable Debugging in WordPress <i class="linkify icon"></i></a>) https redirection will be automatically disabled.', 'rt_seo')
				);
				$this->Print_MessageBox($msg_data);
			
			?>
            
           
            
			<div class="ui segment" style="max-width: 1100px;">
				<div class="ui grid">
				
				

					<div class="eight wide column">
						<h3 class="ui dividing header">
							<p style="font-size:16px;font-weight:bold;">
							<?php _e( 'Enable HTTPS?', 'rt_seo' )?>
							<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rt_seo_ssl_tip');"><i class="question circle icon"></i></a></p>
						</h3>
						<p id="rt_seo_ssl_tip" style="display:none">
							<?php _e( 'Turn this on to globally redirect all URLs and resources', 'rt_seo' )?>
						</p>

					</div>

					<div class="ui top aligned center aligned eight wide column">
						<div class="ui top aligned center aligned form full_h">
							<div class="field">
								<div class="ui fitted toggle checkbox">
									<input <?php disabled($is_ssl_supported, false); ?> type="checkbox" name="rt_seo_ssl" <?php if ( $rt_seo_ssl ) echo "checked=\"1\""; ?>  onclick="toggleVisibilityInstall('rt_seo_options');toggleVisibilityInstall('rt_seo_http_redirect');"/>
									<label></label>
								</div>
							</div>
						</div>
					</div>
					<div style="width: 100%; margin: auto; margin-bottom:20px;text-align:left; display:<?php if ($rt_seo_ssl) echo 'block'; else echo 'none'; ?>" id="rt_seo_options">

                        <?php
        				$msg_data = array(
        					'type' => 'info_white',
        					'size' => 'small',
		'content' => __('You need to have valid SSL certificate on the server. Make sure that your hoster is installed it for you or you can order SSL certificate from <a target="_blank" href="' . RT_SEO_Helper::$LINKS['get_ssl'] . '">SEOGuarading.com <i class="linkify icon"></i></a>', 'rt_seo')
        				);
        				$this->Print_MessageBox($msg_data);
                        ?>


							<div class="ui grid">
								<div class="eleven wide column">
									<h3 class="ui header">
										<p style="font-weight:bold;">
										<?php _e( 'HTTPS locations', 'rt_seo' )?>
										<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rt_seo_locations_tip');"><i class="question circle icon"></i></a></p>
									</h3>
									<p id="rt_seo_locations_tip" style="display:none">
										<?php _e( 'Choose where you want http:// URLs to be changed to https://', 'rt_seo' )?>
									</p>
								</div>
								<div class="ui top aligned left aligned four wide column">
									<div class="ui top aligned left aligned form full_h">
										<div class="ui form">
										  <div class="grouped fields">

											<div class="field">
											  <div class="ui slider checkbox">
												<input type="radio" name="rt_seo_locations" value="all" <?php if ( $rt_seo_locations == 'all') echo "checked=\"checked\""; ?>>
												<label>Everywhere</label>
											  </div>
											</div>
											<div class="field">
											  <div class="ui slider checkbox">
												<input type="radio" name="rt_seo_locations" value="backend" <?php if ( $rt_seo_locations == 'backend') echo "checked=\"checked\""; ?>>
												<label>Backend only</label>
											  </div>
											</div>
											<div class="field">
											  <div class="ui slider checkbox">
												<input type="radio" name="rt_seo_locations" value="frontend" <?php if ( $rt_seo_locations == 'frontend') echo "checked=\"checked\""; ?>>
												<label>Frontend only</label>
											  </div>
											</div>
										  </div>
										</div>
									</div>
								</div>
							</div>
						</div>

					<div style="width: 100%; margin: auto; margin-bottom:20px;text-align:left; display:<?php if ($rt_seo_ssl) echo 'none'; else echo 'block'; ?>" id="rt_seo_http_redirect">

						<div class="ui grid">
							<div class="eight wide column">
								<h3 class="ui header">
									<p style="font-weight:bold;">
									<?php _e( 'Redirect all pages to HTTP', 'rt_seo' )?>
									<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rt_seo_mobile_sitemap_tip');"><i class="question circle icon"></i></a></p>
								</h3>
								<p id="rt_seo_mobile_sitemap_tip" style="display:none">
									<?php _e( 'With SSL disabled, you can turn on this setting to redirect all https:// pages to non-SSL automatically.', 'rt_seo' )?>
								</p>
							</div>
							<div class="ui top aligned center aligned eight wide column">
								<div class="ui top aligned center aligned form full_h">
									<div class="field">
									  <div class="ui fitted toggle checkbox">
											<input type="checkbox" name="rt_seo_http_redirect" <?php if ( $rt_seo_http_redirect ) echo "checked=\"1\""; ?>/>
										<label></label>
									  </div>
									</div>
								</div>
							</div>

						</div>
					</div>

				</div>
				</div>
                
                <input type="submit" value="Update" class="ajax_button ui medium secondary button">
                
			</div>
        
		</div>
		</div>

		
		</form>
		<?php
		
		}

		

		function update_settings() {		
										
			if (!empty($_POST) && check_admin_referer( 'name_3d2k3nd2d' )) {	


				if (is_multisite()) {
					update_site_option('rt_seo_global', 0);
					
					foreach ($_POST as $pkey => $pval) {
						update_site_option(esc_html($pkey), esc_html($pval));
					}
					
					wp_cache_flush();
					$this->check_network_ssl();
				
					
					do_action('rt_seo_network_settings_saved', $_POST);
					
				} else {
			
					update_option('rt_seo_ssl', 0);
					update_option('rt_seo_http_redirect', 0);

					
					foreach ($_POST as $pkey => $pval) {
						update_option(esc_html($pkey), esc_html($pval));
					}
					
					
					wp_cache_flush();
					$this->check_ssl();
					
					do_action('rt_seo_settings_saved', $_POST);
				}

				$message_data = array(
					'type' => 'info_white',
					'header' => '',
					'message' => __('SSL settings have been saved', 'rt_seo'),
					'button_text' => '',
					'button_url' => '',
					'help_text' => ''
				);
				//echo '<div style="max-width:1100px;margin-top: 10px;margin-bottom: 15px;">';
				$this->PrintIconMessage($message_data);
				//echo '</div>';


			}
			
		}

		
		function is_ssl_supported() {	
			global $rt_seo_http_code;
					
			$has_ssl = false;
			$url = home_url(false, 'https');
			
			if ($response = $this->http_get($url)) {
				if (!empty($response['code']) && $response['code'] == 200) {
					$has_ssl = true;
				}	
			}
	
			return apply_filters('is_ssl_supported', $has_ssl);
		}
		

		
		function is_ssl_front() {	
			global $rt_seo_http_code;
					
			$is_ssl_front = true;
			$url = home_url(false, 'http');
			
			if ($response = $this->http_get($url)) {
				if (!empty($response['code']) && $response['code'] == 200) {
					$is_ssl_front = false;
				}	
			}
	
			return apply_filters('is_ssl_front', $is_ssl_front);
		}
		
		function check_ssl() {			
			
			if (!empty($_POST['rt_seo_check'])) {
				return;
			}
											
			$rt_seo_ssl = get_option('rt_seo_ssl');
			
			$rt_seo_http_redirect = get_option('rt_seo_http_redirect');	
			$nonssl = (!empty($rt_seo_http_redirect)) ? true : false;
				
			if (!empty($rt_seo_ssl)) {
				$rt_seo_locations = get_option('rt_seo_locations');
				$doredirect = false;
				$redirecturl = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
				
				switch ($rt_seo_locations) {
					case 'backend'				:
						if ((is_admin() && !defined('DOING_AJAX')) || $GLOBALS['pagenow'] === 'wp-login.php') {
							$doredirect = true;
							$nonssl = false;
						}
						break;
					case 'frontend'				:
						if (!is_admin() && $GLOBALS['pagenow'] !== 'wp-login.php') {							
							$doredirect = true;
							$nonssl = false;
						}
						break;
					case 'all'					:
					default 					:
						$doredirect = true;
						$nonssl = false;
						break;
				}
				
				if (!empty($doredirect)) {
					if (!is_ssl()) {
						$this->redirect($redirecturl);
					}
				}	
			}
			
			if (!empty($nonssl) && $nonssl == true) {			
				if (is_ssl()) {					
					$redirecturl = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
					$this->redirect($redirecturl);
				}
			}
		}
		
		function check_network_ssl() {											
			$rt_seo_global = get_site_option('rt_seo_global');
				
			if (!empty($rt_seo_global)) {
				$redirecturl = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
				
				if (!is_ssl()) {
					$this->redirect($redirecturl);
				}
			}
		}
		
		function redirect($redirecturl = null) {
			if (!empty($redirecturl)) {
				if (headers_sent()) {					
					?>
						
					<script type="text/javascript">
					document.location = "<?php echo esc_attr(stripslashes($redirecturl)); ?>";
					</script>
					
					<?php
				} else {
					wp_redirect($redirecturl, "301");
					exit();
				}
			}
		}
	
		function replace_https($value = null) {	
			if (!empty($value)) {
				$rt_seo_ssl = get_option('rt_seo_ssl');
				if (!empty($rt_seo_ssl)) {	
					if (is_ssl()) {
						if (!is_array($value) && !is_object($value)) {
							$value = preg_replace('|/+$|', '', $value);
							$value = preg_replace('|http://|', 'https://', $value);
						}		
					}
				}
			}
		
			return apply_filters('rt_seo_replace_https', $value);
		}

		
		function filter_buffer($buffer = null) {
			$buffer = $this->replace_insecure_links($buffer);
			return $buffer;
		}
		
		function start_buffer() {
			// Check if SSL is enabled and current protocol is SSL
			$rt_seo_ssl = get_option('rt_seo_ssl');
			if (!empty($rt_seo_ssl) && is_ssl()) {
				$this->build_url_list();
				ob_start(array($this, "filter_buffer"));
			}
		}
		
		function stop_buffer() {
			// Check if SSL is enabled and current protocol is SSL
			$rt_seo_ssl = get_option('rt_seo_ssl');
			if (!empty($rt_seo_ssl) && is_ssl()) {
				if (ob_get_length()) {
					ob_end_flush();
				}
			}
		}
		
		function build_url_list() {
			$home = str_replace("https://", "http://" , get_option('home'));
			$home_no_www  = str_replace("://www.", "://", $home);
			$home_yes_www = str_replace("://", "://www.", $home_no_www);
			$escaped_home = str_replace("/", "\/", $home);
			
			$this->http_urls = array(
				$home_yes_www,
				$home_no_www,
				$escaped_home,
				"src='http://",
				'src="http://',
			);
		}
		
		function replace_insecure_links($str = null) {			
			$search_array = apply_filters('rt_seo_replace_search_list', $this->http_urls);
			$ssl_array = str_replace(array("http://", "http:\/\/"), array("https://", "https:\/\/"), $search_array);
			$str = str_replace($search_array, $ssl_array, $str);
			
			$patterns = array(
				'/url\([\'"]?\K(http:\/\/)(?=[^)]+)/i',
				'/<link .*?href=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
				'/<meta property="og:image" .*?content=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
				'/<form [^>]*?action=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
				'/<(script|link|base|img|form)[^>]*(href|src|action)=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
			);
			
			$str = preg_replace($patterns, 'https://', $str);
			
			global $rt_seo_bodydata;
			if (empty($rt_seo_bodydata)) {
				$str = str_replace("<body ", "<body data-rtseossl='1' ", $str);
				$rt_seo_bodydata = true;
			}
			
			return apply_filters("rt_seo_replace_output", $str);
		}
		

	}
	
	$RT_SEO_Ssl = new RT_SEO_Ssl();
	
	if (defined('WP_DEBUG') && WP_DEBUG !== true) {
		add_action('admin_init', array($RT_SEO_Ssl, 'start_buffer'), 10, 1);
		add_action('init', array($RT_SEO_Ssl, 'start_buffer'), 10, 1);
		add_action('shutdown', array($RT_SEO_Ssl, 'stop_buffer'), 10, 1);
		

		

		
		if (is_multisite()) {
			add_action('wp_loaded', array($RT_SEO_Ssl, 'check_network_ssl'), 10, 1);	
		} else {
			add_action('wp_loaded', array($RT_SEO_Ssl, 'check_ssl'), 10, 1);
		}
		
		
		add_filter('upload_dir', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('option_siteurl', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('option_home', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('option_url', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('option_wpurl', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('option_stylesheet_url', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('option_template_url', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('wp_get_attachment_url', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('widget_text', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('login_url', array($RT_SEO_Ssl, 'replace_https'));
		add_filter('language_attributes', array($RT_SEO_Ssl, 'replace_https'));
	}


}


