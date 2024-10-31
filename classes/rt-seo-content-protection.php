<?php

if (!class_exists('RT_SEO_Content_Protection')) { 
	class RT_SEO_Content_Protection extends RT_SEO_Helper
	{
		
		private $backupFolder;
		private $tableName;

		
		public function protection_panel() {

	
			//var_dump($this->getChangedPostsInfoByDate('2018-10-12', true));
		?>
			<form method="post" action="admin.php?page=rt_seo_protection">

			<div id="main" class="ui main container" style="float: left;margin-top: 20px; width:99%; max-width: 1150px!important;">
					<h2 class="ui dividing header">SEO Content Protection</h2>
					<div class="ui blue message">SEO Content Protection helps to track and monitor all the changes in posts content for the last 365 days</div>
			<?php
				$this->backupAllPosts();
				//echo $this->restoreAllPostByDate(date('Y-m-d'));
				$this->checkActions();
			?>
            <a class="ui green button" onclick="toggleVisibilityInstall('calendar')">        
                <i class="calendar outline icon"></i>
                Calendar
            </a>

			<?php


				//$snap = $this->checkAction();

				//if (!$snap) $snap = $this->compareSnapshots();
				//$dates = $this->getSnapshotDates();
				 
				$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date('Y-m-d');
				$flag = isset($_REQUEST['date']) ? 'display:none;' : '';

				$posts = $this->getPostsToCompareByDate($date);


				//$snap = $this->comparePosts($posts);
				echo "<div style='$flag' id='calendar'><br>";
				$this->printCalendar();
			?>
			
			</div><br><br>
				<div class="ui grid">
					<div class="sixteen wide column">
					<h4 class="ui dividing header">Changes for <?php echo $date; ?></h4>


						<?php 
						if (isset($posts) && count($posts)) {
							foreach ($posts as $post) {
								$snap = $this->comparePosts($post);
								$snap['old_snap_date'] = strtotime($post['date']);
								$username = $post['user_id'] ? get_userdata($post['user_id'])->user_login : '';
								//var_dump($snap);
								
								if (empty($snap['data']) && empty($snap['added']) && empty($snap['removed'])) {
									?>
									<div class="ui message"><?php echo "Post ID:  {$post['post']['ID']} has been edited " . date("Y-m-d H:i" , $snap['old_snap_date']). ' by user "' . $username . "\", but nothing has been changed. The title \"". $post['post']['post_title'] .'"';?></div>
									<?php				
								} else {
								
									if (isset($snap['removed']) && !empty($snap['removed'])) {
										foreach ($snap['removed'] as $id => $content) {
									?>
								
										<div class="ui negative message">
										<div  class="ui right floated header tiny">
											<a onclick="ConfirmRestore('<?php echo admin_url() . "admin.php?page=rt_seo_protection&date=$date&r_id={$post['ID']}"; ?>');" href="javascript:;" >
												<i class="window restore outline icon"></i>
												<div class="content">Restore&nbsp;&nbsp;&nbsp;</div>
											</a>
											<br />
											<a href="javascript:;" onclick="toggleVisibilityInstall('post<?php echo $id.$snap['old_snap_date']?>');">
												<i class="info circle icon"></i>
												<div class="content">Details</div>
											</a>
										</div>
										<div class="width80">
										<div class="ui red horizontal label">deleted</div>
										<b><?php echo $post['prev_post']['post_title']; ?></b><br />
										<?php echo "Post ID:  $id has been removed " . date("Y-m-d H:i" , $snap['old_snap_date']). ' by user "' . $username . "\". The title \"". $post['prev_post']['post_title'] .'"';?></div>
											<div id="post<?php echo $id.$snap['old_snap_date']; ?>"  style="display:none"><hr>
												<div class="ui bulleted list Differences DifferencesSideBySide">
													<h4 class="ui header">Content:</h4>
													<?php echo $content;?>
												</div>
											</div>
										</div>
									<?php
										}
									}
									
									?>

								
									
									
									<?php
									if (isset($snap['added']) && !empty($snap['added'])) {
										foreach ($snap['added'] as $id => $content) {
									?>
									
										<div class="ui positive message">

											<div  class="ui right floated header tiny">
												<a onclick="ConfirmRestore('<?php echo admin_url() . "admin.php?page=rt_seo_protection&date=$date&r_id={$post['ID']}"; ?>');" href="javascript:;" >
													<i class="window restore outline icon"></i>
													<div class="content">Restore&nbsp;&nbsp;&nbsp;</div>
												</a>
                                                <br />
												<a href="javascript:;" onclick="toggleVisibilityInstall('post<?php echo $id.$snap['old_snap_date']; ?>');">
													<i class="info circle icon"></i>
													<div class="content">Details</div>
												</a>
											</div>
											<div class="width80">
												<div class="ui green horizontal label">new</div>
                                                <b><?php echo $post['post']['post_title']; ?></b><br />
												<?php echo "Post ID:  $id has been added " . date("Y-m-d H:i" , $snap['old_snap_date']). ' by user "' . $username . "\".";?>
											</div>
											<div id="post<?php echo $id.$snap['old_snap_date']; ?>"  style="display:none"><hr>
												<div class="ui bulleted list">
													<h4 class="ui header">Content:</h4>
													<?php echo $content;?>
												</div>
											</div>
										</div>
									<?php
										}

									}
									
									if (isset($snap['data']) && !empty($snap['data'])) {
										foreach ($snap['data'] as $id => $params) {
											if (is_array($params) && count($params)) {
												?>

												<div class="ui message">
												<div  class="ui right floated header tiny">
													<a onclick="ConfirmRestore('<?php echo admin_url() . "admin.php?page=rt_seo_protection&date=$date&r_id={$post['prev_ID']}"; ?>');" href="javascript:;" >
														<i class="upload icon"></i>
														<div class="content">Restore&nbsp;&nbsp;&nbsp;</div>
													</a>
                                                    <br />
													<a href="javascript:;" onclick="toggleVisibilityInstall('post<?php echo $id.$snap['old_snap_date'];?>');">
														<i class="info circle icon"></i>
														<div class="content">Details</div>
													</a>
												</div>
												<div class="width80">
                                                    <div class="ui red horizontal label">changed</div>
                                                    <b><?php echo $post['post']['post_title']; ?></b><br />
    												<?php echo "Post ID:  $id has been changed " . date("Y-m-d H:i" , $snap['old_snap_date']). ' by user "' . $username . "\".";?></div>
    													<div id="post<?php echo $id.$snap['old_snap_date'];?>"  style="display:none"><hr>
    													<div class="ui bulleted list">
    												<?php
												foreach ($params as $param => $value) { 

													if (is_array($value)) {
														foreach ($value as $key => $item) { 
														?>
														<div class="width80"><i class="exclamation triangle icon red"></i>
															<?php echo ucfirst($param); ?> changed.<a href="javascript:;" onclick="toggleVisibilityInstall('post<?php echo $id . $param . $key . $snap['old_snap_date'];?>');"> ( View changes )</a>
														</div>
														<div id="post<?php echo $id . $param . $key . $snap['old_snap_date'];?>" style="display:none"><hr>
															<div class="ui bulleted list">
																<?php
																	echo $item;
																?>
															</div>
														</div>
													<?php
														}
													} else {
													?>

														<div class="width80"><i class="exclamation triangle icon red"></i>
														<?php echo ucfirst($param); ?> has been changed.<a href="javascript:;" onclick="toggleVisibilityInstall('post<?php echo $id . $param . $snap['old_snap_date'];?>');"> ( View changes )</a></div>
															<div id="post<?php echo $id . $param . $snap['old_snap_date'];?>" style="display:none"><hr>
																<div class="ui bulleted list">
																	<?php
																		echo $value;
																	?>
																</div>
															</div>
												<?php 
													} 
												}	
												?>
												</div>
															</div>
															</div>

															<?php
												

											}

										}
										
									
									}
								}
							}			
						} else {
							?>
							<div class="ui message">No any changes for this date.</div>
							<?php
						}								
							?>


						<a id="full_restore_btn" onclick="jQuery('.tiny.modal.full_restore').modal('show');" href="javascript:;" class="ui medium floated right  secondary button">Restore All for <?php echo $date; ?></a>

					</div>
					<div class="ui mini modal restore">
					  <div class="header">
						Restore The Post
					  </div>
					  <div class="content">
						Are you sure you want to restore the post?
					  </div>
					  <div class="actions">
						<div class="ui negative button" data-value="No">No</div>
						<div id="rtseocp_yes" class="ui positive button" data-value="">Yes</div>
					  </div>
					</div>
					<div class="ui tiny modal full_restore">
					  <div class="header">
						Restore All Posts
					  </div>
					  <div class="content">
						Are you sure you want to restore all posts for <?php echo $date; ?>?
					  </div>
					  <div class="actions">
						<div class="ui negative button" data-value="No">No</div>
						<div id="full_restore" class="ui positive button" data-value="<?php echo admin_url() . "admin.php?page=rt_seo_protection&r_full={$date}&nonce=" . wp_create_nonce( 'name_dsyvq2m34n2' ); ?>">Yes</div>
					  </div>
					</div>
			
				</div>
			</div>
		<script>
			jQuery('.ui.dropdown').dropdown();
			function ConfirmRestore(link) {
				jQuery("#rtseocp_yes").attr('data-value', link);//("", );// = link;
				jQuery('.mini.modal.restore')
					.modal('show');
				;
			}
			
			function ConfirmFullRestore(link) {
				jQuery('.mini.modal.full_restore')
					.modal('show');
				;
			}
			
			jQuery(document).on("click", "#rtseocp_yes", function () {
				window.location.href = jQuery(this).data("value");
			});
			
			jQuery(document).on("click", "#full_restore", function () {
				window.location.href = jQuery(this).data("value");
			});
		</script>
			
			</form>
		<?php                                                                              
		
		}


		public function __construct() {
			global $wpdb;
			
			$this->tableName = $wpdb->prefix . 'rtseo_content_protection';
			$this->installDate = $this->getInstallDate();
			$this->backupFolder = WP_CONTENT_DIR . DIRSEP . 'rt-seo-backup-content';
			
			$filename = $this->backupFolder . DIRSEP;
			if(!is_dir($filename)) mkdir($filename);
			
			$filename = $filename.'.htaccess';
			if(!file_exists($filename)) 
			{
				$fp = fopen($filename, 'w');
				fwrite($fp, '<Limit GET POST>'."\n".'order deny,allow'."\n".'deny from all'."\n".'</Limit>');
				fclose($fp);
			}
		}

		public function getInstallDate() {
			global $wpdb;
		
			if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $this->tableName .'"' ) == $this->tableName ) {
				$installDate = $wpdb->get_col( "SELECT date FROM {$this->tableName} WHERE install_flag = 1 ORDER BY date ASC LIMIT 1 " );
			}
	
			return (isset($installDate)) ? date('Y-m-d', strtotime($installDate[0])) : '';
		}

		public function protection_panel__() {
			var_dump($this->backupAllPosts());
			var_dump($this->comparePosts('2018-10-02'));
			echo '111';
		}

		
		public function restorePosts($ids) {
			
			global $wpdb;
			
			$i = 0;
			if (!$ids || !is_array($ids) || empty($ids)) return false;
			
			foreach ($ids as $id) {
				$backupedPostsInfo = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$this->tableName} WHERE id = %d  LIMIT 1", $id), ARRAY_A );
				//var_dump($backupedPostsInfo);
				$post = $this->readStoredPost($backupedPostsInfo[0]['filepath']);
				
				$postId = (int) $backupedPostsInfo[0]['post_id'];
				
				$postArr = array(
					'ID' => $postId,
					'post_author' => $backupedPostsInfo[0]['user_id'],
					'post_content' => $post['post_content'],
					'post_title' => $post['post_title'],
					'post_name' => str_replace("__trashed", "", $post['post_name']),
					'post_status' => 'publish',
					'post_type' => $post['post_type'],
				);
					

				if (get_post($postId)) {
					if (wp_insert_post($postArr, true)) $i++;	
				} else {
					if ($wpdb->insert($wpdb->prefix . 'posts', array(
													"ID" => $postId,
													"post_title" => $postArr['post_title'],
													"post_content" => $postArr['post_title'],
													"post_name" => $postArr['post_name'],
													"post_author" => $postArr['post_author'],
													'post_status' => $postArr['post_status'],
													"post_date" => $backupedPostsInfo[0]['date'],
													"post_date_gmt" => $backupedPostsInfo[0]['date'],
													"post_modified" => $backupedPostsInfo[0]['date'],
													"post_modified_gmt" => $backupedPostsInfo[0]['date'],		
												))) {
													$this->backupPost((int) $backupedPostsInfo[0]['post_id']);
													$i++;
												}
					
				}
				
				if ($post['post_description']) add_post_meta( $backupedPostsInfo[0]['post_id'], 'rtseo_description', $post['post_description'] );
				//echo 'ID = ' . $backupedPostsInfo[0]['post_id'];

			}
			
			return $i;
		}
		
		
		public function getAllPostsIDs() {
			global $wpdb;
			
			$postIds = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE (post_type='page' OR post_type='post') AND post_status = 'publish'" );
			
			return $postIds;
		}
    
    
		public function Read_Latest_json()
		{
			$filename = $this->backupFolder.DIRSEP.'latest.json';
			if(!file_exists($filename)) return array();
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			
			$latest_json = (array)json_decode($contents, true);
			if ($latest_json === false) return array();
			
			return $latest_json;
		}

		
		public function Save_Latest_json($a)
		{		
			$filename = $this->backupFolder.DIRSEP.'latest.json';
			$fp = fopen($filename, 'w');
			fwrite($fp, json_encode($a));
			fclose($fp);
		}
		
	
		public function backupAllPosts() {

			$post_ids = $this->getAllPostsIDs();
			
			$latest_md5 = $this->Read_Latest_json();
			$latest_md5_new = array();
			
			if (count($post_ids))
			{
				// Backup by 10
				$max = 10;
				for ($i = 0; $i < ceil(count($post_ids) / $max); $i++)
				{
					$post_ids_block = array_slice($post_ids, $i * $max, $max);
					
					foreach ($post_ids_block as $post_id)
					{
						$post = get_post($post_id);
						
						$md5_post_title = md5($post->post_title);
						$md5_post_content = md5($post->post_content);
						
						if ( isset($latest_md5[$post_id]) && $latest_md5[$post_id] == $md5_post_title.$md5_post_content) 
						{
							// Skip backup
							$latest_md5_new[$post_id] = $latest_md5[$post_id];
							continue;  
						}
						
						$description = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'rtseo_description', true))));
						
						

						$postArray = array(
							'ID' => $post->ID,
							'post_title' => $post->post_title,
							'post_content' => $post->post_content,
							'post_description' => $description,
							'post_name' => $post->post_name,
							'post_type' => $post->post_type,
						);
						
						$this->savePost($postArray, 0, false, true);
						

						
						$latest_md5_new[$post_id] = $md5_post_title.$md5_post_content;
					}
				}
				
				$this->Save_Latest_json($latest_md5_new);
			}


					
		}



		public function backupPost($post_id) {
			
			$post = get_post($post_id);
			
			if ($post->post_status != 'publish') return;
			
			
			$latest_md5 = $this->Read_Latest_json();

			$md5_post_title = md5($post->post_title);
			$md5_post_content = md5($post->post_content);
			
			if ( isset($latest_md5[$post_id]) && $latest_md5[$post_id] == $md5_post_title.$md5_post_content) 
			{
				// Skip backup
				return;  
			}
			
			$description = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'rtseo_description', true))));
			
			$postArray = array(
				'ID' => $post->ID,
				'post_title' => $post->post_title,
				'post_content' => $post->post_content,
				'post_description' => $description,
				'post_name' => $post->post_name,
				'post_type' => $post->post_type,
			);
			
			$this->savePost($postArray, 1);
						
			$latest_md5[$post_id] = $md5_post_title.$md5_post_content;
				
			$this->Save_Latest_json($latest_md5);
			
		

		}



		public function backupPostRemoved($post_id) {
			
			$post = get_post($post_id);
			
			if ($post->post_status != 'trash') return;
			

			
			$description = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'rtseo_description', true))));
			
			$postArray = array(
				'ID' => $post->ID,
				'post_title' => $post->post_title,
				'post_content' => $post->post_content,
				'post_description' => $description,
				'post_name' => $post->post_name,
				'post_type' => $post->post_type,
			);
			
			$this->savePost($postArray, 1, true);		

		}

		public function savePost($postArray, $userId, $delete = false, $install = false) {
			
			global $wpdb;
			
			$delFlag = $delete ? 1 : 0;
			$installFlag = $install ? 1 : 0;
			
			if ($userId !== 0) $userId = get_current_user_id();

			$dir = $this->backupFolder . DIRSEP . $postArray['ID'] . DIRSEP;
			if(!is_dir($dir)) mkdir($dir);
			
			$filename = $dir . date("Y-m-d_His") . '.bak';
			if(!file_exists($filename)) 
			{
				$fp = fopen($filename, 'w');
				fwrite($fp, json_encode($postArray));
				fclose($fp);
			}

			if(is_file($filename))	{
				//		echo $filename;
				//die;
				$wpdb->insert($this->tableName, array(
												"post_id" => $postArray['ID'],
												"filepath" => $filename,
												"user_id" => $userId,
												"del_flag" => $delFlag,
												"install_flag" => $installFlag,
											));
											//$wpdb->print_error();
											//die;
			}
		}


		public function getDatesWithChanges() {
			global $wpdb;
			
			$dates = $wpdb->get_col( "SELECT date FROM $this->tableName" );
			
			foreach ($dates as $k => $v) {
				$dates[$k] = substr($v, 0, 10);
			}
			
			$dates = array_unique($dates);
			
			return $dates;
		}


		public function getChangedPostsInfoByDate($date, $latest = false) {
			global $wpdb;

			if ($latest) {
				//$date = str_replace('-', '/', $date);
				//$date = date('Y-m-d',strtotime($date . "+1 days"));

				$posts = $wpdb->get_col( $wpdb->prepare("SELECT x.id
															FROM {$this->tableName} x
															JOIN (SELECT p.post_id,
																		 MAX(p.date) AS max_total
																	FROM {$this->tableName} p
																	WHERE p.date < DATE_FORMAT(%s, '%%Y-%%m-%%d %%H:%%i')
																GROUP BY p.post_id) y ON y.post_id= x.post_id
																					  AND y.max_total = x.date
															GROUP BY x.post_id, x.date" , $date));
			} else {
				$posts = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$this->tableName} WHERE DATE_FORMAT(date, '%%Y-%%m-%%d') = %s and install_flag = 0" , $date), ARRAY_A );
							//var_dump($posts);
							//var_dump($date);
			}
			
			return $posts;
		}

		public function dateCompare($a, $b)
		{
			$t1 = strtotime($a['date']);
			$t2 = strtotime($b['date']);
			return $t2 - $t1;
		}  

		public function getPostsToCompareByDate($date) {
			
			$prevPostsInfo = array();
			$posts = array();
			
			global $wpdb;

			$postsInfo = $this->getChangedPostsInfoByDate($date);
			
			usort($postsInfo, array($this, 'dateCompare'));
							
			
			foreach ($postsInfo as $k => $postInfo) {

					$posts[$k]['ID'] = $postInfo['id'];
					$posts[$k]['user_id'] = $postInfo['user_id'];
					$posts[$k]['date'] = $postInfo['date'];	
					//var_dump($postInfo['date']);
				//	var_dump($postInfo['post_id']);
				if ($postInfo['del_flag']) {
					$posts[$k]['post'] = '';
					$posts[$k]['prev_post'] = $this->readStoredPost($postInfo['filepath']);
				} else {
					$prevPostsInfo = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$this->tableName} WHERE date < %s AND post_id = %d ORDER BY date DESC LIMIT 1", $postInfo['date'], $postInfo['post_id']), ARRAY_A );
					//var_dump($prevPostsInfo);
					/*
					if (isset($prevPostsInfo[0]['del_flag']) && $prevPostsInfo[0]['del_flag']) {
					$posts[$k]['post'] = $this->readStoredPost($postInfo['filepath']);
					$posts[$k]['prev_post'] = '';
					}
*/
					//if (!$prevPostsInfo) continue;
					if ($postInfo) {
						$posts[$k]['post'] = $this->readStoredPost($postInfo['filepath']);
					} else {
						$posts[$k]['post'] = '';
					}
					
					if ($prevPostsInfo && $prevPostsInfo[0]['del_flag'] == 0) {
						$posts[$k]['prev_ID'] = $prevPostsInfo[0]['id'];
						$posts[$k]['prev_post'] = $this->readStoredPost($prevPostsInfo[0]['filepath']);
					} else {
						$posts[$k]['prev_post'] = '';
					}
				}
			}			
			
			
			return $posts;
		}
		
		
		public function printCalendar() {
			
			global $wpdb;
			
            $date_start = mktime(0, 0, 0, date("m")-11, 1,   date("Y"));
            $date_end = mktime(0, 0, 0, date("m")+1, 1,   date("Y"));
            
            $history_arr = array();
            
            $date_current = $date_start;
            
            $i = 0;
            while($date_current < $date_end) 
            {
                $tmp_i = $date_start+$i*24*60*60;
                
                $date_current = $tmp_i;
                $month_current = date("m", $tmp_i);
                $day_current = date("d", $tmp_i);
                $year_current = date("Y", $tmp_i);
                
                if ($date_current < $date_end)
                {
					if ($res = $this->getChangedPostsInfoByDate(date("Y-m-d", $tmp_i))) {
						$history_arr[$year_current.'-'.$month_current.'-01'][$day_current] = count($res);
					} else {
						$history_arr[$year_current.'-'.$month_current.'-01'][$day_current] = 0;
					}
                }
                
                $i++;
            }

				    
            ?>
            <div class="ui grid seo_cal">
            <?php
            $i_block = 1;
            foreach ($history_arr as $month => $month_arr)
            {
                $txt_Yd = date("Y-m", strtotime($month));
                
                echo '<div class="four wide column">';
                
                echo '<h4 class="ui header">'.date("F Y", strtotime($month)).'</h4>';
                
                ?>
                    <ul class="seo_cal_weekdays">
                      <li>Su</li>
                      <li>Mo</li>
                      <li>Tu</li>
                      <li>We</li>
                      <li>Th</li>
                      <li>Fr</li>
                      <li>Sa</li>
                    </ul>
                    <ul class="seo_cal_days">
                <?php
                $weekday = date("w", strtotime($txt_Yd.'-01'));
                for ($i = 0; $i < $weekday; $i++)
                {
                    echo '<li>&nbsp;</li>';
                }
                foreach ($month_arr as $day => $changes)
                {
                    $changes_class = '';
                    $changes_link = ' href="admin.php?page=rt_seo_protection&date='.$txt_Yd.'-'.$day.'"';
                    if ($changes > 0) echo '<li><a '.$changes_link.'><div class="ui red circular label">'.intval($day).'</div></a></li>';
                    else echo '<li><a href="admin.php?page=rt_seo_protection&date='.$txt_Yd.'-'.$day.'">'.intval($day).'</a></li>';
  
                }
                ?>
                    </ul>
                </div>
                <?php
                
            }
            ?>
            </div>
            <?php


		}


		public function readStoredPost($filename) {
			if(!is_file($filename)) return false;
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			return (array)json_decode($contents, true);
		}

		public function parseStoredPosts($post) {
			$results = array();
			
			if ($post) {
				global $wpdb;

				


				$link = isset($post['ID']) ? get_permalink($post['ID']) : '';


				$title = isset($post['post_title']) ? $post['post_title'] : '';
				$description = isset($post['post_description']) ? $post['post_description'] : '';


				$results['data'][$post['ID']]['permalink'] = $link;
				$results['data'][$post['ID']]['title'] = $title;
				$results['data'][$post['ID']]['description'] = $description;
				$results['data'][$post['ID']]['content'] = isset($post['post_content']) ? $post['post_content'] : '';

				$html = isset($post['post_content']) ? str_get_html($post['post_content']) : '';

				if ($html) {

					$images = $html->find('img');
					foreach ($images as $image) {
						if(isset($image->alt) && $image->alt != '') {
							$results['data'][$post['ID']]['image_alt'][] = $image->alt;
						}
						if(isset($image->title) && $image->title != '') {
							$results['data'][$post['ID']]['image_title'][] = $image->title;
						}
					}

					$h1Tags = $html->find('h1');
					
					if (!empty($h1Tags)) {
						foreach ($h1Tags as $h1) {
							if(isset($h1->plaintext) && $h1->plaintext != '') {
								$results['data'][$post['ID']]['h1'][] = $h1->plaintext;
							}
						}
					}

					$h2Tags = $html->find('h2');
					
					if (!empty($h1Tags)) {
						foreach ($h2Tags as $h2) {
							if(isset($h2->plaintext) && $h2->plaintext != '') {
								$results['data'][$post['ID']]['h2'][] = $h2->plaintext;
							}
						}
					}

					$h3Tags = $html->find('h3');
					
					if (!empty($h3Tags)) {
						foreach ($h3Tags as $h3) {
							if(isset($h1->plaintext) && $h1->plaintext != '') {
								$results['data'][$post['ID']]['h3'][] = $h3->plaintext;
							}
						}
					}

					$h4Tags = $html->find('h4');
					
					if (!empty($h4Tags)) {
						foreach ($h4Tags as $h4) {
							if(isset($h1->plaintext) && $h4->plaintext != '') {
								$results['data'][$post['ID']]['h4'][] = $h4->plaintext;
							}
						}
					}

					$h5Tags = $html->find('h5');
					
					if (!empty($h5Tags)) {
						foreach ($h5Tags as $h5) {
							if(isset($h5->plaintext) && $h1->plaintext != '') {
								$results['data'][$post['ID']]['h5'][] = $h5->plaintext;
							}
						}
					}

					$h6Tags = $html->find('h6');
					
					if (!empty($h6Tags)) {
						foreach ($h6Tags as $h6) {
							if(isset($h6->plaintext) && $h6->plaintext != '') {
								$results['data'][$post['ID']]['h6'][] = $h6->plaintext;
							}
						}
					}

				}


			
			}
		    return $results;
		}


		
		public function comparePosts($post) {
			//$data = $this->getPostsToCompareByDate($date);
			$newSnap = $this->parseStoredPosts($post['post']);
			//var_dump($newSnap);
			//echo '<pre>';
			//var_dump($data);
			$oldSnap = $this->parseStoredPosts($post['prev_post']);
			//var_dump($oldSnap);
			//print_r($oldSnap); 
			if ($oldSnap === false) return false;
			
			$result['data'] = array();
			$result['removed'] = array();
			$result['added'] = array();
			
			if (isset($newSnap['data']) && is_array($newSnap['data']) && !empty($newSnap['data']))	 {
				foreach ($newSnap['data'] as $id => $params) {
					if (!isset($oldSnap['data'][$id])) {
						$result['added'][$id] = $this->htmlDiff('', @$newSnap['data'][$id]['content']);
						continue;
					}
					foreach($params as $param => $value) {
						//if ($param == 'permalink') onctinue
						//echo $value . ' = ' . $oldSnap['data'][$id][$param] . '<br>'; 	
						if (is_array($value)) {
							foreach ($value as $key => $item) {

								if ($item != @$oldSnap['data'][$id][$param][$key]) {
									$result['data'][$id][$param][] = $this->htmlDiff(@$oldSnap['data'][$id][$param][$key], $item);
								}
							}
						} else {
							if ($value != @$oldSnap['data'][$id][$param]) { 
								if (!is_string($value) || !is_string(@$oldSnap['data'][$id][$param])) continue;
								
								$result['data'][$id][$param] = $this->htmlDiff(@$oldSnap['data'][$id][$param], $value);
							}
						}
					}
				}
			}
			
			if (isset($oldSnap['data']) && is_array($oldSnap['data']) && !empty($oldSnap['data'])) {
				foreach ($oldSnap['data'] as $id => $params) {
					//var_dump($newSnap['data'][$id]);
					if (!isset($newSnap['data'][$id])) {
						$result['removed'][$id] = $this->htmlDiff(@$oldSnap['data'][$id]['content'], '');
					}
					/*
					foreach($params as $param => $value) {				
						//if ($param == 'permalink') onctinue
						//echo $value . ' = ' . $oldSnap['data'][$id][$param] . '<br>';
						if (is_array($value)) {
								//var_dump(@$newSnap['data'][$id][$param][$key]);
							foreach ($value as $item) {
								if ($item != @$newSnap['data'][$id][$param][$key]) {
									$result['data'][$id][$param][] = $this->htmlDiff(@$newSnap['data'][$id][$param][$key], $item);
								}
							}
						} else {
							if ($value != @$newSnap['data'][$id][$param]) {
								if (!is_string($value) || !is_string(@$newSnap['data'][$id][$param])) continue;
								$result['data'][$id][$param] = $this->htmlDiff($value, @$newSnap['data'][$id][$param]);
							}
						}
					}
					
					*/
				}
			}
			return $result;
		}
		
		public function restoreAllPostByDate($date) {

			$changedPostsIds = $this->getChangedPostsInfoByDate($date, true);
			
			if (!is_array($changedPostsIds) || empty($changedPostsIds)) return false;

			return $this->restorePosts($changedPostsIds);
			
		}
		
		public function checkActions() {
			if (isset($_REQUEST['r_id'])) {
				$restoreId = (int) $_REQUEST['r_id'];
				if ($restoreId) {
					$count = $this->restorePosts(array($restoreId));
					$msg_data = array( 
						'type' => ($count) ? 'ok' : 'error',
						'size' => 'small',
						'content' => ($count) ? $count . ' post(s) restored' : 'Error. Contact Support.'
					);

					$this->Print_MessageBox($msg_data);
				}
			}
			
			if (isset($_REQUEST['nonce']) && wp_verify_nonce( $_REQUEST['nonce'] , 'name_dsyvq2m34n2') && isset($_REQUEST['r_full'])) {
					
					
					$date = $_REQUEST['r_full'];
					$result = $this->restoreAllPostByDate($date);
					$msg_data = array( 
						'type' => ($result) ? 'ok' : 'error',
						'size' => 'small',
						'content' => ($result) ?  'Post(s) restored on ' . date('Y-m-d H:i', strtotime($date)) . '.' : '0 posts has been restored.',
					);

					$this->Print_MessageBox($msg_data);

				
			}
			
			
		}

		public function diff($old, $new){

			$matrix = array();
			$maxlen = 0;
			foreach($old as $oindex => $ovalue){
				$nkeys = array_keys($new, $ovalue);
				foreach($nkeys as $nindex){
					$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
						$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
					if($matrix[$oindex][$nindex] > $maxlen){
						$maxlen = $matrix[$oindex][$nindex];
						$omax = $oindex + 1 - $maxlen; 
						$nmax = $nindex + 1 - $maxlen;
					}
				}   
			}
			if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
			return array_merge(
				$this->diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
				array_slice($new, $nmax, $maxlen),
				$this->diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
		}
		
		function htmlDiff($old, $new){
			$ret = '';
			$old = str_replace("\n\n", "\n", $old);
			$new = str_replace("\n\n", "\n", $new);
			$old = str_replace("\r\n", "\n", $old);
			$new = str_replace("\r\n", "\n", $new);
			$old = str_replace("\n", "\n ", $old);
			$new = str_replace("\n", "\n ", $new);
			$diff = $this->diff(explode(" ", $old),explode(" ", $new));//(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));
			foreach($diff as $k){
				if(is_array($k))
					$ret .= (!empty($k['d'])?"<del style='background-color:rgba(255, 170, 202, 0.7)'>".nl2br(htmlspecialchars(implode(' ',$k['d']), ENT_SUBSTITUTE))." </del>":'').
						(!empty($k['i'])?"<ins style='background-color:rgba(0, 255, 0, 0.3)'>".nl2br(htmlspecialchars(implode(' ',$k['i']), ENT_SUBSTITUTE))."</ins> ":'');
				else $ret .= "<span style='background-color:rgba(0, 255, 255, 0.05)'>" . nl2br(htmlspecialchars($k, ENT_SUBSTITUTE)) . ' </span>';
			}
			//file_put_contents('deb.log', $ret, FILE_APPEND);
			return $ret;
		}

	}
	
	$RT_SEO_Content_Protection = new RT_SEO_Content_Protection();
	
	add_action( 'save_post', array($RT_SEO_Content_Protection, 'backupPost') );
	add_action( 'before_delete_post', array($RT_SEO_Content_Protection, 'backupPostRemoved') );

}