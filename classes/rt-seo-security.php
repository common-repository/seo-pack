<?php

class RT_SEO_Security extends RT_SEO_Helper
{
	

  function security_panel()
  {

	?>
    <style>
    #icblocks .content{font-size:85%!important;}
    #icblocks .sub.header{font-size:69%!important;}
    </style>
    <div id="main" class="ui main" style="margin-top: 20px; width:99%">
            <h2 class="ui dividing header">Security</h2>
            
            <div class="ui grid" id="icblocks">
            
              <div class="four wide column">
                <div class="ui segment">
                    <h2 class="ui icon header">
                      <i class="eye orange icon"></i>
                      <div class="content">
                        Blacklist Monitor
                        <div class="sub header">The Blacklist Monitoring feature will help you with Google Black List Monitoring, McAfee, Norton, BitDefender, PhishTank, WebSecurityGuard, and others.</div>
                        <a href="javascript:;" onclick="OpenSettings(1)" class="ui small secondary button mrt-top-15">Manage</a>
                      </div>
                    </h2>
                </div>
              </div>
              
              <div class="four wide column">
                <div class="ui segment">
                    <h2 class="ui icon header">
                      <i class="globe blue icon"></i>
                      <div class="content">
                        Geo IP Protection
                        <div class="sub header fnt-size-60">The GEOIP Block function not only blocks unwanted traffic but also significantly reduces the load on the server thereby making your site faster and more optimized.</div>
                        <a href="javascript:;" onclick="OpenSettings(2)" class="ui small secondary button mrt-top-15">Manage</a>
                      </div>
                    </h2>
                </div>
              </div>
              
              <div class="four wide column">
                <div class="ui segment">
                    <h2 class="ui icon header">
                      <i class="umbrella red icon"></i>
                      <div class="content">
                        Bad Links Protection 
                        <div class="sub header fnt-size-60">Third-party links that can be listed in your template or third-party plugins  can significantly lower your site in the search results of search engines.</div>
                        <a href="javascript:;" onclick="OpenSettings(3)" class="ui small secondary button mrt-top-15">Manage</a>
                      </div>
                    </h2>
                </div>
              </div>
              
              <div class="four wide column">
                <div class="ui segment">
                    <h2 class="ui icon header">
                      <i class="user secret green icon"></i>
                      <div class="content">
                        Two-Factor Authentication
                        <div class="sub header fnt-size-60">Secure your WordPress login with an additional layer of security from us. This plugin provides two factor authentication during administrator page login.</div>
                        <a href="javascript:;" onclick="OpenSettings(4)" class="ui small secondary button mrt-top-15">Manage</a>
                      </div>
                    </h2>
                </div>
              </div>
              
            </div>
            
            <script>
            function OpenSettings(id)
            {
                jQuery(".settblocks").hide(300);
                jQuery("#block"+id).show(300);
            }
            </script>
			<?php
			
				  if (isset($_GET['view'])) {
					  switch ($_GET['view']) {
						case 'blacklists' :
						?>
							<script>
								jQuery( document ).ready(function() {
									OpenSettings(1);
								});
							</script>
						<?php
							break;
							
						case 'geo' :
						?>
							<script>
								jQuery( document ).ready(function() {
									OpenSettings(2);
								});
							</script>
						<?php
							break;
							
						case 'seo' :
						?>
							<script>
								jQuery( document ).ready(function() {
									OpenSettings(3);
								});
							</script>
						<?php
							break;
							
						case 'tfa' :
						?>
							<script>
								jQuery( document ).ready(function() {
									OpenSettings(4);
								});
							</script>
						<?php
							break;
							
							
					  }
				  }
			
			?>
			<script>
			function ShowLoader()
			{
				jQuery(".ajax_button").hide();
				jQuery(".scanner_ajax_loader").show(); 
				
				jQuery.post(
					ajaxurl, 
					{
						'action': 'rt_seo_ajax_scan_blacklist'
					}, 
					function(response){
						document.location.href = 'admin.php?page=rt_seo_security&view=blacklists';
					}
				);  
			}
			</script>
			<script>
			function ShowLoaderSEO()
			{
				jQuery(".ajax_button").hide();
				jQuery(".scanner_ajax_loader").show(); 
				
				jQuery.post(
					ajaxurl, 
					{
						'action': 'rt_seo_ajax_scan_seo'
					}, 
					function(response){
						document.location.href = 'admin.php?page=rt_seo_security&view=seo';
					}
				);  
			}
			</script>

            <div class="ui grid">
            
              <div class="sixteen wide column settblocks" id="block1" style="display: none;">
                <div class="ui segment">
                    <h3 class="ui dividing header">Blacklist Monitor</h3>
            <?php
			
				$params = RT_SEO_Helper::Get_SQL_Params(array('latest_scan_date_blacklists', 'latest_results'));
				
				if (!isset($params['latest_scan_date_blacklists'])) $params['latest_scan_date_blacklists'] = '';
				if (!isset($params['latest_results'])) $params['latest_results'] = '';
				
				$domain = $this->PrepareDomain(get_site_url());
				
				

				$list = RTSEO_PLGSGWBM::$blacklists;
				foreach ($list as $k => $row)
				{
					$row['status'] = 'OK';
					$list[$k] = $row;
				}
				
				$latest_results = (array)@json_decode($params['latest_results'], true);
				if (count($latest_results))
				{
					foreach ($latest_results as $row)
					{
						$list[$row]['status'] = 'BL';
					}
				}


				if (isset($params['latest_scan_date_blacklists']) && trim($params['latest_scan_date_blacklists']) == '') $flag_status_unknown = true;
				else $flag_status_unknown = false;
				
				if (!$flag_status_unknown)
				{
					// Prepare BL and OK lists
					$tmp_arr = array('BL' => array(), 'OK' => array());
					foreach ($list as $k => $row)
					{
						if ($row['status'] == "OK")
						{
							$tmp_arr['OK'][$k] = $row;
						}
						else {
							$tmp_arr['BL'][$k] = $row;
						}
					}
				}
			
			?>        
           
            <div class="ui grid">
                <div class="six wide column">
                
                <?php 
                    if ($flag_status_unknown)
                    {

                    }
                    else {
                        $data = array(
                            array(
                                'txt' => 'Clean ('.count($tmp_arr['OK']).')',
                                'val' => count($tmp_arr['OK']),
                            ),
                            array(
                                'txt' => 'Blacklisted ('.count($tmp_arr['BL']).')',
                                'val' => count($tmp_arr['BL']),
                            ),
                        );
                    }
                ?>

                
                </div>
                

            
            </div>
            
            
            <?php
                $data = array(
                    array(
                        'active' => 'active',
                        'icon' => 'check white',
                        'title' => 'Blacklist Scanner',
                        'description' => 'you can check your website in 30+ blacklists',
                    ),
                    array(
                        'active' => 'active',
                        'icon' => 'check white',
                        'title' => 'Blacklist Removal & Monitoring',
                        'description' => 'if any issue detected, we will fix your website',
                    ),
                );
                
            if ($flag_status_unknown)
            {
                $msg_data = array(
                    'type' => 'warning',
                    'content' => 'You don\'t have any results yet. Please use the button <b>Recheck</b> to get the results.'
                );
                $this->Print_MessageBox($msg_data);
            }

            ?>
            

            

            <div class="ui grid">
                <div class="ten wide column">
                    <?php
                    if (!$flag_status_unknown)
                    {
                        // Show blocked
                        foreach ($tmp_arr['BL'] as $k => $row)
                        {
                            $msg_data = array(
                                'type' => 'error',
                                'content' => 'Your domain ('.$domain.') is blacklisted in <img src="'.$row['logo'].'"> <b>'.$k.'</b>'
                            );
                            $this->Print_MessageBox($msg_data);
                        }
                        
                        // Show OK
                        foreach ($tmp_arr['OK'] as $k => $row)
                        {
                            $msg_data = array(
                                'type' => 'ok',
                                'content' => 'Not blacklisted in <img src="'.$row['logo'].'"> <b>'.$k.'</b>'
                            );
                            $this->Print_MessageBox($msg_data);
                        }
                        
                    }
                    
                    ?>
                </div>
                
                <div class="six wide column">
                    <div class="ui raised segment">
                        <h3 class="ui dividing header">Blacklist Status</h3>
                        <div class="content"><b>Latest check:</b> <?php echo $params['latest_scan_date_blacklists']; ?></div>
                        <div class="content"><b>Blacklisted:</b> <?php if (isset($tmp_arr['BL']) && is_array($tmp_arr['BL'])) echo count($tmp_arr['BL']); ?></div>
                        <div class="content"><b>Clean:</b> <?php if (isset($tmp_arr['OK']) && is_array($tmp_arr['OK'])) echo count($tmp_arr['OK']); ?></div>
                        
                        <form method="post" action="admin.php?page=rt_seo_security" novalidate="novalidate">
                        
                        <p class="sg_center">
                            <img class="scanner_ajax_loader" width="48" height="48" style="display: none;" src="<?php echo plugins_url('images/ajax_loader.svg', dirname(__FILE__)); ?>" />
                            <a class="ajax_button ui medium secondary button mrt-top-15" href="javascript:;" onclick="ShowLoader();">Recheck</a>
                        </p>
                        
                        <?php
                        wp_nonce_field( 'name_49FD96F7C7F5' );
                        ?>
                        <input type="hidden" name="action" value="rescan"/>
                        </form>
                    </div>
                </div>
                
            </div>
            

                
      

            <div class="ui grid">
                <div class="sixteen wide column">
                    <p class="sg_center">
                        <img class="scanner_ajax_loader" width="96" height="96" style="display: none;" src="<?php echo plugins_url('images/ajax_loader.svg', dirname(__FILE__)); ?>" />
                        <a class="ajax_button ui massive secondary button mrt-top-15" href="javascript:;" onclick="ShowLoader();">Recheck</a>

                    </p>
                    <p class="sg_center c_red"><b>Scan process will take approximately 30 seconds</b></p>
                </div>
            </div>
            
                    
                                        
                </div>
              </div>



              <div class="sixteen wide column settblocks" id="block2" style="display: none;">
                <div class="ui segment">
                    <h3 class="ui dividing header">GEO Website Protection</h3>
 <?php                
		if (isset($_GET['view']) && $_GET['view'] == 'geo') $this->check_action();
        $params = RT_SEO_Helper::Get_SQL_Params();
		if (!isset($params['frontend_ip_list'])) $params['frontend_ip_list'] = '';
		if (!isset($params['frontend_ip_list_allow'])) $params['frontend_ip_list_allow'] = '';
        $params['frontend_country_list'] = isset($params['frontend_country_list']) ? json_decode($params['frontend_country_list'], true) : '';
        //print_r($params);

        $myIP = $_SERVER['REMOTE_ADDR'];
		if (!class_exists('RT_SEO_Geo_IP2Country')) {
			include_once 'geo.php';
		}
			if (filter_var($myIP, FILTER_VALIDATE_IP)) {
				$geo = new RT_SEO_Geo_IP2Country();
				$myCountryCode = $geo->getCountryByIP($myIP);
				$myCountry = $geo->getNameByCountryCode($myCountryCode) ? $geo->getNameByCountryCode($myCountryCode) : '';
			}


           ?>
    <script>
    function InfoBlock(id)
    {
        jQuery("#"+id).toggle();
    }
    function SelectCountries(select, uncheck)
    {
        if (select != '') jQuery(select).prop( "checked", true );
        
        if (uncheck != '') jQuery(uncheck).prop( "checked", false );
    }
    </script>
    
    
    <div class="ui grid max-box">
    <div class="row">


    <div class="ui bottom attached segment">
    <?php
        ?>
        <h4 class="ui header">Front-end protection</h4>
        
        <form method="post" action="admin.php?page=rt_seo_security&view=geo">
        
        <p>
        <?php
        if (isset($params['protection_frontend']) && intval($params['protection_frontend']) == 1) { $block_class = ''; $protection_txt = '<span class="ui green horizontal label">Enabled</span>'; $protection_bttn_txt = 'Disable Protection'; }
        else { $block_class = 'class="hide"'; $protection_txt = '<span class="ui horizontal red label">Disabled</span>'; $protection_bttn_txt = 'Enable Protection'; }
        ?>
        GEO Protection for front-end is <?php echo $protection_txt; ?> Visitors from selected countried and selected IP addresses will not be able to visit your website.
        </p>
        <input type="submit" name="submit" id="submit" class="ui mini secondary button mrt-top-15" value="<?php echo $protection_bttn_txt; ?>">

        <p>&nbsp;</p>
        
		<?php
		wp_nonce_field( 'name_2Jjf73gds8d' );
		?>
		<input type="hidden" name="action" value="EnableDisable_frontend_protection"/>
		</form>
        
        <form method="post" action="admin.php?page=rt_seo_security&view=geo">
        <div <?php echo $block_class; ?>>
        
            <h4 class="ui header">Block (blacklist) by IP or range (your IP is <?php echo $myIP; ?>)</h4>
            
            <div class="ui ignored message">
                  <i class="help circle icon"></i>e.g. 200.150.160.1 or 200.150.160.* or or 200.150.*.*
            </div>
            
            <div class="ui input" style="width: 100%;margin-bottom:10px">
                <textarea name="frontend_ip_list" style="width: 100%;height:200px" placeholder="Insert IP addresses or range you want to block, one by line"><?php echo $params['frontend_ip_list']; ?></textarea>
            </div>
            <input type="submit" name="submit" id="submit" class="ui secondary button mrt-top-15" value="Save & Apply">
            
            <h4 class="ui header">Block by country (your country is <?php echo $myCountry; ?>)</h4>
            
            <div class="ui ignored message">
                  <i class="help circle icon"></i>Quick buttons: <a class="mini ui button bttn_bottom" href="javascript:SelectCountries('.all', '.country_<?php echo $myCountryCode; ?>');">Select All (exclude <?php echo $myCountryCode; ?>)</a> <a class="mini ui button bttn_bottom" href="javascript:SelectCountries('', '.all');">Uncheck All</a> <a class="mini ui button bttn_bottom" href="javascript:SelectCountries('.all', '.country_US,.country_CA');">Select All (exclude USA, Canada)</a> <a class="mini ui button bttn_bottom" href="javascript:SelectCountries('.all', '.europe');">Select All (exclude EU countries)</a> <a class="mini ui button bttn_bottom" href="javascript:SelectCountries('.3rdcountry', '');">Select All 3rd party countries</a>
            </div>
            
            <?php echo self::CountryList_checkboxes($params['frontend_country_list']); ?>
            
            <p>&nbsp;</p>
            <input type="submit" name="submit" id="submit" class="ui secondary button mrt-top-15" value="Save & Apply">
            
            
            <h4 class="ui header">Allow (whitelist) by IP or range (your IP is <?php echo $myIP; ?>)</h4>
            
            <div class="ui ignored message">
                  <i class="help circle icon"></i>e.g. 200.150.160.1 or 200.150.160.* or or 200.150.*.*
            </div>
            
            <div class="ui input" style="width: 100%;margin-bottom:10px">
                <textarea name="frontend_ip_list_allow" style="width: 100%;height:200px" placeholder="Insert IP addresses or range you want to allow, one by line"><?php echo $params['frontend_ip_list_allow']; ?></textarea>
            </div>
            <input type="submit" name="submit" id="submit" class="ui secondary button mrt-top-15" value="Save & Apply">
            
        </div>
        
		<?php
		wp_nonce_field( 'name_3dfUejeked' );
		?>
		<input type="hidden" name="action" value="Save_frontend_params"/>
		</form>
        <?php

    


    ?>
    
    </div>
           
        
    </div>
    </div>	
    


                                        
                </div>
              </div>
              
              
              <div class="sixteen wide column settblocks" id="block3" style="display: none;">
                <div class="ui segment">
                    <h3 class="ui dividing header">SEO Protection</h3>
<?php                    

        $params = RT_SEO_Helper::Get_SQL_Params();
		if (!isset($params['progress_status'])) $params['progress_status'] = 0;
        
           ?>
    
    <div class="ui grid max-box">
    <div class="row">
    
    <div class="ui bottom attached segment">
    <?php
		
        if (intval($params['progress_status']) == 0)
        {
            ?>
            <form method="post" action="admin.php?page=rt_seo_security&view=seo">
    

			<p class="scanner_ajax_loader sg_center" style="display: none;">
				<img width="60" height="60" src="<?php echo plugins_url('/images/ajax_loader.svg', dirname(__FILE__)); ?>" />
				                <br /><br />
                The scanner is in progress.<br>
                Please wait, it will take 30-60 seconds.
			</p>
			<p class="sg_center">
				<a class="ajax_button ui medium secondary button mrt-top-15" href="javascript:;" onclick="ShowLoaderSEO();">Start Scanner</a>
			</p>

    
    		<?php
    		wp_nonce_field( 'name_10EFDDE97A00' );

    		?>
    		</form>

            <hr><h4 class="ui header">Results</h4>
            <?php
            if (isset($params['latest_scan_date_seo']))
            {
                // Show report
                echo '<p>Latest scan was '.$params['latest_scan_date_seo'].'</p>';
                
                $params['results'] = isset($params['results']) ? (array)json_decode($params['results'], true) : '';
                
                if (!isset($_GET['showdetailed']) || intval($_GET['showdetailed']) == 0)
                {
                    /**
                     * Show simple
                     */
                    $results = RT_SEO_Protection::PrepareResults($params['results']);

                    echo '<h3>Bad words (<a href="admin.php?page=rt_seo_security&view=seo&showdetailed=1">show details</a>)</h3>';
                    if (count($results['WORDS']))
                    {
                        echo '<table class="ui selectable celled table small">';
                        echo '<thead><tr><th>Words</th></thead>';
                        foreach ($results['WORDS'] as $word)
                        {
                            echo '<tr>';
                            echo '<td>'.$word.'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    else echo '<p>No bad words detected.</p>';
                    
                    echo "<hr>";
                    
                    echo '<h3>Detected links (<a href="admin.php?page=rt_seo_security&view=seo&showdetailed=1">show details</a>)</h3>';
                    if (count($results['A']))
                    {
                        echo '<table class="ui selectable celled table small">';
                        echo '<thead><tr><th>Links</th><th>Anchor Text</th></tr></thead>';
                        foreach ($results['A'] as $link => $txt)
                        {
                            echo '<tr>';
                            echo '<td>'.$link.'</td><td>'.$txt.'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    else echo '<p>No strange links detected.</p>';
                    
                    echo "<hr>";
                    
                    echo '<h3>Detected iframes (<a href="admin.php?page=rt_seo_security&view=seo&showdetailed=1">show details</a>)</h3>';
                    if (count($results['IFRAME']))
                    {
                        echo '<table class="ui selectable celled table small">';
                        echo '<thead><tr><th>Links</th></thead>';
                        foreach ($results['IFRAME'] as $link)
                        {
                            echo '<tr>';
                            echo '<td>'.$link.'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    else echo '<p>No iframes detected.</p>';
                    
                    echo "<hr>";
                    
                    echo '<h3>Detected JavaScripts (<a href="admin.php?page=rt_seo_security&view=seo&showdetailed=1">show details</a>)</h3>';
                    if (count($results['SCRIPT']))
                    {
                        echo '<table class="ui selectable celled table small">';
                        echo '<thead><tr><th>JavaScripts Link or codes</th></thead>';
                        foreach ($results['SCRIPT'] as $link)
                        {
                            echo '<tr>';
                            echo '<td>'.$link.'</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    else echo '<p>No iframes detected.</p>';
                }
                else {
                    /**
                     * Show detailed
                     */
                    $post_ids = array();
                    $post_titles = array();
                    if (isset($params['results']['posts']['WORDS']) && count($params['results']['posts']['WORDS']))
                    {
                        foreach ($params['results']['posts']['WORDS'] as $post_id => $post_arr)
                        {
                            $post_ids[ $post_id ] = $post_id;
                        }
                    }
                    if (isset($params['results']['posts']['A']) && count($params['results']['posts']['A']))
                    {
                        foreach ($params['results']['posts']['A'] as $post_id => $post_arr)
                        {
                            $post_ids[ $post_id ] = $post_id;
                        }
                    }
                    if (isset($params['results']['posts']['IFRAME']) && count($params['results']['posts']['IFRAME']))
                    {
                        foreach ($params['results']['posts']['IFRAME'] as $post_id => $post_arr)
                        {
                            $post_ids[ $post_id ] = $post_id;
                        }
                    }
                    if (isset($params['results']['posts']['SCRIPT']) && count($params['results']['posts']['SCRIPT']))
                    {
                        foreach ($params['results']['posts']['SCRIPT'] as $post_id => $post_arr)
                        {
                            $post_ids[ $post_id ] = $post_id;
                        }
                    }
                    $post_titles = RT_SEO_Protection::GetPostTitles_by_IDs($post_ids);
                    
                    echo '<h3>Detailed by post (<a href="admin.php?page=rt_seo_security&view=seo&showdetailed=0">show simple</a>)</h3>'; 
                    if (isset($params['results']['posts']['WORDS']) && count($params['results']['posts']['WORDS']))
                    {
                        foreach ($params['results']['posts']['WORDS'] as $post_id => $post_arr)
                        {
                            if (count($post_arr))
                            {
                                $edit_link = 'post.php?post='.$post_id.'&action=edit';
                                echo '<table class="ui selectable celled table small">';
                                echo '<thead><tr><th><b>Bad words in post ID: '.$post_id.'</b> ('.$post_titles[$post_id]/*RT_SEO_Protection::GetPostTitle_by_ID($post_id)*/.') <a href="'.$edit_link.'" target="_blank" class="edit_post"><i class="edit icon"></i> edit</a></th></tr></thead>';
                                foreach ($post_arr as $word)
                                {
                                    echo '<tr>';
                                    echo '<td>'.$word.'</td>';
                                    echo '</tr>';
                                }
                                echo '</table>';
                            }
                        }
                    }
                    if (isset($params['results']['posts']['A']) && count($params['results']['posts']['A']))
                    {
                        foreach ($params['results']['posts']['A'] as $post_id => $post_arr)
                        {
                            if (count($post_arr))
                            {
                                $edit_link = 'post.php?post='.$post_id.'&action=edit';
                                echo '<table class="ui selectable celled table small">';
                                echo '<thead><tr><th class="ten wide"><b>Links in post ID: '.$post_id.'</b> ('.$post_titles[$post_id]/*RT_SEO_Protection::GetPostTitle_by_ID($post_id)*/.') <a href="'.$edit_link.'" target="_blank" class="edit_post"><i class="edit icon"></i> edit</a></th><th class="six wide">Anchor Text</th></tr></thead>';
                                foreach ($post_arr as $link_data)
                                {
                                    foreach ($link_data as $link => $txt)
                                    {
                                        echo '<tr>';
                                        echo '<td>'.$link.'</td><td>'.$txt.'</td>';
                                        echo '</tr>';
                                    }
                                }
                                echo '</table>';
                            }
                        }
                    }
                    //else echo '<p>No strange links detected.</p>';
//print_r($params['results']['posts']['IFRAME']);
                    if (isset($params['results']['posts']['IFRAME']) && count($params['results']['posts']['IFRAME']))
                    {
                        foreach ($params['results']['posts']['IFRAME'] as $post_id => $post_arr)
                        {
                            if (count($post_arr))
                            {
                                $edit_link = 'post.php?post='.$post_id.'&action=edit';
                                echo '<table class="ui selectable celled table small">';
                                echo '<thead><tr><th><b>Iframes in post ID: '.$post_id.'</b> ('.$post_titles[$post_id]/*RT_SEO_Protection::GetPostTitle_by_ID($post_id)*/.') <a href="'.$edit_link.'" target="_blank" class="edit_post"><i class="edit icon"></i> edit</a></th></tr></thead>';
                                foreach ($post_arr as $link)
                                {
                                    echo '<tr>';
                                    echo '<td>'.$link.'</td>';
                                    echo '</tr>';
                                }
                                echo '</table>';
                            }
                        }
                    }
                    //else echo '<p>No strange links detected.</p>';
//print_r($params['results']['posts']['SCRIPT']);exit;
                    if (isset($params['results']['posts']['SCRIPT']) && count($params['results']['posts']['SCRIPT']))
                    {
                        foreach ($params['results']['posts']['SCRIPT'] as $post_id => $post_arr)
                        {
                            if (count($post_arr))
                            {
                                $edit_link = 'post.php?post='.$post_id.'&action=edit';
                                echo '<table class="ui selectable celled table small">';
                                echo '<thead><tr><th><b>JavaScript in post ID: '.$post_id.'</b> ('.$post_titles[$post_id]/*RT_SEO_Protection::GetPostTitle_by_ID($post_id)*/.') <a href="'.$edit_link.'" target="_blank" class="edit_post"><i class="edit icon"></i> edit</a></th></tr></thead>';
                                foreach ($post_arr as $js_link => $js_code)
                                {
                                    if ($js_code == '') $js_code = $js_link;
                                    echo '<tr>';
                                    echo '<td>'.$js_code.'</td>';
                                    echo '</tr>';
                                }
                                echo '</table>';
                            }
                        }
                    }
                    //else echo '<p>No strange links detected.</p>';
                }
                
                
            }
            else echo '<p class="msg_alert">No results. Please click <b>Start Scanner</b> button.</p>';
        } 

    ?>
    
    </div>
           
        
    </div>
    </div>	
    
    
    
                                        
                </div>
              </div>
              
              
              
              <div class="sixteen wide column settblocks" id="block4" style="display: none;">
                <div class="ui segment">
                    <h3 class="ui dividing header">Two-Factor Authentication</h3>
                    <?php $params = RT_SEO_Helper::Get_SQL_Params(); ?>
				  <div class="content">
						<div class="ui form full_h">
							<?php 
								if (isset($_GET['view']) && $_GET['view'] == 'tfa') $this->check_action();         
								$params = RT_SEO_Helper::Get_SQL_Params();
							?>
								<div class="ui ignored message">
									Two-factor authentication is a security process in which user provides two authentication factors to proof they are who they say they are. Our WordPress two-factor authentication plugin adds an extra layer of protection from unauthorized access. It stops all possible brute force attacks, even if your website has been hacked and your password has been stolen our smart two-step authentication tool will prevent your website from being compromised.
								</div>

							<form method="post" action="admin.php?page=rt_seo_security&view=tfa" novalidate="novalidate">

							<div class="inline field">
								<div class="ui toggle checkbox">
									<input type="checkbox" name="rt_seo_enable_2fa" id="rt_seo_enable_2fa" value="" <?php if (isset($params['rt_seo_enable_2fa']) && $params['rt_seo_enable_2fa'] == 1) echo 'checked="checked"'; ?>>
									<label for="rt_seo_enable_2fa">Enable Two-Factor Authentication for administrator login page</label>
									<?php
									    wp_nonce_field( 'name_3dfUegmeft' );
									?>
								</div>
							</div>
							<div class="ui right pointing red basic label">To complete your configuration please go to </div> <a class="ui red horizontal label" href="<?php echo get_site_url(); ?>/wp-admin/profile.php#tfa">your profile page</a>
							<h5 class="ui header">
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('tfa_help_tip');"><i class="question circle icon"></i><?php _e( 'Need more help?', 'rt_seo' )?></a>
							</h5>
							<div id="tfa_help_tip" style="display:none" class="ui secondary segment">
								<div class="ui grid">



										<div class="nine wide column">
											<h3 class="ui dividing header">Two-Factor Authentication Configuration</h3>
											<div>
											<p>Two-factor authentication is an improved security measure that requires two forms of identification: your password and a generated security code. With Two-factor authentication enabled, an application on your smartphone supplies a code that you must enter with your password to log in.</p>
											</div>
										</div>
										<div class="nine wide column">
											<h4 class="ui header">Google Authenticator</h4>
											<div>
											<p>Two-factor authentication requires a smartphone with a supported time-based one-time password app.</p>
											</div>
										</div>
										<div class="nine wide column">
											<h4 class="ui header">Step 1. Download App for your device</h4>
											<div>
											<p>Download and Install Google Authenticator on your smartphone or desktop.</p>

											<p>For Android™, iOS®, and Blackberry® — <a href="https://support.google.com/accounts/answer/1066447?hl=en" target="_blank">Google Authenticator</a></p>

											<p>Direct link to AppStore for iOS - <a href="http://appstore.com/googleauthenticator" target="_blank">Install Google Authenticator for iOS</a></p>

											<p>Direct link to Google Play market for Android - <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Install Google Authenticator for Android</a></p>
											</div>
										</div>
										<div class="nine wide column">
											<h4 class="ui header">Step 2. Configuration</h4>
											<div>
											<p>You can see a QR Code to scan with a mobile phone with the application of Google Authenticator installed. Or enter the code manually.</p>
											<p>Sample:</p>
											<img src="<?php echo plugins_url('images/two-factor-authentication.jpg', dirname(__FILE__)); ?>" />
											</div>
										</div>
										<div class="nine wide column">
											<h4 class="ui header">Step 3. Activate Two-Factor Authentication</h4>
											<div>
											<p>Now, your site access is protected by Two-Factor Authentication. Log out from your backend, you'll see that instead of asking for the username and password only, you will need to enter a secret key. The Secret Key is the six digit password you can see on your Google Authenticator screen.</p>
											</div>
										</div>

								</div>
							</div>
																
            
							<h4 class="ui header">Allow (whitelist) by IP or range (your IP is <?php echo $myIP; ?>)</h4>
							
							<div class="ui ignored message">
								  <i class="help circle icon"></i>e.g. 200.150.160.1 or 200.150.160.* or or 200.150.*.*
							</div>
							
							<div class="ui input" style="width: 100%;margin-bottom:10px">
								<textarea name="rt_seo_tfa_ip_list_allow" style="width: 100%;height:200px" placeholder="Insert IP addresses or range you want to allow, one by line"><?php if (isset($params['rt_seo_tfa_ip_list_allow']) && $params['rt_seo_tfa_ip_list_allow'] != '') echo $params['rt_seo_tfa_ip_list_allow']; ?></textarea>
							</div>
							<input type="submit" name="submit" id="submit" class="ui secondary button mrt-top-15" value="Save">   
							<input type="hidden" name="action" value="Save_tfa_params">   
							</form>
						</div>

	  
				  </div>

                </div>
              </div>
              
            </div>
            




    </div>
	<?php
  }



    public static function CreateSettingsFile()
    {
        $params = RT_SEO_Helper::Get_SQL_Params(array('frontend_ip_list', 'frontend_ip_list_allow', 'frontend_country_list', 'protection_frontend'));
        
        $line = '<?php $seo_sg_settings = "'.addslashes(json_encode($params)).'"; ?>';
        
        $fp = fopen(dirname(__FILE__).'/settings.php', 'w');
        fwrite($fp, $line);
        fclose($fp);
    }
    



	public static function CheckWPConfig_file()
	{
	    if (!file_exists(dirname(__FILE__).'/settings.php')) self::CreateSettingsFile();

		if (!defined('ABSPATH') || strlen(ABSPATH) < 8) 
		{
			$scan_path = dirname(__FILE__);
			$scan_path = str_replace(DIRSEP.'wp-content'.DIRSEP.'plugins'.DIRSEP.'rt-seo-pack', DIRSEP.'classes', DIRSEP, $scan_path);
    		//echo TEST;
		}
        else $scan_path = ABSPATH;
        
        $filename = $scan_path.DIRSEP.'wp-config.php';

        $handle = fopen($filename, "r");

        if ($handle === false) return false;
        $contents = fread($handle, filesize($filename));
        if ($contents === false) return false;
        fclose($handle);

        if (stripos($contents, '140FEEA75CC5-START') === false)     // Not found
        {

            self::PatchWPConfig_file();
        }
    }
    
	public static function PatchWPConfig_file($action = true)   // true - insert, false - remove
	{
        
		$file = dirname(__FILE__).DIRSEP."geo.check.php";
		$file = str_replace('\\', '/', $file);
        $integration_code = '<?php /* SEOPack Block 140FEEA75CC5-START */ if (substr($_SERVER["SCRIPT_FILENAME"], -12) != "wp-login.php" && strpos($_SERVER["SCRIPT_FILENAME"], "wp-admin") === false && file_exists("'.$file.'"))include_once("'.$file.'");/* SEOPack Block 140FEEA75CC5-END */?>';
        
        // Insert code
		if (!defined('ABSPATH') || strlen(ABSPATH) < 8) 
		{
			$scan_path = dirname(__FILE__);
			$scan_path = str_replace(DIRSEP.'wp-content'.DIRSEP.'plugins'.DIRSEP.'rt-seo-pack', DIRSEP.'classes', DIRSEP, $scan_path);
    		//echo TEST;
		}
        else $scan_path = ABSPATH;
        
        $filename = $scan_path.DIRSEP.'wp-config.php';
        $handle = fopen($filename, "r");
        if ($handle === false) return false;
        $contents = fread($handle, filesize($filename));
        if ($contents === false) return false;
        fclose($handle);
        
        $pos_code = stripos($contents, '140FEEA75CC5');
        
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
    


    
    public static function CountryList_checkboxes($selected_array = array())
    {
        $selected = array();
        if (is_array($selected_array) && count($selected_array))
        {
            foreach ($selected_array as $v)
            {
                $selected[$v] = $v;
            }
            
        }
        $a = '<div class="ui five column grid country_list">'."\n";

        foreach (RT_SEO_Geo_IP2Country::$country_list as $country_code => $country_name)
        {
            if (isset($selected[$country_code])) $checked = 'checked="checked"';
            else $checked = '';
            $a .= '<div class="column"><label><input class="country_'.$country_code.' '.RT_SEO_Geo_IP2Country::$country_type_list[$country_code].'" '.$checked.' type="checkbox" name="country_list[]" value="'.$country_code.'">'.$country_name.'</label></div>'."\n";
        }

        $a .= '</div>';
        
        return $a;
    }
    
    
  
    
    public function UpdateBlacklistStatus()
    {
        $domain = $this->PrepareDomain(get_site_url());
        
        $data = array(
            'latest_scan_date_blacklists' => date("Y-m-d H:i:s"),
            'latest_results' => array()
        );
        
        if (RTSEO_PLGSGWBM::Scan_in_Google($domain) == "BL") $data['latest_results'][] = 'Google';
        if (RTSEO_PLGSGWBM::Scan_in_McAfee($domain) == "BL") $data['latest_results'][] = 'McAfee';
        if (RTSEO_PLGSGWBM::Scan_in_Norton($domain) == "BL") $data['latest_results'][] = 'Norton';
        
        $URLVoid_arr = RTSEO_PLGSGWBM::Scan_in_URLVoid($domain);
        if (count($URLVoid_arr))
        {
            foreach ($URLVoid_arr as $row)
            {
                $data['latest_results'][] = $row;
            }
        }
        
        //print_r($data);
        
        
        $data['latest_results'] = json_encode($data['latest_results']);
        
        RT_SEO_Helper::Set_SQL_Params($data);

    }
	
	function check_action() 
		{
			$action = '';
			if (isset($_REQUEST['action'])) $action = sanitize_text_field(trim($_REQUEST['action']));
			
			// Actions
			if ($action != '')
			{
				$action_message = '';
				switch ($action)
				{   
					case 'EnableDisable_frontend_protection':
						if (check_admin_referer( 'name_2Jjf73gds8d' ))
						{
							$params = RT_SEO_Helper::Get_SQL_Params(array('protection_frontend'));
							RT_SEO_Helper::Set_SQL_Params(array('protection_frontend' => round(1 - $params['protection_frontend']) ));
							
							self::CreateSettingsFile();
							self::CheckWPConfig_file();
						}
						break;
						
					case 'Save_frontend_params':
						if (check_admin_referer( 'name_3dfUejeked' ))
						{
							$data = array();
							if (isset($_POST['frontend_ip_list'])) $data['frontend_ip_list'] = sanitize_text_field($_POST['frontend_ip_list']);
							if (isset($_POST['frontend_ip_list_allow'])) $data['frontend_ip_list_allow'] = sanitize_text_field($_POST['frontend_ip_list_allow']);
							if (isset($_POST['country_list'])) $data['frontend_country_list'] = $_POST['country_list'];
							else $data['frontend_country_list'] = array();

							
							$data['frontend_country_list'] = json_encode($data['frontend_country_list']);
							
							$action_message = 'GEO protection settings saved';
							
							RT_SEO_Helper::Set_SQL_Params($data);
							
							self::CheckWPConfig_file();
							self::CreateSettingsFile();
						}
						break;
						
					case 'Save_tfa_params':
						if (check_admin_referer( 'name_3dfUegmeft' ))
						{
							$data = array();
							$data['rt_seo_enable_2fa'] = isset($_POST['rt_seo_enable_2fa']) ? 1 : 0;
							$data['rt_seo_tfa_ip_list_allow'] = isset($_POST['rt_seo_tfa_ip_list_allow']) ? sanitize_text_field($_POST['rt_seo_tfa_ip_list_allow']) : '';

							$action_message = 'Two-Factor Authentication settings saved';
							
							RT_SEO_Helper::Set_SQL_Params($data);

						}
						break;

				}
				
				if ($action_message != '')
				{
					$message_data = array(
						'type' => 'info_white',
						'header' => '',
						'message' => $action_message,
						'button_text' => '',
						'button_url' => '',
						'help_text' => ''
					);
					echo '<div style="max-width:800px;margin-top: 10px;margin-bottom: 15px;">';
					$this->PrintIconMessage($message_data);
					echo '</div>';
				}
			}
			
			return RT_SEO_Helper::Get_SQL_Params();
			
		}
		
		
		
}
     
$RT_SEO_Security = new RT_SEO_Security();  
  
  


class RTSEO_URLVoidAPI
{
	private $_api;
	private $_plan;
	
    public $_output;
	public $_error;
	
	public function __construct( $api, $plan )
	{
		$this->_api = $api;
		$this->_plan = $plan;
	}
	
	/*
	 * Set key for the API call
	 */
	public function set_api( $api )
	{
		$this->_api = $api;
	}
	
	/*
	 * Set plan identifier for the API call
	 */
	public function set_plan( $plan )
	{
		$this->_plan = $plan;
	}

	/*
	 * Call the API
	 */
	public function query_urlvoid_api( $website, $first_time_scan = false )
	{
	    $curl = curl_init();
        
		if ($first_time_scan === true) curl_setopt ($curl, CURLOPT_URL, "http://api.urlvoid.com/".$this->_plan."/".$this->_api."/host/".$website."/scan/" );
		else curl_setopt ($curl, CURLOPT_URL, "http://api.urlvoid.com/".$this->_plan."/".$this->_api."/host/".$website."/rescan/" );
        
		curl_setopt ($curl, CURLOPT_USERAGENT, "API");
    	curl_setopt ($curl, CURLOPT_TIMEOUT, 30);
    	curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 30);
    	curl_setopt ($curl, CURLOPT_HEADER, 0);
    	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec( $curl );

		curl_close( $curl );
		return $result;
	}
	
	/*
	 * Convert array of engines to string
	 */
	public function show_engines_array_as_string( $engines, $last_char = ", " )
	{
   		if ( is_array($engines) )
		{
   		    foreach( $engines as $item ) $str .= trim($item).$last_char;
   		    return rtrim( $str, $last_char );
		}
		else
		{
		    return $engines;
		}
	}
	
	public function scan_host( $host )
	{
	    $output = $this->query_urlvoid_api( $host );
        
        if (stripos($output, '<action_result>ERROR</action_result>') !== false) $output = $this->query_urlvoid_api( $host, true );

		$this->_output = $output;
		
		$this->_error = ( preg_match( "/<error>(.*)<\/error>/is", $output, $parts ) ) ? $parts[1] : '';
		
		return json_decode( json_encode( simplexml_load_string( $output, 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOWARNING ) ), true );
	}
	
}


class RTSEO_PLGSGWBM
{
    public static $blacklists = array(
        'Google' => array('logo' => 'http://www.google.com/s2/favicons?domain=google.com'),
        'McAfee' => array('logo' => 'http://www.google.com/s2/favicons?domain=mcafee.com'),
        'Norton' => array('logo' => 'http://www.google.com/s2/favicons?domain=norton.com'),
        'Quttera' => array('logo' => 'http://www.google.com/s2/favicons?domain=quttera.com'),
        'ZeroCERT' => array('logo' => 'http://www.google.com/s2/favicons?domain=zerocert.org'),
        'AVGThreatLabs' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.avgthreatlabs.com'),
        'Avira' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.avira.com'),
        'Bambenek Consulting' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.bambenekconsulting.com'),
        'BitDefender' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.bitdefender.com'),
        'CERT-GIB' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.cert-gib.com'),
        'CyberCrime' => array('logo' => 'http://www.google.com/s2/favicons?domain=cybercrime-tracker.net'),
        'c_APT_ure' => array('logo' => 'http://www.google.com/s2/favicons?domain=security-research.dyndns.org'),
        'Disconnect.me (Malw)' => array('logo' => 'http://www.google.com/s2/favicons?domain=disconnect.me'),
        'DNS-BH' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.malwaredomains.com'),
        'DrWeb' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.drweb.com'),
        'DShield' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.dshield.org'),
        'Fortinet' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.fortinet.com'),
        'GoogleSafeBrowsing' => array('logo' => 'http://www.google.com/s2/favicons?domain=developers.google.com'),
        'hpHosts' => array('logo' => 'http://www.google.com/s2/favicons?domain=hosts-file.net'),
        'Malc0de' => array('logo' => 'http://www.google.com/s2/favicons?domain=malc0de.com'),
        'MalwareDomainList' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.malwaredomainlist.com'),
        'MalwarePatrol' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.malware.com.br'),
        'MyWOT' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.mywot.com'),
        'OpenPhish' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.openphish.com'),
        'PhishTank' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.phishtank.com'),
        'Ransomware Tracker' => array('logo' => 'http://www.google.com/s2/favicons?domain=ransomwaretracker.abuse.ch'),
        'SCUMWARE' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.scumware.org'),
        'Spam404' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.spam404.com'),
        'SURBL' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.surbl.org'),
        'ThreatCrowd' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.threatcrowd.org'),
        'ThreatLog' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.threatlog.com'),
        'urlQuery' => array('logo' => 'http://www.google.com/s2/favicons?domain=urlquery.net'),
        'URLVir' => array('logo' => 'http://www.google.com/s2/favicons?domain=urlvir.com'),
        'VXVault' => array('logo' => 'http://www.google.com/s2/favicons?domain=vxvault.net'),
        'WebSecurityGuard' => array('logo' => 'http://www.google.com/s2/favicons?domain=www.websecurityguard.com'),
        'YandexSafeBrowsing' => array('logo' => 'http://www.google.com/s2/favicons?domain=yandex.com'),
        'ZeuS Tracker' => array('logo' => 'http://www.google.com/s2/favicons?domain=zeustracker.abuse.ch'),
    );
    
    public static $api_urlvoid = array(
        '075d2746f96bc493d977e5c45c0e66457a147995',
        'd8a6c7bfc0bcdcafee9015f279fb87f0d2f98461',
        'e913bc7f9dd4c3d029774a8937ec0c6e48190ea2',
        'd99fdac6cbaed9d4549f1ba1b15f23950c7bcb54',
        'fcd3e995e2fd998bdaf63fa5c39423ec96fad48b',
        'b86d0094996fa5dedfa0a942d27081414ce4a9cb',
        '753b5c36de6bb9f7cfd726c7bf91020c1ecb547a',
        '095216e11be24a074ca4fe50a6d9bb8abd01e0c6',
        'dca6d53bf80cbd950cc6e2d4dce2d04772151342',
        'ed602b474bb3e1d670b5ed1ae43c8f323b736856',
        '2adc4c7b87647252fec79fde5a5ed2d01f7c57a7',
        'dbfee84de858035aafe6e26d81edd7c7b01660df',
        '91caa4eb6d2293099be5f3351c128cbdf957da9d'
    );
    
    
	function Scan_in_Google($domain)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://safebrowsing.googleapis.com/v4/threatMatches:find?key=AIzaSyBtFip7uxKIDAMCV9tQAfQZzFyW0_JQjuo",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => '  {
		"client": {
		  "clientId":      "siteguarding",
		  "clientVersion": "1.5.2"
		},
		"threatInfo": {
		  "threatTypes":      ["MALWARE", "SOCIAL_ENGINEERING"],
		  "platformTypes":    ["WINDOWS"],
		  "threatEntryTypes": ["URL"],
		  "threatEntries": [
			{"url": "https://'.$domain.'/"}
		  ]
		}
		  }',
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"postman-token: b05b8d34-85f2-49cf-0f8e-03686a71e4e9"
		  ),
		));

		$response = curl_exec($curl);
		if (curl_error($curl)) {
			$error_msg = curl_error($curl);
			echo $error_msg;
		}
		curl_close($curl);

		$response = json_decode($response, true);
		return $response;
		if (!isset($response['matches'])) return "OK";
		else return 'BL';

	}
    
    function Scan_in_McAfee($domain)
    {
        $url = "http://www.siteadvisor.com/sites/".$domain;
        $response = wp_remote_get( esc_url_raw( $url ) );
        $content = wp_remote_retrieve_body( $response );
        
    	if (strpos($content, 'siteYellow') || strpos($content, 'siteRed'))
        {
    		return 'BL';
    	} 
        else return 'OK';
    }
    
    function Scan_in_Norton($domain)
    {
        $url = "https://safeweb.norton.com/report/show?url=".$domain;
        $response = wp_remote_get( esc_url_raw( $url ) );
        $content = wp_remote_retrieve_body( $response );
        
    	if (strpos($content, $domain) !== false)
        {
    		if (!strpos($content, 'SAFE') && !strpos($content, 'UNTESTED'))
            {
    			return 'BL';
    		}
            else return 'OK';
    	}
    }
    
    function Scan_in_URLVoid($domain)
    {
        // check if domain is subdomain
        if(substr_count($domain, '.') > 1)
        {
            $pieces = explode(".", $domain);
            $last_piece = end($pieces);
            $domain = prev($pieces) . '.' . $last_piece;
        }
        
        $tmp_api_keys = self::$api_urlvoid;
        shuffle($tmp_api_keys);
        shuffle($tmp_api_keys);
        $URLVoidAPI = new RTSEO_URLVoidAPI( $tmp_api_keys[0], 'api1000' );
        $array = array();
        $array = $URLVoidAPI->scan_host( $domain );
        if (intval($array['detections']['count']) > 0) return $array['detections']['engines']['engine'];
        else return array();
    }


    
	function PrepareDomain($domain)
	{
	    $host_info = parse_url($domain);
	    if ($host_info == NULL) return false;
	    $domain = $host_info['host'];
	    if ($domain[0] == "w" && $domain[1] == "w" && $domain[2] == "w" && $domain[3] == ".") $domain = str_replace("www.", "", $domain);
	    
	    return $domain;
	}
}

?>