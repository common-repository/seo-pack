<?php

class RT_SEO_Images extends RT_SEO_Helper
{
	const IMAGES_PER_PAGE = 50;
	
	public function __construct() {
		add_action('wp_ajax_ajaxOptimize', array($this, 'ajaxOptimize') );
		add_action('wp_ajax_ajaxScan', array($this, 'ajaxScan') );
		add_action('wp_ajax_ajaxRestore', array($this, 'ajaxRestore') );
	}
	
	function images_panel() {
		


		?>
		<script>
		function checkAll(){
			if(jQuery('.ui.checkbox.selectAll').checkbox('is checked')){
				jQuery('.ui.checkbox').checkbox('check');
			} else {
				jQuery('.ui.checkbox').checkbox('set unchecked');
			}					
		}
		
		function checkAllNotOptimized(){
			if(jQuery('.ui.checkbox.selectAllNotOptimized').checkbox('is checked')){
				jQuery('.ui.checkbox.not_optimized').checkbox('check');
			} else {
				jQuery('.ui.checkbox.not_optimized').checkbox('set unchecked');
			}					
		}
		
		function rt_seo_optimize()
		{
			urls = [];
			
			jQuery("#ajax_button_rt_seo").hide();
			jQuery("#scanner_ajax_loader_rt_seo").show(); 
			jQuery('.urls:checkbox:checked').each(function(){
				urls.push(this.value);
			});

			
			jQuery.post(
				ajaxurl, 
				{
					'action': 'ajaxOptimize',
					'urls': urls
				}, 
				function(response){
					jQuery("#check_actions").html(response);
					jQuery("#ajax_button_rt_seo").show();
					jQuery("#scanner_ajax_loader_rt_seo").hide(); 
					jQuery('html, body').animate({scrollTop:0}, 'slow');
					setTimeout(function(){
					   window.location.reload(1);
					}, 3000);
				}
			);  
		}
		
		function rt_seo_scan()
		{		
			jQuery("#ajax_button_rt_seo").hide();
			jQuery("#scanner_ajax_loader_rt_seo").show(); 

			
			jQuery.post(
				ajaxurl, 
				{
					'action': 'ajaxScan',
				}, 
				function(response){
					jQuery("#check_actions").html(response);
					jQuery("#ajax_button_rt_seo").show();
					jQuery("#scanner_ajax_loader_rt_seo").hide(); 
					jQuery('html, body').animate({scrollTop:0}, 'slow');
					setTimeout(function(){
					   window.location.reload(1);
					}, 3000);
				}
			);  
		}
		
		function rt_seo_restore()
		{
			urls = [];
			
			jQuery("#ajax_button_rt_seo").hide();
			jQuery("#scanner_ajax_loader_rt_seo").show(); 
			jQuery('.urls:checkbox:checked').each(function(){
				urls.push(this.value);
			});

			
			jQuery.post(
				ajaxurl, 
				{
					'action': 'ajaxRestore',
					'urls': urls
				}, 
				function(response){
					jQuery("#check_actions").html(response);
					jQuery("#ajax_button_rt_seo").show();
					jQuery("#scanner_ajax_loader_rt_seo").hide(); 
					jQuery('html, body').animate({scrollTop:0}, 'slow');
					setTimeout(function(){
					   window.location.reload(1);
					}, 3000);
				}
			);  
		}
		</script>


		<div id="main" class="ui main" style="margin-top: 20px; width:99%;max-width:1300px;">
				<h2 class="ui dividing header">Images Optimizer</h2>
			<div class="ui blue icon small message">
				<i class="exclamation icon"></i>
				<div class="content">
				<b>Tips:</b> Upgrade to PRO version to unblock all the fetures and limits <a target="_blank" href="<?php echo RT_SEO_Helper::$LINKS['get_pro']; ?>"><b>Click to Upgrade</b></a>.                
				</div>
			</div>
			<div id="check_actions">
			<?php
				//$this->checkAction();
				$files = $this->getImages();
				$page = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
			?>
			</div>
		<form method="post" action="admin.php?page=rt_seo_images<?php echo '&page_num=' . $page;?>">
		<?php
			wp_nonce_field( 'name_df64dsf87sky' );
		?>
			<div class="ui segment">
				<h4 class="ui dividing header">Images</h4>
				
				<table class="ui selectable celled table small">
					<thead>
						<tr>
							<th class="sg_center" style="width:4%;" onclick="checkAll();">
								<p style="font-size:10px;">Select all</p>
								<div class="ui centered 	checkbox selectAll">
									<input type="checkbox" value="select_all" >
									<label></label>
								</div>
							</th>
							<th style="width:130px;">Preview</th>
							<th>Filename</th>
							<th>URL</th>
							<th class="sg_center">Size</th>
							<th class="sg_center">Status</th>
						</tr>
					</thead>
					<tbody>

						<?php echo $this->getImagesBlock($page); ?>

					</tbody>
				  <tfoot>
					<tr>
					<th class="sg_center" style="width:4%;" onclick="checkAllNotOptimized();">
						<p style="font-size:10px;">Select all<br>not optimized</p>
						<div class="ui centered checkbox selectAllNotOptimized">
							<input type="checkbox" value="select_all" >
							<label></label>
						</div>
					</th>
					<th colspan="5" class="sg_center">
					  <div class="ui pagination menu" style="padding-top:0;">
					  	<?php if ($page != 1): ?>
							<a class="icon item" href="<?php echo admin_url() . 'admin.php?page=rt_seo_images&page_num=' . ($page - 1); ?>" >
							  <i class="left chevron icon"></i>
							</a>
						<?php else: ?>
							<a class="icon item" href="javascript:;" >
							  <i class="left disabled chevron icon"></i>
							</a>
						<?php endif; ?>	
						
						<?php
						if ((ceil(count($files)/self::IMAGES_PER_PAGE)) <= 10 || $page <= 5) {
							$i = 1;
							$limit = 10;
						} elseif (((ceil(count($files)/self::IMAGES_PER_PAGE)) - $page) <= 5) {
							$i = (ceil(count($files)/self::IMAGES_PER_PAGE)) - 10;
							$limit = (ceil(count($files)/self::IMAGES_PER_PAGE));
						} else {
							$i = $page - 5;
							$limit = $page + 5;
						}
						do {
						?>
						<a class="item <?php if ($i == $page) echo 'active'; ?>" href="<?php echo admin_url() . 'admin.php?page=rt_seo_images&page_num=' . $i; ?>"><?php echo $i; ++$i;?></a>
						<?php
						if ($i > $limit) break;
						} while ($i <= (ceil(count($files)/self::IMAGES_PER_PAGE)));
						?>
						<?php if ($page != (ceil(count($files)/self::IMAGES_PER_PAGE))): ?>
							<a class="icon item" href="<?php echo admin_url() . 'admin.php?page=rt_seo_images&page_num=' . ($page + 1); ?>" >
							  <i class="right chevron icon"></i>
							</a>
						<?php else: ?>
							<a class="icon item" href="javascript:;" >
							  <i class="right disabled chevron icon"></i>
							</a>
						<?php endif; ?>
					  </div>
					</th>
				  </tr></tfoot>
				</table>

			</div>
				
			<div class="sg_center" id="ajax_button_rt_seo" style="max-width:1300px;margin-top: 10px;margin-bottom: 15px;">
				<a  onclick="rt_seo_scan()" href="javascript:;" class="ui medium secondary button mrt-top-15">Scan</a>
				<a  onclick="rt_seo_optimize()" href="javascript:;" class="ui medium secondary button mrt-top-15">Optimize</a>
				<a  onclick="rt_seo_restore()" href="javascript:;" class="ui medium secondary button mrt-top-15">Restore</a>
			</div>				
			<div class="sg_center" id="scanner_ajax_loader_rt_seo" style="display:none;max-width:1300px;margin-top: 10px;margin-bottom: 15px;">
				<img  width="48" height="48" src="<?php echo plugins_url('images/ajax_loader.svg', dirname(__FILE__)); ?>" />
				<p><b>Please wait. Request in progress.</b></p>
			</div>


		</form>
		</div>

		<?php
	}

	


