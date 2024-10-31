<?php

class RT_SEO_Helper
{
    var $plugin_slug;
    var $webanalyzeFolder;
    var $indexFile;
    var $apiKeyFile;
    var $licenceInfo;
    var $membership;
	var $apiUrl;
	
    public static $LINKS = array(
        'get_ssl' => 'https://seoguarding.com/contact-us/',
        'wp_debug' => 'https://codex.wordpress.org/Debugging_in_WordPress',
        'buy_plugin' => 'https://seoguarding.com/wordpress-plugin-seoguarding',
        'get_pro' => 'https://seoguarding.com/contact-us/',
    );
	
    public static $categories_left = array(
		100 => "All categories",
		1 => "Arts & Entertainment",
		2 => "Autos & Vehicles",
		3 => "Beauty & Fitness",
		4 => "Books & Literature",
		5 => "Business & Industrial",
		6 => "Computers & Electronics",
		7 => "Finance",
		8 => "Food & Drink",
		9 => "Games",
		10 => "Health",
		11 => "Hobbies & Leisure",
		12 => "Home & Garden",
		13 => "Internet & Telecom"
    );
	
    public static $categories_right = array(
		14 => "Jobs & Education",
		15 => "Law & Government",
		16 => "News",
		17 => "Online Communities",
		18 => "People & Society",
		19 => "Pets & Animals",
		20 => "Real Estate",
		21 => "Reference",
		22 => "Science",
		23 => "Shopping",
		24 => "Sports",
		25 => "Travel",
		0 => "Not Categorized"
    );
	
    function __construct(){
		if (!defined('ABSPATH') || strlen(ABSPATH) < 8) 
		{
			$root_path = dirname(__FILE__);
			$root_path = str_replace(DIRSEP.'wp-content'.DIRSEP.'plugins'.DIRSEP.'rt-seo-pack', DIRSEP.'classes', DIRSEP, $scan_path);
		}
		else $root_path = ABSPATH;

		$this->webanalyzeFolder = $root_path . 'webanalyze' . DIRSEP;
		$this->indexFile = $this->webanalyzeFolder . 'index.html';
		$this->apiKeyFile = $this->webanalyzeFolder . 'seo_verification.txt';
		$this->licenceInfo = RT_SEO_Validation::readLicenceInfo();
		$this->membership = isset($this->licenceInfo['membership']) ? $this->licenceInfo['membership'] : '';
		$this->apiUrl = "http://portal.seoguarding.com/api/index.php";
    	$this->class_name = sanitize_title( get_class($this) );
		
    }

	function get_admin_page_url() {
	  return get_admin_url().'options-general.php?page='.$this->plugin_slug;
	}  
  
  
    function http_request($method, $url, $data = '', $auth = '', $check_status = true)
    {
	  $body = '';
      $status = 0;
      $method = strtoupper($method);
      
      if (function_exists('curl_init')) {
          $ch = curl_init();
          
		  

		  
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)');
          curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
          curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
          //curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

          
          switch ($method) {
              case 'POST':
                  curl_setopt($ch, CURLOPT_POST, true);
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                  break;
              
              case 'PURGE':
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PURGE');
                  break;
          }
          
          if ($auth) {
              curl_setopt($ch, CURLOPT_USERPWD, $auth);
          }
          
          $contents = curl_exec($ch);
          
          $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          //print curl_error($ch);
          curl_close($ch);
      } else {
          $parse_url = @parse_url($url);
          
          if ($parse_url && isset($parse_url['host'])) {
              $host = $parse_url['host'];
              $port = (isset($parse_url['port']) ? (int) $parse_url['port'] : 80);
              $path = (!empty($parse_url['path']) ? $parse_url['path'] : '/');
              $query = (isset($parse_url['query']) ? $parse_url['query'] : '');
              $request_uri = $path . ($query != '' ? '?' . $query : '');
              
              $request_headers_array = array(
                  sprintf('%s %s HTTP/1.1', $method, $request_uri), 
                  sprintf('Host: %s', $host), 
                  sprintf('User-Agent: %s', W3TC_POWERED_BY), 
                  'Connection: close'
              );
              
              if (!empty($data)) {
                  $request_headers_array[] = sprintf('Content-Length: %d', strlen($data));
              }
              
              if (!empty($auth)) {
                  $request_headers_array[] = sprintf('Authorization: Basic %s', base64_encode($auth));
              }
              
              $request_headers = implode("\r\n", $request_headers_array);
              $request = $request_headers . "\r\n\r\n" . $data;
              $errno = null;
              $errstr = null;
              
              $fp = @fsockopen($host, $port, $errno, $errstr, 10);
              
              if (!$fp) {
                  return false;
              }
              
              $response = '';
              @fputs($fp, $request);
              
              while (!@feof($fp)) {
                  $response .= @fgets($fp, 4096);
              }
              
              @fclose($fp);
              
              list($response_headers, $contents) = explode("\r\n\r\n", $response, 2);
              
              $matches = null;
              
              if (preg_match('~^HTTP/1.[01] (\d+)~', $response_headers, $matches)) {
                  $status = (int) $matches[1];
              }
          }
      }

