<?php

class RT_SEO_Dashboard extends RT_SEO_Helper
{
	
    var $minimum_title_length = 10;
    var $maximum_title_length = 80;
    var $minimum_description_length = 20;
    var $maximum_description_length = 200;
    var $maximum_url_length = 80;
	
  public function dashboard_panel()
  {
	global $rtsp_options, $RT_SEO_Serps;
	$key = ($rtsp_options['rtseo_website_api_key']) ? $rtsp_options['rtseo_website_api_key'] : false;
	$this->checkCore($key);
	$this->checkLicenceInfo($key);

	?>
    <div id="main" class="ui main" style="margin-top: 20px; width:99%">
            <h2 class="ui dividing header">Realtime SEO dashboard
            <!--    <a href="javascript:;" style="float:right" class="fnt-size-70" onClick="document.location.href = '<?php //echo admin_url() . '?page=rtseop_install&rtseop_install_nonce=' . wp_create_nonce( 'seop-install-nonce' ); ?>';"><i class="magic icon"></i>Run Configuration Wizard</a> -->
            </h2>




       
        <div class="ui grid">

            <div class="ten wide column">
            
                <style>
                .hcolumn:hover{background-color: #eee;}
                </style>      
                <div class="ui three column grid">
                
                <?php 
                $list = array(
                    0 => array('icon' => 'magic red', 'text' => 'Cofiguration Wizard', 'link_target' => '', 'link' => admin_url() . '?page=rtseop_install&rtseop_install_nonce=' . wp_create_nonce( 'seop-install-nonce' ), 'label' => ''),
                    1 => array('icon' => 'cogs green', 'text' => 'General Settings', 'link_target' => '', 'link' => admin_url() . 'admin.php?page=rt_seo_pack', 'label' => ''),
                    2 => array('icon' => 'umbrella black', 'text' => 'Security Setup', 'link_target' => '', 'link' => admin_url() . 'admin.php?page=rt_seo_security', 'label' => ''),
                    3 => array('icon' => 'globe blue', 'text' => 'SSL (https) Management', 'link_target' => '', 'link' => admin_url() . 'admin.php?page=rt_seo_ssl', 'label' => ''),
                    4 => array('icon' => 'random orange', 'text' => 'Redirect Management', 'link_target' => '', 'link' => admin_url() . 'admin.php?page=rt_seo_redirect', 'label' => ''),
                    5 => array('icon' => 'object group outline orange', 'text' => 'Image Optimization', 'link_target' => '', 'link' => admin_url() . 'admin.php?page=rt_seo_images', 'label' => ''),
                    6 => array('icon' => 'envelope outline yellow', 'text' => 'Contact Us', 'link_target' => 'target="_blank"', 'link' => 'https://seoguarding.com/contact-us/', 'label' => ''),
                    7 => array('icon' => 'bug red', 'text' => 'Report a Bug', 'link_target' => 'target="_blank"', 'link' => 'https://seoguarding.com/contact-us/', 'label' => ''),
                    8 => array('icon' => 'medkit green', 'text' => 'Website & SEO Protection', 'link_target' => 'target="_blank"', 'link' => 'https://seoguarding.com/services/website-security-protection/', 'label' => ''),
                    9 => array('icon' => 'heartbeat red', 'text' => 'Google Core Web Vitals', 'link_target' => '', 'link' => admin_url() . 'admin.php?page=rt_seo_vitals', 'label' => ''),
                );
                
                for ($i = 0; $i < count($list); $i++) { ?>
                  <div class="column">
                    <div class="ui raised segment center aligned hcolumn">
                      <?php
                      if ($list[$i]['label'] != '') echo $list[$i]['label'];
                      ?>
                      <a <?php echo $list[$i]['link_target']; ?> href="<?php echo $list[$i]['link']; ?>" style="font-size: 120%;"><i class="<?php echo $list[$i]['icon']; ?> huge icon"></i><br><br><?php echo $list[$i]['text']; ?></a>
                    </div>
                  </div>
                <?php } ?>
                
                </div>
            
    				<?php 
				/*        
                <div class="ui segment">

                <h3 class="ui dividing header"><i class="globe icon"></i>Important SEO News and Updates</h3>
                
                    <div class="ui relaxed divided items">
                        
                        <?php
						$response = $this->http_get(plugins_url( '..'. DIRSEP . 'tmp' . DIRSEP . 'realtime_seo.json', __FILE__ ));
						$data = json_decode($response['body']);
						
						if ($data) {
							foreach ($data->news as $item) {
                        ?>
                        
                        <div class="item">
                          <div class="ui small image">
                            <img src="<?php if (!$item->img) { echo plugins_url( '../images/no_image.jpg', __FILE__ ); } else { echo $item->img; }?>">
                          </div>
                          <div class="content">
                            <a class="header"><?=$item->title;?></a>
                            <div class="description">
                              <?=$item->text;?>
                            </div>
                            <div class="extra">
                              <div class="ui right floated mini secondary button">
                                <a style="color:white;" target="_blank" href="<?=$item->url;?>">View</a>
                                <i class="right chevron icon"></i>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php
							}
                        }
                        ?>
                        
                      </div>
                   
                </div>
				*/   ?>
            </div>
            
            <div class="six wide column floated right">

                <?php



                $this->admin_analytics();


                ?>
                <?php /*
                <div id="dashboard-widgets" class="metabox-holder columns-1">
                  <div id='postbox-container-1' class='postbox-container'>
                    <?php
                    do_meta_boxes( 'rt_seo_analytics', 'normal', false );
                    ?>
                  </div>
                </div>
                */ ?>


            </div>
            
        </div>
<?php
/*
        <div class="ui grid">
            <div class="sixteen wide column">
                <h2 class="ui dividing header">Google Activity Monitor</h2>
            </div>
        </div>
        
        <div class="ui grid">
                <?php $RT_SEO_Serps->getSerpsBlock(); ?>
        </div>
		*/
?>


    </div>
	<?php
  }

 	public function getAnalytics() {
		global $wpdb;
		$analytics = RT_SEO_Helper::Get_SQL_Params(array('data', 'last_analyze'));
		$analytics['data'] = json_decode($analytics['data'], true);
		array_multisort(array_map('count', $analytics['data'], SORT_DESC, $analytics['data']));

		return $analytics ;

	}

	private function getLimitMessage() {
		echo "<div class='item'>. . .</div><hr><div>Limit is exceeded, please purchase the plugin to view the full list. <a target='_blank' href='" . RT_SEO_Helper::$LINKS['buy_plugin'] . "'>Link to purchase</a></div>";

	}

	public function admin_analytics() {

		if (isset($_POST['action']) && $_POST['action'] == 'rtseo_analytics_update' && isset($_POST['nonce-rtseop']) && $_POST['nonce-rtseop'] == esc_attr(wp_create_nonce('rtseopack'))) {

			$this->analyze();
		}

        echo '<div class="ui segment">';
        echo '<h3 class="ui dividing header"><i class="tasks icon"></i>Statistics</h3>';

		$analytics = $this->getAnalytics();
		
		$validator = new RT_SEO_Validation(true);
		$limits = false;//$validator->getLimits();
		
		if ($analytics) {
;
			echo "<p>Last analyze: " . date("F j, Y, g:i a", $analytics['last_analyze']) . "</p>";
			foreach ($analytics['data'] as $reason => $values) {
				switch($reason) {
					case "empty_titles":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages with empty titles
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('empty_titles');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages with empty titles.</div>
								<div id="empty_titles" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_empty_titles'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "title_more_than_max":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages exceed max title length ( > <?=$this->maximum_title_length;?>)
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('title_more_than_max');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages exceed max title length ( > <?=$this->maximum_title_length;?>)</div>
								<div id="title_more_than_max" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_title_more_than_max'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "title_less_than_min":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages with titles which have less than min length ( < <?=$this->minimum_title_length;?>)
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('title_less_than_min');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div> 
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages with titles which have less than min length ( < <?=$this->minimum_title_length;?>)</div>
								<div id="title_less_than_min" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_title_less_than_min'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "empty_description":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages with empty description.
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('empty_description');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages with empty description.</div>
								<div id="empty_description" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_empty_description'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "description_more_than_max":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages exceed max description length ( > <?=$this->maximum_description_length;?>)
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('description_more_than_max');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages exceed max description length ( > <?=$this->maximum_description_length;?>)</div>
								<div id="description_more_than_max" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_description_more_than_max'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "description_less_than_min":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages with description which have less than min length ( < <?=$this->minimum_description_length;?>)
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('description_less_than_min');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages with description which have less than min length ( < <?=$this->minimum_description_length;?>)</div>
								<div id="description_less_than_min" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_description_less_than_min'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "low_words_content":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages with too little content (less than 500 words).
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('low_words_content');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages with too little content (less than 500 words).</div>
								<div id="low_words_content" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_low_words_content'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "images_empty_or_no_alt":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages with empty or non-exist "ALT" attribute in your img tags.
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('images_empty_or_no_alt');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages with empty or non-exist "ALT" attribute in your img tags.</div>
								<div id="images_empty_or_no_alt" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_images_empty_or_no_alt'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "too_long_url":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages with too long URL address length.
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('too_long_url');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages with too long URL address length.</div>
								<div id="too_long_url" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_too_long_url'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "favicon":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							Favicon is installed.
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="<?php echo get_site_url() . '/wp-admin/customize.php'?>"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i></a>
							Favicon for your website isn't installed.</div>

							</div>

							<?php
						}
						break;
					case "no_h1_tag":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages with empty of non-exist "H1" tags
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('no_h1_tag');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages with empty of non-exist "H1" tags</div>
								<div id="no_h1_tag" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_no_h1_tag'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "h1_tag_more_than_one":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages have more than one "H1" tag
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('h1_tag_more_than_one');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages have more than one "H1" tag</div>
								<div id="h1_tag_more_than_one" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_h1_tag_more_than_one'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "same_title_and_h1":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Pages have the same title and "H1" tag
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('same_title_and_h1');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Pages have the same title and "H1" tag</div>
								<div id="same_title_and_h1" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											foreach ($values as $id => $value){
												echo "<div class='item'>ID: $id. Link: <a target='_blank' href='$value'>$value</a></div>";
												if ($limits && ($i === $limits['limit_same_title_and_h1'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;
					case "duplicated_titles":
						if (empty($values)) {
							?>
							<div class="ui green message"><i class="check icon green"></i>
							0 Duplicated titles
							</div>
							<?php
						} else {
							?>
							<div class="ui red message">
                            <div  class="ui right floated header tiny"><a href="javascript:;" onclick="toggleVisibilityInstall('duplicated_titles');"><i class="info circle icon"></i></i><div class="content">Details</div></a></div>
                            <div class="width80"><i class="exclamation triangle icon red"></i>
							<?php echo count($values) ?> Duplicated titles</div>
								<div id="duplicated_titles" style="display:none"><hr>
									<div class="ui bulleted list">
										<?php
											$i = 1;
											foreach ($values as $title => $links){
												echo "<div class='item'>Title: $title.<br> Links:<br>";
												foreach ($links as $link) {
													echo "<a target='_blank' href='$link'>$link</a><br>";
												}
												echo "</div>";
												if ($limits && ($i === $limits['limit_duplicated_titles'])) {
													$this->getLimitMessage();
													break;
												}
												$i++;
											}
										?>
									</div>
								</div>
							</div>

							<?php
						}
						break;


				}
			}

		} else {
			echo 'Not analyzed yet.';
		}
        
        ?>
            <form name="dofollow" action="" method="post">

            <p class="sg_center">
                <input type="hidden" name="action" value="rtseo_analytics_update" />
                <input type="hidden" name="nonce-rtseop" value="<?php echo esc_attr(wp_create_nonce('rtseopack')); ?>" />
                <input type="submit" class="medium ui secondary button" name="Submit" value="<?php _e('Analyze', 'rt_seo')?>" />
            </p>

        <?php
        echo '</div>';
  }

	public function analyze() {
		$results = array(
			"empty_titles" => array(),
			"title_more_than_max" => array(),
			"title_less_than_min" => array(),
			"empty_description" => array(),
			"description_more_than_max" => array(),
			"description_less_than_min" => array(),
			"low_words_content" => array(),
			"images_empty_or_no_alt" => array(),
			"too_long_url" => array(),
			"favicon" => array(),
			"no_h1_tag" => array(),
			"h1_tag_more_than_one" => array(),
			"same_title_and_h1" => array(),
			"duplicated_titles" => array(),
			);

		global $wpdb;
		$table_name = $wpdb->prefix . 'rtseo_analytics';
		$wpdb->query("DELETE FROM $table_name WHERE var_name='last_analyze' or var_name='data'");

		$postsData = $wpdb->get_results("SELECT id, post_title AS title, post_content AS content FROM $wpdb->posts WHERE post_type = 'post' or post_type = 'page'", ARRAY_A);

		if (!get_option( 'site_icon', false )) $results['favicon'][]= 0;


		foreach ($postsData as $post) {

			$link = get_permalink($post['id']);

			$title = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($post['id'], 'rtseo_title', true))));
			$description = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($post['id'], 'rtseo_description', true))));