	function getImagesBlock($page) {	
		$images = $this->getImages();
		
		$validator = new RT_SEO_Validation(true);
		$limits = $validator->getLimits();
		
		$offset = ($page - 1) * self::IMAGES_PER_PAGE;

		$output = '';
		
		if ($images === false) {
			$output .= '<tr><td colspan="6">Not scanned yet.</td></tr>';
		} else if (!empty($images)) {
			foreach ($images as $num => $image) {
				$num++;
				if ($num <= $offset || $num > ($offset + self::IMAGES_PER_PAGE)) {
					continue;
				} else if ($offset == 0 && $num > self::IMAGES_PER_PAGE) {
					continue;
				} else {
					$class = !$image['flag'] ? "not_optimized" : "";
					$status = $image['flag'] ? "<div class='ui green message'><i class='large check icon'></i>Optimized</div>" : "<div class='ui yellow message'><i class='large exclamation icon'></i>Not optimized</div>";
					$compressInfo = '';
					if (isset($image['old_size'])) {
						$diff = (int) $image['old_size'] - filesize($image['file_path']);
						if ($diff > 0) {
							$color = 'green';
							$sym = '-';
						} else {
							$color = 'red';
							$sym = '+';
						}
						$diff = (abs($diff));
						$percent = number_format(($diff  * 100) / ((int) $image['old_size']), 2);
						$compressInfo = "<br><span style='font-size:10px;color:$color;'> $sym$diff Bytes ($sym$percent %)<span>";
					}
					$output .= '
					
					<tr>
						<td class="sg_center" style="width:1%;">
							<div class="ui checkbox '. $class .'">
								<input type="checkbox" class="urls" name="urls[]" value="'. $image['file_url'] .'">
								<label></label>
							</div>
						</td>
						<td class="sg_center" style="width:1%;"><a target="_blank" href="'.$image['file_url'].'"><img width="100" height="100" src="'.$image['thumb'].'"></a></td>
						<td style="width:16%;">'.$image['file_short'].'</td>
						<td style="width:28%;"><a target="_blank" href="'.$image['file_url'].'">'.$image['file_url'].'</a></td>
						<td class="sg_center" style="width:10%;">'.filesize($image['file_path']).' Bytes' . $compressInfo . '</td>
						<td class="sg_center" style="width:12%;">'.$status.'</td>
					</tr>
					
					';
				}
				
				if (isset($limits['limit_images_optimizer']) && $limits['limit_images_optimizer'] && ($num === $limits['limit_images_optimizer'])) {
					$output .= '<tr><td colspan="6"><div class="ui yellow message"><i class="exclamation triangle red icon"></i>Limit is exceeded, please purchase the plugin to view the full list. <a target="_blank" href="' . RT_SEO_Helper::$LINKS['buy_plugin'] . '">Link to purchase</a></div></td></tr>';
					return $output;
				}
			}
		} else {
			$output .= '<tr><td colspan="6">Images not found.</td></tr>';
		}
		return $output;
	}

