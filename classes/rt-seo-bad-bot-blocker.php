<?php

if (!class_exists('RT_SEO_BadBotBlocker')) {
	class RT_SEO_BadBotBlocker extends RT_SEO_Helper
	{
		
		private $antiBotFile = '';
		private $whiteListFile = '';
		
		function __construct() {
			parent::__construct();
			$this->antiBotFile = str_replace(array("/", "\\"), DIRSEP, __DIR__ . '/sgAntiBot.php');
			$this->whiteListFile = str_replace(array("/", "\\"), DIRSEP, __DIR__ . '/wl.dat');
		}
		
		public function badbot_panel() {

		
		?>
		<form method="post" id="rt_seo_badbot_blocker_form" action="admin.php?page=rt_seo_badbot_blocker">
		<?php
			wp_nonce_field( 'name_n3j45n34k5j3' );
		?>
		<div id="main" class="ui main" style="margin-top: 20px; width:99%;max-width: 1100px;">
				<h2 class="ui dividing header">Bad Bot Blocker</h2>
				<?php if (!$this->membership) : ?>
					<div class="ui red message">Free version has some limits, please upgrade to <a target="_blank" href="<?php echo RT_SEO_Helper::$LINKS['get_pro']; ?>">PRO version</a></div>
				<?php endif; ?>
				<?php $this->update_badbot(); ?>
	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui dividing header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Enable / Disable BadBot Blocker ', 'rt_seo')?>
					</p>
				</h3>
				<p>
					<?php _e("Once it's enabled, BadBot Blocker will protect your website against bad bots", 'rt_seo')?>
				</p>
			</div>
			<?php $rt_seo_badbot_blocker_status = (get_option('rt_seo_badbot_blocker_status') == 1 ? ' checked="checked"' : ''); ?>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rt_seo_badbot_blocker[enabled]" <?php if ( $rt_seo_badbot_blocker_status )  echo $rt_seo_badbot_blocker_status; ?>/>
							<label></label>
					    </div>
				    </div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui dividing header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Whitelist:', 'rt_seo')?>
					</p>
				</h3>
				<p>
					<?php _e('Add User-Agents of bots to the whitelist ( separate by , )', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="55" rows="5" style="resize: none;" <?php if ($this->membership != 1) echo 'disabled'; ?> name="rt_seo_badbot_blocker[whitelist]"><?php if (get_option('rt_seo_badbot_blocker_whitelist', false)) echo get_option('rt_seo_badbot_blocker_whitelist'); ?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>	
	
	
			<div class="ui segment">


		<b>Whitelisted bots by default:</b>
		<div class="ui bulleted list">
			<div class="item">Yandex</div>
			<div class="item">Google</div>
			<div class="item">AdsBot-Google</div>
			<div class="item">Bing.com</div>
			<div class="item">Freenom.com</div>
			<div class="item">Facebook</div>
			<div class="item">Twitter</div>
			<div class="item">Telegram</div>
		</div>



			</div>

			
			
		</div>


		<div style="max-width:1100px;margin-top: 10px;margin-bottom: 15px;">
			<input type="submit" value="Update" class="ui medium secondary floated right button mrt-top-15">
		</div>
		
		</form>
		<?php
		
		}
		
		public function update_badbot() {
			if ( !current_user_can('manage_options') )  { wp_die( 'You do not have sufficient permissions to access this page.' ); }
			if ($this->membership != 1) delete_option('rt_seo_badbot_blocker_whitelist');
			
			if (!empty($_POST) && check_admin_referer( 'name_n3j45n34k5j3' )) {	
				
				$data = $_POST['rt_seo_badbot_blocker'];

				if ($this->membership != 1) $data['whitelist'] = '';
				
				if (isset($data['enabled']) && $data['enabled']) {
					if ($this->patchWPConfig(true)) {
						file_put_contents($this->whiteListFile, $data['whitelist']);
						update_option('rt_seo_badbot_blocker_status', true);
					} else {
						$msg_data = array(
							'type' => 'error',
							'size' => 'mini',
							'content' => __('Can\'t patch wp-config.php file. Please check perrmissions', 'rt_seo')
						);
						$this->Print_MessageBox($msg_data);
						return false;
					}
				} else {
					if ($this->patchWPConfig(false)) {
						@unlink($this->whiteListFile);
						delete_option('rt_seo_badbot_blocker_status');
					} else {
						$msg_data = array(
							'type' => 'error',
							'size' => 'mini',
							'content' => __('Can\'t patch wp-config.php file. Please check perrmissions', 'rt_seo')
						);
						$this->Print_MessageBox($msg_data);
						return false;
					}

				}
				
				if (isset($data['whitelist'])) {
					update_option('rt_seo_badbot_blocker_whitelist', $data['whitelist']);
				} else {
					delete_option('rt_seo_badbot_blocker_whitelist');
				}
				
				wp_cache_flush();
				
				$msg_data = array(
					'type' => 'ok',
					'size' => 'mini',
					'content' => __('Antibot settings have been saved', 'rt_seo')
				);


				$this->Print_MessageBox($msg_data);

				
			}
		}

		public function patchWPConfig($action) {

			$integration_code = '<?php /* SEOPack Block 8MKM2EFDCS-START */@include_once("'.$this->antiBotFile.'");/* SEOPack Block 8MKM2EFDCS-END */?>';
			
			// Insert code
			if (!defined('ABSPATH') || strlen(ABSPATH) < 8) 
			{
				$scan_path = dirname(dirname(__FILE__));
				$scan_path = str_replace(DIRSEP.'wp-content'.DIRSEP.'plugins'.DIRSEP.'realtime-seo', DIRSEP, $scan_path);
				//echo TEST;
			}
			else $scan_path = ABSPATH;
			
			$filename = $scan_path.DIRSEP.'wp-config.php';

			$handle = fopen($filename, "r");
			if ($handle === false) return false;
			$contents = fread($handle, filesize($filename));
			if ($contents === false) return false;
			fclose($handle);
			
			$pos_code = stripos($contents, '8MKM2EFDCS');
			
			if ($action === false)
			{
				// Remove block
				$contents = str_replace($integration_code, "", $contents);
			}
			else {
				// Insert block
				if ( $pos_code !== false/* && $pos_code == 0*/)
				{
					// Skip double code injection
					return true;
				}
				else {
					// Insert
					

					$contents = $integration_code.$contents;
				}
			}

			$handle = fopen($filename, 'w');
			if ($handle === false) 
			{
				// 2nd try , change file permssion to 666
				$status = chmod($filename, 0666);
				if ($status === false) return false;
				
				$handle = fopen($filename, 'w');
				if ($handle === false) return false;
			}
			
			$status = fwrite($handle, $contents);
			if ($status === false) return false;
			fclose($handle);

			
			return true;
		}
    
		
		
	}
	
	$RT_SEO_BadBotBlocker = new RT_SEO_BadBotBlocker();
	



}


