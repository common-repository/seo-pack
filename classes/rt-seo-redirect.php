<?php

if (!class_exists('RT_SEO_Redirect')) {
	class RT_SEO_Redirect extends RT_SEO_Helper
	{
		
		var $http_urls = array();
		
		function __construct() {

		}
		
		public function redirect_panel() {

		
		?>
		<script>
			//todo: This should be enqued
			jQuery(document).ready(function(){
				jQuery('span.rtseo-remove-redirect').html('Delete').css({'color':'red','cursor':'pointer'}).click(function(){
					var confirm_delete = confirm('Remove This Redirect?');
					if (confirm_delete) {
						
						// remove element and submit
						jQuery(this).parent().parent().remove();
						jQuery('#rt_seo_redirect_form').submit();
						
					}
				});

			});
		</script>
		<form method="post" id="rt_seo_redirect_form" action="admin.php?page=rt_seo_redirect">
		<?php
			wp_nonce_field( 'name_n3j45n34k5j3' );
		?>
		<div id="main" class="ui main" style="margin-top: 20px; width:99%;max-width: 1100px;">
				<h2 class="ui dividing header">Redirects</h2>
				<?php $this->update_redirects();?>
			<div class="ui segment">
				<h4 class="ui dividing header">Custom 301 redirects</h4>

				<table class="ui selectable celled table small">
					<thead>
						<tr>
							<th colspan="2">Link</th>
							<th colspan="2">New Address</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2"><small>example: /about.htm</small></td>
							<td colspan="2"><small>example: <?php echo get_option('home'); ?>/about/</small></td>
						</tr>
						<?php echo $this->get_redirects(); ?>
						<tr>
							<td style="width:44%;"><div class="ui fluid input"><input type="text" name="rt_seo_redirects[link][]" value="" style="width:99%;" /></div></td>
							<td style="width:2%;">&raquo;</td>
							<td style="width:44%;"><div class="ui fluid input"><input type="text" name="rt_seo_redirects[new_address][]" value="" style="width:99%;" /></div></td>
							<td><span class="rtseo-remove-redirect ui mini inverted red button">Remove</span></td>
						</tr>
					</tbody>
				</table>
				<?php $rt_seo_redirects_regular_exp = (get_option('rt_seo_redirects_regular_exp') === 'true' ? ' checked="checked"' : ''); ?>
				<div class="inline field">
				
				  <div class="ui toggle checkbox">

						<input type="checkbox"  id="rtseo-regular-exp" name="rt_seo_redirects[regular_exp]" <?php if ( $rt_seo_redirects_regular_exp ) echo "checked=\"1\""; ?>/>
					<label>Use regular expressions? </label>
				  </div>
				</div>

			</div>
			
			<div class="ui segment" style="max-width: 1100px;">
				<h4 class="ui dividing header">Custom 404 redirect</h4>
				<table class="ui selectable celled table small">
					<thead>
						<tr>
							<th>Redirect To:</th>
							<th>Type of Redirect</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><small>example: sample-page.htm</small></td>
							<td><small>301 or 302 redirect</small></td>
						</tr>
						<tr>
							<td style="width:45%;"><div class="ui fluid input"><input type="text" name="rt_seo_redirects[404_redirect_link]" value="<?php if (get_option('rt_seo_redirects_404_redirect_link', false)) echo get_option('rt_seo_redirects_404_redirect_link'); ?>" style="width:99%;" /></div></td>
							<td style="width:45%;">
						<!--	<input type="text" name="rt_seo_redirects[new_address][]" value="" style="width:99%;" /> -->
							<select name="rt_seo_redirects[404_redirect_type]" class="ui fluid dropdown">
							    <option value="301" <?php if (get_option('rt_seo_redirects_404_redirect_type') == 301) echo 'selected'; ?>>301 Moved Permanently</option>
							    <option value="302" <?php if (get_option('rt_seo_redirects_404_redirect_type') == 302) echo 'selected'; ?>>302 Temporarily Moved</option>
							</select>
							</td>
						</tr>
					</tbody>
				</table>

			</div>
			
			
		</div>


		<div style="max-width:1100px;margin-top: 10px;margin-bottom: 15px;">
			<input type="submit" value="Update" class="ui medium secondary floated right button mrt-top-15">
		</div>
		
		</form>
		<?php
		
		}

		public function get_redirects() {
			$redirects = get_option('rt_seo_redirects');
			$output = '';
			if (!empty($redirects)) {
				foreach ($redirects as $link => $new_address) {
					$output .= '
					
					<tr>
						<td><input type="text" name="rt_seo_redirects[link][]" value="'.$link.'" style="width:99%" /></td>
						<td>&raquo;</td>
						<td><input type="text" name="rt_seo_redirects[new_address][]" value="'.$new_address.'" style="width:99%;" /></td>
						<td><span class="rtseo-remove-redirect ui mini inverted red button"></span></td>
					</tr>
					
					';
				}
			} 
			return $output;
		}
		
		public function validate_url( $base ) {
			$url = home_url('/') . $base;
			$handle = curl_init( trailingslashit( $url ) );
			curl_setopt( $handle,  CURLOPT_RETURNTRANSFER, TRUE );
			$response = curl_exec( $handle );
			$http_code = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
			if ( $http_code == 404 || $http_code == 301 ) {
				$message = 'URL does not exists or is redirected! Please type a valid URL!';
				return array( 'success' => false, 'error' => $message );
			}
			return array( 'success' => true, 'error' => '' );
			curl_close( $handle );
		}	
		
		public function update_redirects() {
			if ( !current_user_can('manage_options') )  { wp_die( 'You do not have sufficient permissions to access this page.' ); }
			
			if (!empty($_POST) && check_admin_referer( 'name_n3j45n34k5j3' )) {	
				
				$data = $_POST['rt_seo_redirects'];

				if (isset($data['404_redirect_link']) && $data['404_redirect_link'] != '') {

					$result = $this->validate_url($data['404_redirect_link']);
					if(!$result['success']) {
						
						$msg_data = array(
							'type' => 'error',
							'size' => 'mini',
							'content' => $result['error']
						);
						$this->Print_MessageBox($msg_data);
						delete_option('rt_seo_redirects_404_redirect_link');
						return false;
					} else {	
						update_option('rt_seo_redirects_404_redirect_link', $data['404_redirect_link']);
					}
				}
				else {
					delete_option('rt_seo_redirects_404_redirect_link');
				}
				
				if (isset($data['404_redirect_type'])) {
					update_option('rt_seo_redirects_404_redirect_type', $data['404_redirect_type']);
				}
				else {
					delete_option('rt_seo_redirects_404_redirect_type');
				}
				
				$redirects = array();
				
				if (isset($data['link'])) {
					for($i = 0; $i < sizeof($data['link']); ++$i) {
						$link = trim( sanitize_text_field( $data['link'][$i] ) );
						$new_address = trim( sanitize_text_field( $data['new_address'][$i] ) );
					
						if ($link == '' && $new_address == '') { continue; }
						else { $redirects[$link] = $new_address; }
					}
				}
				
				update_option('rt_seo_redirects', $redirects);
				
				if (isset($data['regular_exp'])) {
					update_option('rt_seo_redirects_regular_exp', 'true');
				}
				else {
					delete_option('rt_seo_redirects_regular_exp');
				}
				

				
				wp_cache_flush();
				
				$msg_data = array(
					'type' => 'ok',
					'size' => 'mini',
					'content' => __('Redirect settings have been saved', 'rt_seo')
				);


				$this->Print_MessageBox($msg_data);

				
			}
		}

		public function redirect_404() {
			global $wp_query;
			if ( $wp_query->is_404 ) {
				$redirect_404_link    = get_option( 'rt_seo_redirects_404_redirect_link' );
				$redirect_404_type    = get_option( 'rt_seo_redirects_404_redirect_type' );
				if ( $redirect_404_link ) {
					wp_redirect( home_url('/') . $redirect_404_link, intval( $redirect_404_type ) );
					exit;
				}
			}
		}
		
		public function redirect() {
			
			$userrequest = str_ireplace(get_option('home'),'',$this->get_address());
			$userrequest = rtrim($userrequest,'/');
			
			$redirects = get_option('rt_seo_redirects');
			if (!empty($redirects)) {
				
				$regex = get_option('rt_seo_redirects_regular_exp');
				$do_redirect = '';
				
				foreach ($redirects as $link => $new_address) {
					if ($regex === 'true' && strpos($link,'*') !== false) {
						if ( strpos($userrequest, '/wp-login') !== 0 && strpos($userrequest, '/wp-admin') !== 0 ) {
							$link = str_replace('*','(.*)',$link);
							$pattern = '/^' . str_replace( '/', '\/', rtrim( $link, '/' ) ) . '/';
							$new_address = str_replace('*','$1',$new_address);
							$output = preg_replace($pattern, $new_address, $userrequest);
							if ($output !== $userrequest) {
								$do_redirect = $output;
							}
						}
					}
					elseif(urldecode($userrequest) == rtrim($link,'/')) {
						$do_redirect = $new_address;
					}
					
					if ($do_redirect !== '' && trim($do_redirect,'/') !== trim($userrequest,'/')) {

						if (strpos($do_redirect,'/') === 0){
							$do_redirect = home_url().$do_redirect;
						}
						header ('HTTP/1.1 301 Moved Permanently');
						header ('Location: ' . $do_redirect);
						exit();
					}
					else { unset($redirects); }
				}
			}
		}

		public function get_address() {
			
			$protocol = 'http';
			if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
    			$protocol .= "s";
			}

			return $protocol . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		} 

	}
	
	$RT_SEO_Redirect = new RT_SEO_Redirect();
	
	add_action('init', array($RT_SEO_Redirect,'redirect'), 1);
	add_action('wp', array($RT_SEO_Redirect,'redirect_404'), 1);


}