	function getImages() {	
		$params = RT_SEO_Helper::Get_SQL_Params(array('images_optimizer'));
		$imagesData = isset($data['images_optimizer']) ? $data['images_optimizer'] : '';

		if (!$imagesData) return false;
		return unserialize(base64_decode($imagesData));
	}


	function setImages($data) {	
		$images['images_optimizer'] = base64_encode(serialize($data));
		RT_SEO_Helper::Set_SQL_Params($images);
	}

	public function getImageRealPath($attachment_id, $size = 'full') {
		$file = get_attached_file($attachment_id, true);
		if (empty($size) || $size === 'full') {
			return realpath($file);
		}
		if (! wp_attachment_is_image($attachment_id) ) {
			return false;
		}
		$info = image_get_intermediate_size($attachment_id, $size);
		if (!is_array($info) || ! isset($info['file'])) {
			return false;
		}

		return realpath(str_replace(wp_basename($file), $info['file'], $file));
	}
	
	function scanForImages() {	
		$listFromDb = $this->getImages();
		$list = array();
		$i = 0;
		$args = array(
			'post_type' => 'attachment',
			'post_mime_type' => 'image/jpeg,image/jpg,image/png',
			'post_status' => 'inherit',
			'posts_per_page' => -1,
			'orderby' => 'id',
			'order' => 'ASC'
		);
		$imagesUrls = array();
		$sizes = get_intermediate_image_sizes();
		$query_images = new WP_Query( $args );
		if ( $query_images->have_posts() ){
			while ($query_images->have_posts()){
				$query_images->the_post();
				foreach ( $sizes as $size ) {
					$imgSrcArr = wp_get_attachment_image_src( get_the_ID(), $size);
					$imgThumbArr = wp_get_attachment_image_src( get_the_ID(), array(100,100));
					if (in_array($imgSrcArr[0], $imagesUrls)) continue;
					$list[$i]['file_url'] = $imgSrcArr[0];
					$list[$i]['thumb'] = $imgThumbArr[0];

					$list[$i]['file_path'] = $this->getImageRealPath(get_the_ID(), $size) ? $this->getImageRealPath(get_the_ID(), $size) :  get_attached_file(get_the_ID());
					$list[$i]['file_short'] = basename( $list[$i]['file_url'] );
					$list[$i]['flag'] = 0;
					if (is_array($listFromDb) && count($listFromDb)) {
						foreach($listFromDb as $images) {
							if ($list[$i]['file_url'] === $images['file_url']) {
								$list[$i]['flag'] = $images['flag'];
								if (isset($images['backup'])) $list[$i]['backup'] = $images['backup'];
								if (isset($images['old_size'])) $list[$i]['old_size'] = $images['old_size'];
							}
						}
					}
					$imagesUrls[] = $list[$i]['file_url'];
					$i++;
				}
			}
		}
		$this->setImages($list);
		return $list;
	}
	
