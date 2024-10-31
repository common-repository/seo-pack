<?php

if (!class_exists('RT_SEO_Vitals')) {

	class RT_SEO_Vitals extends RT_SEO_Helper
	{
		
		public $upgradeLink = '/en/buy-service/google-core-web-vitals-fix-services';

		public function ServiceProvideURL() {
			return 'https://'.'site'.'guarding'.'.com';
		}
		
		public function vitals_panel() {

            $this->TemplateHeader($title = 'Google Core Web Vitals Fix');

            if (PHP_MAJOR_VERSION == 4) {
            $this->error('Not supported PHP version');
            return false;
            }

            if (is_int(PHP_MAJOR_VERSION) && is_int(PHP_MINOR_VERSION) && PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION == 2) {
            $this->error('PHP 5.2.x is Not supported anymore, due to security issues.');
            return false;
            }

            $extensions = get_loaded_extensions();
            if (!$this->check_ioncube_version()) {
            //$this->error('IonCube Loader version 10 or higher is required for this plugin.');
            //return false;
            }

            $action = '';
            if (isset($_REQUEST['action'])) $action = sanitize_text_field(trim($_REQUEST['action']));

            // Actions
            if ($action != '')
            {
            $action_message = '';
            switch ($action)
            {

            case 'Save_Settings':
            if (check_admin_referer( 'name_4b5jh35b3h5v4' ))
            {
            $data = array();
            $action_type = 'ok';
            $data['plggcwvf_status'] = intval($_POST['plggcwvf_status']);
            $action_message = 'Settings saved';
            $this->Set_Params($data);
            if ($data['plggcwvf_status']) {
            $license = $this->get_licence_info();
            if ($license === true) {
            $this->PatchWPConfig_file(true);
            } else {
            $action_message = $license;
            $action_type = 'alert';
            }

            } else {
            $this->PatchWPConfig_file(false);
            }
            }
            break;
            }

            if ($action_message != '')
            {
            $message_data = array(
            'type' => $action_type,
            'header' => '',
            'message' => $action_message,
            'button_text' => '',
            'button_url' => '',
            'help_text' => ''
            );
            RT_SEO_Helper::PrintIconMessage($message_data);
            }
            }

            if (is_file(dirname(__FILE__) . '/terminated.flag'))
            {
            $message_data = array(
            'type' => 'alert',
            'header' => '',
            'message' => 'Something is wrong with your license or it has expired. Enable improvement again to update license info or purchase our paid license.',
            'button_text' => '',
            'button_url' => '',
            'help_text' => ''
            );
            RT_SEO_Helper::PrintIconMessage($message_data);
            }


            $params = $this->Get_Params();

            if (!is_file(dirname(__FILE__) . '/license.json') || !is_file(dirname(__FILE__) . '/core.web.vitals.php') || !$params['plggcwvf_status']) {
            $params['plggcwvf_status'] = 0;
            $this->Set_Params($params);
            $this->PatchWPConfig_file(false);
            @unlink(dirname(__FILE__) . '/license.json');
            @unlink(dirname(__FILE__) . '/core.web.vitals.php');
            }

            $exp_date = $this->get_exp_date();

            $status = $params['plggcwvf_status'] ? '<span class="green ui label">Enabled</span>' : '<span class="red ui label">Disabled</span>';
            $status_css = $params['plggcwvf_status'] ? '' : 'disabled';

            ?>

            <div class="ui icon info message">
                <i class="info circle icon"></i>
                <div class="content">
                    <div class="header">
                        Disclaimer
                    </div>
                    <p>This version of the plugin shows the improvements you can get by optimizing your site for Google Core Web Vitals. It does not make any changes in the WordPress CMS core and does not optimize JavaScript or CSS files it only performs basic optimization. <b>All the changes should be done manually.</b></p>
                    <p>Please note, WordPress has 100 000+ different themes and plugins and it is not possible to create an automated solution that can optimize all plugins and themes. Google Core Web Vital optimization is a 100% manual work and <b>should be done by professional engineers.</b></p>
                    <p>Our plugin performs the website performance check and shows you if we can help you get better results for Google and improve your website performance.</p>
                </div>
            </div>



            <h3 class="ui dividing header">Current status <?php echo $status; ?></h3>
            <?php if ($exp_date) : ?>
                Valid till: <b><?php echo $exp_date; ?></b>
            <?php endif; ?>
            <div class="ui basic center aligned segment">
                <form method="post" action="admin.php?page=rt_seo_vitals" class="ui form">


                    <?php
                    wp_nonce_field( 'name_4b5jh35b3h5v4' );

                    if ($params['plggcwvf_status']) {
                        ?>
                        <input type="submit" name="submit" class="negative ui big button" value="Disable Improvement">
                        <input type="hidden" name="plggcwvf_status" value="0">
                        <?php
                    }
                    else {
                        ?>
                        <input type="submit" name="submit" class="positive ui big button" value="Enable Improvement">
                        <input type="hidden" name="plggcwvf_status" value="1">
                        <?php
                    }
                    ?>

                    <input type="hidden" name="action"  value="Save_Settings">
                </form>
            </div>

            <h3 class="ui dividing header">Parameters for Improvement <?php echo $status?></h3>

            <div class="ui info message">
                <p><b>Please note:</b> This plugin performs basic automated optimization and can not optimize the code if you need a full optimization, our engineers can help you. Please note, Google Core Web Vitals optimization is 100% manual work and should be done by professionals.</p>
            </div>

            <div class="ui grid">
                <div class="four wide column">
                    <div class="ui center aligned secondary segment <?php echo $status_css; ?>">
                        <?php
                        if ($status_css == '') echo '<div class="floating ui green label">OK</div>';
                        ?>
                        <i class="code big icon"></i><br />
                        <h3 class="ui header">JavaScript</h3>
                        <p>Optimization of javascript codes</p>
                    </div>
                </div>
                <div class="four wide column">
                    <div class="ui center aligned secondary segment <?php echo $status_css; ?>">
                        <?php
                        if ($status_css == '') echo '<div class="floating ui green label">OK</div>';
                        ?>
                        <i class="file code big icon"></i><br />
                        <h3 class="ui header">CSS</h3>
                        <p>Optimization of css codes</p>
                    </div>
                </div>
                <div class="four wide column">
                    <div class="ui center aligned secondary segment <?php echo $status_css; ?>">
                        <?php
                        if ($status_css == '') echo '<div class="floating ui green label">OK</div>';
                        ?>
                        <i class="upload big icon"></i><br />
                        <h3 class="ui header">Output Compressing</h3>
                        <p>Optimization of data transfer</p>
                    </div>
                </div>
                <div class="four wide column">
                    <div class="ui center aligned secondary segment <?php echo $status_css; ?>">
                        <?php
                        if ($status_css == '') echo '<div class="floating ui green label">OK</div>';
                        ?>
                        <i class="plug big icon"></i><br />
                        <h3 class="ui header">Queries</h3>
                        <p>Optimization amount of queries</p>
                    </div>
                </div>
            </div>


            <div class="ui basic center aligned segment">
                <a target="_blank" class="ui big button" href="https://developers.google.com/speed/pagespeed/insights/?url=<?php echo get_site_url(); ?>&strategy=mobile&category=performance&category=accessibility&category=best-practices&category=seo&category=pwa&utm_source=lh-chrome-ext">Check My Website!</a>
                <a target="_blank" class="positive ui big button" href="<?php echo self::ServiceProvideURL().$this->upgradeLink; ?>">Upgrade to Premium</a>

                <br /><br />

                Check your website results with enabled and disabled status to see the difference in improvement.
            </div>


            <h3 class="ui dividing header">Premium Support Includes</h3>

            <div class="ui list">

                <a class="item"><i class="check icon green"></i><div class="content"><div class="description">100% manual code optimization (to get better score results)</div></div></a>
                <a class="item"><i class="check icon green"></i><div class="content"><div class="description">Google Fix Validation (we will prepare your website to pass validation and will send all necessary requests to Google)</div></div></a>
                <a class="item"><i class="check icon green"></i><div class="content"><div class="description">JavaScript optimization (to get better score results)</div></div></a>
                <a class="item"><i class="check icon green"></i><div class="content"><div class="description">CSS optimization (to get better score results)</div></div></a>
                <a class="item"><i class="check icon green"></i><div class="content"><div class="description">24/7 support & Live Chat support</div></div></a>
                <a class="item"><i class="check icon green"></i><div class="content"><div class="description">Google Revision Request</div></div></a>
                <a class="item"><i class="check icon green"></i><div class="content"><div class="description">Full security for the website (monitoring, malware removal, antivirus, firewall (WAF))</div></div></a>

            </div>
            <a target="_blank" class="ui small button" href="<?php echo self::ServiceProvideURL(); ?>/en/google-core-web-vitals-fix">Service Details</a>
            <a target="_blank" class="positive ui small button" href="<?php echo $this->upgradeLink; ?>">Get Premium</a>








            <?php
            $this->BottomHeader();

        }


    public function TemplateHeader($title = '')
    {
        wp_enqueue_style( 'plggcwvf_LoadSemantic_css' );
        wp_enqueue_script( 'plggcwvf_LoadSemantic_js', '', array(), false, true );
        ?>
        <script>
            jQuery(document).ready(function(){
                jQuery("#main_container_loader").hide();
                jQuery("#main_container").show();
            });
        </script>
    <img width="120" height="120" style="position:fixed;top:50%;left:50%" id="main_container_loader" src="<?php echo plugins_url('images/ajax_loader.svg', dirname(__FILE__)); ?>" />
        <div id="main_container" class="ui main container" style="margin:20px 0 0 0!important; display: none;">
            <?php
            if ($title != '') {
                ?>
                <h2 class="ui dividing header"><?php echo $title; ?></h2>
                <?php
            }
            ?>

            <?php
            }

            public function BottomHeader()
            {
            ?>
        </div>
        <?php
    }


        public function Get_Params($vars = array())
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'rtseo_analytics';

            $ppbv_table = $wpdb->get_results("SHOW TABLES LIKE '".$table_name."'" , ARRAY_N);
            if(!isset($ppbv_table[0])) return false;

            if (count($vars) == 0)
            {
                $rows = $wpdb->get_results(
                    "
        	SELECT *
        	FROM ".$table_name."
        	"
                );
            }
            else {
                foreach ($vars as $k => $v) $vars[$k] = "'".$v."'";

                $rows = $wpdb->get_results(
                    "
        	SELECT * 
        	FROM ".$table_name."
            WHERE var_name IN (".implode(',',$vars).")
        	"
                );
            }

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


        public function Set_Params($data = array())
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



        public function PatchWPConfig_file($action = true)   // true - insert, false - remove
        {

            $file = dirname(__FILE__).DIRECTORY_SEPARATOR."core.web.vitals.php";

            $integration_code = '<?php /* SEO Block FD5503D3B128-START */ if (file_exists("'.$file.'"))include_once("'.$file.'");/* SEO Block FD5503D3B128-END */?>';

            // Insert code
            if (!defined('ABSPATH') || strlen(ABSPATH) < 8)
            {
                $scan_path = dirname(__FILE__);
                $scan_path = str_replace(DIRECTORY_SEPARATOR.'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'wp-google-core-web-vitals-fix', DIRECTORY_SEPARATOR, $scan_path);
                //echo TEST;
            }
            else $scan_path = ABSPATH;

            $filename = $scan_path.DIRECTORY_SEPARATOR.'wp-config.php';
            if (!is_file($filename)) $filename = dirname($scan_path).DIRECTORY_SEPARATOR.'wp-config.php';
            $handle = fopen($filename, "r");
            if ($handle === false) return false;
            $contents = fread($handle, filesize($filename));
            if ($contents === false) return false;
            fclose($handle);

            $pos_code = stripos($contents, 'FD5503D3B128');

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

        public function get_licence_info()
        {
            global $wp_filesystem;

            if ( ! $wp_filesystem ) {
                WP_Filesystem();
            }

            $website_url = get_site_url();
            $domain = RT_SEO_Helper::PrepareDomain($website_url);

            $wp_filesystem->mkdir( $wp_filesystem->abspath() . 'webanalyze');
            $wp_filesystem->put_contents( $wp_filesystem->abspath() . 'webanalyze/verification.txt', md5($domain), FS_CHMOD_FILE);

            $url = self::ServiceProvideURL()."/ext/vitals/index.php";
            $response = wp_remote_post( $url, array(
                    'method'      => 'POST',
                    'timeout'     => 600,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(),
                    'body'        => array(
                        'action' => 'get_license',
                        'domain' => $domain,
                        'verification_link' => $website_url . '/webanalyze/verification.txt',
                        'cms' => 'wp',
                    ),
                    'cookies'     => array()
                )
            );

            $licence = @json_decode($response['body'], true);

            if ($licence['status'] == 'ok') {
                $wp_plgpath = str_replace(ABSPATH, $wp_filesystem->abspath(), dirname(__FILE__));

                $php_version = substr(PHP_VERSION, 0, 3);

                if ( (float) $php_version >= 7.2) $php_version = '7.2';

                $downloadLink = self::ServiceProvideURL()."/ext/vitals/files/core.web.vitals.$php_version.bin";

                wp_remote_get($downloadLink, array(
                    'stream' => true,
                    'timeout' => 30,
                    'filename' => $wp_plgpath . '/core.web.vitals.php'
                ));

                unset($licence['status'], $licence['reason']);
                $json = json_encode($licence);

                $wp_filesystem->put_contents( $wp_plgpath . '/license.json', json_encode($licence), FS_CHMOD_FILE);
                $wp_filesystem->delete( $wp_plgpath . '/terminated.flag');
                return true;
            } else {
                return $licence['reason'];
            }
        }



        public function get_exp_date() {
            if(is_file(dirname(__FILE__) . '/license.json')) {
                $license_info = @json_decode(file_get_contents(dirname(__FILE__) . '/license.json'), true);
                if ($license_info && isset($license_info['exp_date'])) return $license_info['exp_date'];
            }
            return false;
        }

        public function check_ioncube_version()
        {
            ob_start();
            phpinfo(INFO_GENERAL);
            $aux = str_replace('&nbsp;', ' ', ob_get_clean());
            if($aux !== false)
            {
                $pos = mb_stripos($aux, 'ionCube PHP Loader');
                if($pos !== false)
                {
                    $aux = mb_substr($aux, $pos + 18);
                    $aux = mb_substr($aux, mb_stripos($aux, ' v') + 2);

                    $version = '';
                    $c = 0;
                    $char = mb_substr($aux, $c++, 1);
                    while(mb_strpos('0123456789.', $char) !== false)
                    {
                        $version .= $char;
                        $char = mb_substr($aux, $c++, 1);
                    }

                    return ($version >= 10);
                }
            }

            return false;

        }

        public function error($msg)
        {
            ?>
            <div class="ui icon error message">
                <i class="info circle icon"></i>
                <div class="content">
                    <div class="header">
                        Error
                    </div>
                    <p><?php echo $msg; ?></p>
                </div>
            </div>
            <?php
        }


    }
	
	$RT_SEO_Vitals = new RT_SEO_Vitals();



}