			if( !$title ) {
				$title = get_the_title( $post['id'] );
			}




			if (strlen($link) >= $this->maximum_url_length) {
				$results['too_long_url'][$post['id']] = $link;
			}

			if ($title == '') {
				$results['empty_titles'][$post['id']] = $link;
			}

			if (strlen($title) <= $this->minimum_title_length && $title != '') {
				$results['title_less_than_min'][$post['id']] = $link;
			}

			if (strlen($title) >= $this->maximum_title_length) {
				$results['title_more_than_max'][$post['id']] = $link;
			}

			if ($description == '') {
				$results['empty_description'][$post['id']] = $link;
			}

			if (strlen($description) <= $this->minimum_description_length && $description != '') {
				$results['description_less_than_min'][$post['id']] = $link;
			}

			if (strlen($description) >= $this->maximum_description_length) {
				$results['description_more_than_max'][$post['id']] = $link;
			}


			if (str_word_count(strip_tags($post['content'])) < 500) {
				$results['low_words_content'][$post['id']] = $link;
			}

			$html = str_get_html($post['content']);

			if ($html) {

				$images = $html->find('img');
				foreach ($images as $image) {
					if(!isset($image->alt) || $image->alt = '') {
						$results['images_empty_or_no_alt'][$post['id']] = $link;
					}
				}

				$h1Tags = $html->find('h1');

				if (empty($h1Tags)) {
					$results['no_h1_tag'][$post['id']] = $link;
				} elseif (count($h1Tags) > 1) {
					$results['h1_tag_more_than_one'][$post['id']] = $link;
				}

				if (count($h1Tags) == 1 && $h1Tags[0]->plaintext == $title) {
					$results['same_title_and_h1'][$post['id']] = $link;
				}

			}


		}

		$duplicateTitles = $wpdb->get_results("SELECT id,
												   a.post_title,
												   post_content
												FROM {$wpdb->posts} a
												   INNER JOIN (SELECT post_title
															   FROM   {$wpdb->posts}
															   WHERE  post_type = 'post' or post_type = 'page'
															   GROUP  BY post_title
															   HAVING COUNT(id) > 1) dup
														   ON a.post_title = dup.post_title ORDER BY post_title",
												ARRAY_A);
		foreach ($duplicateTitles as $title) {
			$results['duplicated_titles'][$title['post_title']][] = get_permalink($title['id']);
		}




	  $wpdb->insert( $table_name, array('var_name' => 'last_analyze', 'var_value' => current_time('timestamp') ) );
	  $wpdb->insert( $table_name, array('var_name' => 'data', 'var_value' => json_encode($results) ) );

	  return true;
	}

}

$RT_SEO_Dashboard = new RT_SEO_Dashboard();
?>