	public function saveImage($data, $url) {
		$images = $this->getImages();

		if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
			$data = substr($data, strpos($data, ',') + 1);
			$type = strtolower($type[1]); // jpg, png, gif

			if (!in_array($type, array('jpg', 'jpeg', 'gif', 'png'))) {
				return false;
			}

			$data = base64_decode($data);

			if ($data === false) {
				return false;
			}
		} else {
			return false;
		}

		foreach ($images as $key => $image) {
			if ($image['flag'] === 1) continue;
			if ($image['file_url'] === $url) {	
				if ($image['flag'] === 1) return false;
				$path = $image['file_path'];
				$images[$key]['flag'] = 1;
				$images[$key]['backup'] = $path . '_backup' . date("Ymd");
				$images[$key]['old_size'] = filesize($path);
			}
		}
		rename($path, $path . '_backup' . date("Ymd"));
		$this->setImages($images);
		if (file_put_contents($path, $data)) return true;
	}  
	
	public function restoreImage($url) {
		$images = $this->getImages(); 
		
		foreach ($images as $key => $image) {
			if ($image['flag'] === 0) continue;
			if ($image['file_url'] === $url) {
				unlink($images[$key]['file_path']);
				rename($image['backup'], $images[$key]['file_path']);
				$images[$key]['flag'] = 0;
				unset($images[$key]['backup']);
				unset($images[$key]['old_size']);
				$result = true;
			}
		}
		$this->setImages($images);
		
		if ($result) return true;
		return false;

	}


    public function ajaxOptimize() {

		global $rtsp_options;
		
		$apiKey = ($rtsp_options['rtseo_website_api_key']) ? $rtsp_options['rtseo_website_api_key'] : '';
		
		$apiUrl = "http://api.seoguarding.com/images_optimizer_api.php";//"http://artem.safetybis.com/test2.php";
		
		$images = $this->getImages();
		$urls = $_POST['urls'];

		if ($urls) {
			foreach ($urls as $key => $url) {
				foreach ($images as $image) {
					
					if ($image['file_url'] === $url && $image['flag'] === 1) {
						
						unset($urls[$key]);
					}
				}
			}
		}

		if (empty($urls)) {
			$msg_data = array(
				'type' => 'error',
				'size' => 'small',
				'content' => '0 image(s) optimized.'
			);
			$this->Print_MessageBox($msg_data);
			wp_die();
		}
		
		if (empty($apiKey)) {
			$msg_data = array(
				'type' => 'error',
				'size' => 'small',
				'content' => 'Empty API key'
			);
			$this->Print_MessageBox($msg_data);
			wp_die();
		}
		
		//$domain = RT_SEO_Helper::PrepareDomain(get_site_url());
		
		$params = array(
						'urls' => base64_encode(serialize($urls)),
						'domain' => RT_SEO_Helper::PrepareDomain(get_site_url()),
						'api_key' => $apiKey,
						);
		$response = $this->http_request("POST", $apiUrl, $params);
		//var_dump($response);
		$i = 0;
		if ($response['code'] === 200) {
			$data = json_decode($response['body'], true);
				

				
			if (is_array($data) && count($data)) {

				if (isset($data['error'])) {
					
					$msg_data = array(
						'type' => 'error',
						'size' => 'small',
						'content' => $data['error']
					);
				} else {
			
					foreach ($data as $image) {

						if ($this->saveImage($image['base64'], $image['url'])) $i++;
					}
					$msg_data = array(
						'type' => ($i) ? 'ok' : 'error',
						'size' => 'small',
						'content' => $i . ' image(s) optimized.'
					);
				}
			} else {
				$msg_data = array(
					'type' => ($i) ? 'ok' : 'error',
					'size' => 'small',
					'content' => 'Invalid JSON'
				);
			} 
		} else {
			$msg_data = array(
				'type' => ($i) ? 'ok' : 'error',
				'size' => 'small',
				'content' => 'Bad response ( ' . $response['code'] . ' )'
			);
		}
		$this->Print_MessageBox($msg_data);

        wp_die();
    }
	
	public function ajaxScan() {

		$list = $this->scanForImages();
		$i = count($list);
		$msg_data = array( 
			'type' => ($i) ? 'ok' : 'error',
			'size' => 'small',
			'content' => $i . ' image(s) found.'
		);
		
		$this->scanForImages();
		$this->Print_MessageBox($msg_data);

        wp_die();

	}	
	
	public function ajaxRestore() {
		$i = 0;
		if (!empty($_POST['urls'])) {
			$urls = $_POST['urls'];

			foreach ($urls as $url) {
				if ($this->restoreImage($url)) $i++;
			}
		}
		$msg_data = array(
			'type' => ($i) ? 'ok' : 'error',
			'size' => 'small',
			'content' => $i . ' image(s) restored.'
		);

		$this->Print_MessageBox($msg_data);

        wp_die();

	}

}

$RT_SEO_Images = new RT_SEO_Images();

?>