      if (!$check_status || $status == 200) {
          $body = $contents;
      }
	  
	  
	  $response = array(
		'code'			=>	$status,
		'body'			=>	$body
	  );
	  
      
      return $response;
    }
  
    function http_get($url, $auth = '', $check_status = true)
    {
        return $this->http_request('GET', $url, null, $auth, $check_status);
    }

	    
    
    public static function Get_SQL_Params($var_name_arr = array())
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'rtseo_analytics';
        
        $ppbv_table = $wpdb->get_results("SHOW TABLES LIKE '".$table_name."'" , ARRAY_N);
        if(!isset($ppbv_table[0])) return false;
        
        if (count($var_name_arr) > 0) 
        {
            foreach ($var_name_arr as $k => $v) 
            {
                $var_name_arr[$k] = "'".$v."'";
            }
            $sql_where = "WHERE var_name IN (".implode(",", $var_name_arr).")";
        }
        else $sql_where = '';
        $rows = $wpdb->get_results( 
        	"
        	SELECT *
        	FROM ".$table_name."
        	".$sql_where
        );
        
        $a = array();
        if (count($rows))
        {
            foreach ( $rows as $row ) 
            {
            	$a[trim($row->var_name)] = trim($row->var_value);
            }
        }
    
        return $a;
    }
    
    
    
    public static function Check_IP_in_list($ip, $ip_list = '')
    {
        if ($ip_list == '') return false;   // IP is not in the list
        
        $ip_list = str_replace(array(".*.*.*", ".*.*", ".*"), ".", trim($ip_list));
        $ip_list = explode("\n", $ip_list);
        if (count($ip_list))
        {
            foreach ($ip_list as $rule_ip)
            {
                if (strpos($ip, $rule_ip) === 0) 
                {
                    // match
                    return true;    // IP is in the list
                }
            }
        }
        
        return  false;   // IP is not in the list
    }

    
    public static function Set_SQL_Params($data = array())
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'rtseo_analytics';
    
        if (count($data) == 0) return;   
        
        foreach ($data as $k => $v)
        {
            $tmp = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE var_name = %s LIMIT 1;', $k ) );
            
            if ($tmp == 0)
            {
                // Insert    
                $wpdb->insert( $table_name, array( 'var_name' => $k, 'var_value' => $v ) ); 
            }
            else {
                // Update
                $data = array('var_value'=>$v);
                $where = array('var_name' => $k);
                $wpdb->update( $table_name, $data, $where );
            }
        } 
    }
    

	public function Print_MessageBox($data)
	{

       
       if (isset($data['type']))
       {
            switch ($data['type'])
            {
                case 'error':
                    $data['color'] = 'white';
                    $data['icon'] = 'exclamation triangle red';
                    break;
                    
                case 'info':
                    $data['color'] = 'blue';
                    $data['icon'] = 'exclamation';
                    break;
                    
                case 'ok':
                    $data['color'] = 'white';
                    $data['icon'] = 'check square outline green';
                    break;
                    
                case 'warning':
                    $data['color'] = 'yellow';
                    $data['icon'] = 'exclamation circle';
                    break;
            }
       }
       
	   if (!isset($data['size'])) $data['size'] = 'large';
	   if (isset($data['icon'])) 
       {
            $data['icon_class'] = 'icon';
            $data['icon_html'] = '<i class="'.$data['icon'].' icon"></i>';
       }
       else $data['icon_class'] = $data['icon_html'] = '';
       
       if (isset($data['button']) && !isset($data['button']['target'])) $data['button']['target'] = 1;

	   ?>
            <div class="ui <?php echo $data['color']; ?> <?php echo $data['icon_class']; ?> <?php echo $data['size']; ?> message">
                <?php echo $data['icon_html']; ?>
                <div class="content">
                  <?php if (isset($data['header'])) echo '<div class="header">'.$data['header'].'</div>'; ?>
                  <?php if (isset($data['button'])) { ?> <a class="mini ui <?php echo $data['color']; ?> button right floated" <?php if ($data['button']['target'] == 1) echo 'target="_blank"'; ?> href="<?php echo $data['button']['url']; ?>"><?php echo $data['button']['txt']; ?></a> <?php } ?>
                  <?php echo $data['content']; ?>
                </div>
            </div>
        <?php
    }
    
     
	public static function PrepareDomain($domain)
	{
	    $host_info = parse_url($domain);
	    if ($host_info == NULL) return false;
	    $domain = $host_info['host'];
	    if ($domain[0] == "w" && $domain[1] == "w" && $domain[2] == "w" && $domain[3] == ".") $domain = str_replace("www.", "", $domain);
	    //$domain = str_replace("www.", "", $domain);
	    
	    return $domain;
	}


    
    public static function PrintIconMessage($data)
    {
        $rand_id = "id_".rand(1,10000).'_'.rand(1,10000);
        if ($data['type'] == '' || $data['type'] == 'alert') {$type_message = 'negative'; $icon = 'warning sign';}
        if ($data['type'] == 'ok') {$type_message = 'green'; $icon = 'checkmark box';}
        if ($data['type'] == 'info') {$type_message = 'yellow'; $icon = 'info';}
        if ($data['type'] == 'info_white') {$type_message = ''; $icon = 'info';}
        ?>
        <div class="ui mini icon <?php echo $type_message; ?> message">
            <i class="<?php echo $icon; ?> icon"></i>
            <div class="msg_block_row">
                <?php
                if ((isset($data['button_text']) && $data['button_text'] != '') || (isset($data['help_text']) && $data['help_text'] != '')) {
                ?>
                <div class="msg_block_txt">
                    <?php
                    if ($data['header'] != '') {
                    ?>
                    <div class="header"><?php echo $data['header']; ?></div>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['message'] != '') {
                    ?>
                    <p><?php echo $data['message']; ?></p>
                    <?php
                    }
                    ?>
                </div>
                <div class="msg_block_btn">
                    <?php
                    if ($data['help_text'] != '') {
                    ?>
                    <a class="link_info" href="javascript:;" onclick="InfoBlock('<?php echo $rand_id; ?>');"><i class="help circle icon"></i></a>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['button_text'] != '') {
                        if (!isset($data['button_url_target']) || $data['button_url_target'] == true) $new_window = 'target="_blank"';
                        else $new_window = '';
                    ?>
                    <a class="mini ui green button" <?php echo $new_window; ?> href="<?php echo $data['button_url']; ?>"><?php echo $data['button_text']; ?></a>
                    <?php
                    }
                    ?>
                </div>
                    <?php
                    if ($data['help_text'] != '') {
                    ?>
                        <div style="clear: both;"></div>
                        <div id="<?php echo $rand_id; ?>" style="display: none;">
                            <div class="ui divider"></div>
                            <p><?php echo $data['help_text']; ?></p>
                        </div>
                    <?php
                    }
                    ?>
                <?php
                } else {
                ?>
                    <?php
                    if (isset($data['header']) && $data['header'] != '') {
                    ?>
                    <div class="header"><?php echo $data['header']; ?></div>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['message'] != '') {
                    ?>
                    <p><?php echo $data['message']; ?></p>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>
            </div> 
        </div>
        <?php
    }
    
	public function getApiKeyFromFile() {
	    $keyArray = file($this->apiKeyFile);
		return $keyArray[0];
	}
	
    public function checkCore($key) {
		

		if (!is_dir($this->webanalyzeFolder)) {
			wp_mkdir_p($this->webanalyzeFolder);
		}	
		
		if (!is_file($this->indexFile)) {
			file_put_contents($this->indexFile, '<html><body bgcolor="#FFFFFF"></body></html>');
		}
		
		$key = ($key) ? $key : md5('create_time' . get_site_url());
			
		if (!is_file($this->apiKeyFile)) {
			file_put_contents($this->apiKeyFile, md5($key));
		}
		
	}	

	public function checkLicenceInfo($key, $cache = true) {
		
		$key = ($key) ? $key : md5('create_time' . get_site_url());	
		
		if (!$cache) {
			@unlink($this->apiKeyFile);
			$this->checkCore($key);
		}
		global $rtsp_options;


		$params = array(
						'action' => 'licenseinfo',
						'domain' => get_site_url(),
						'email' => get_option( 'admin_email' ),
						'apikey' => $key,
						);
		$licFile = str_replace("\\", "/", str_replace("classes", "", dirname(__FILE__) . '/tmp/license_info.dat'));
		if (!is_file($licFile) || ((time() - filemtime($licFile)) > (60 * 60 * 24)) || ($cache == false)) {
			$response = $this->http_request("POST", $this->apiUrl, $params);
			if ($response['code'] === 200) {
				$licInfo = json_decode($response['body'], true);
				if ($licInfo['status'] === 'ok') {
					if ($key !== $licInfo['license']['apikey']) {
						$rtsp_options['rtseo_website_api_key'] = $licInfo['license']['apikey'];
						update_option('rtseop_options', $rtsp_options);
					}
					if ($licInfo['license']['apikey'] !== $this->getApiKeyFromFile()) {

						file_put_contents($this->apiKeyFile, $licInfo['license']['apikey']);
					}
					RT_SEO_Validation::writeLicenceInfo($licInfo['license']);
					$this->licenceInfo = $licInfo;
				}
			}
		}
		
	}
	
	public static function checkFileInCache($file) {
		$file = RT_SEO_TMP_PATH . $file;
		if (!is_file($file) || ((time() - filemtime($file)) > (60 * 60 * 24))) {
			@unlink($file);
			return false;
		}
		
		return file_get_contents($file);
	}
	
	
}
$RT_SEO_Helper = new RT_SEO_Helper();
?>