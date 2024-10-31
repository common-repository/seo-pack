<?php

class RT_SEO_Tfa
{
	
	protected $_codeLength = 6;
	static $instance; 
	protected $ip;
	protected $whiteListed;


	public function __construct() {
		$paramsArr = RT_SEO_Helper::Get_SQL_Params(array('rt_seo_enable_2fa'));
		$enabled = isset($params['rt_seo_enable_2fa']) ? $paramsArr['rt_seo_enable_2fa'] : '';
		if (!$enabled) return;

		self::$instance = $this;
		$this->ip = $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '';
		add_action( 'init', array( $this, 'init' ) );
		$whitelistArr = RT_SEO_Helper::Get_SQL_Params(array('rt_seo_tfa_ip_list_allow'));
		$this->whiteListed = RT_SEO_Helper::Check_IP_in_list($this->ip, $whitelist['rt_seo_tfa_ip_list_allow']);
	}

	
	public function init() {
    
		add_action( 'login_form', array( $this, 'addToLogin' ) );
		add_action( 'login_footer', array( $this, 'addToFooter' ) );
		add_filter( 'authenticate', array( $this, 'ifRT_SEO_GTFAenabled' ), 50, 3 );
		add_action( 'personal_options_update', array( $this, 'personal_options_update' ) );
		add_action( 'profile_personal_options', array( $this, 'profile_personal_options' ) );
		add_action('admin_enqueue_scripts', array($this, 'add_qrcode_script'));
		
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'wp_ajax_two_factor_authentication_action', array( $this, 'ajaxHandler' ) );
		}

	}
	
	public function add_qrcode_script() {
		wp_enqueue_script('jquery');
		wp_register_script('qrcode_script', plugins_url('qrcode.js', __FILE__),array("jquery"));
		wp_enqueue_script('qrcode_script');
	}	
	
	
	public function addToLogin() {
		if ($this->whiteListed) return;		
		?>
		<p>	
		<label title="<?php _e('If you don\'t have Website Admin Two-Factor Authentication enabled for your WordPress account, leave this field empty.','two-factor-authentication')?>"><?php _e('Two-Factor Authentication code','two-factor-authentication'); ?><span id="google-auth-info"></span><br />
		<input type="text" name="vCode" id="user_email" class="input" value="" size="20" style="ime-mode: inactive;" /></label>
		</p>
		<?php
	}


	public function addToFooter() {
		?>
		<script type="text/javascript">
			try{
				document.getElementById('user_email').setAttribute('autocomplete','off');
			} catch(e){}
		</script>
		<?php
	}
	
	
	public function ifRT_SEO_GTFAenabled( $user, $username = '', $password = '' ) {
		$userstate = $user;
		


		if ($this->whiteListed) return $userstate;	
			
		if ( get_user_by( 'email', $username ) === false ) {
			$user = get_user_by( 'login', $username );
		} else {
			$user = get_user_by( 'email', $username );
		}

		if ( isset( $user->ID ) && trim(get_user_option( 'two_factor_authentication_enabled', $user->ID ) ) == 'enabled' ) {
			$RT_SEO_GTFA_secret = trim( get_user_option( 'two_factor_authentication_secret', $user->ID ) );
			$RT_SEO_GTFA_delay = trim( get_user_option( 'two_factor_authentication_delay', $user->ID ) );
			if ( !empty( $_POST['vCode'] )) { 
				$vCode = trim( $_POST[ 'vCode' ] );
			} else {
				$vCode = '';
			}
			$lasttimeslot = trim( get_user_option( 'two_factor_authentication_lasttimeslot', $user->ID ) );
			if ( $timeslot = $this->verifyCode( $RT_SEO_GTFA_secret, $vCode, $RT_SEO_GTFA_delay, $lasttimeslot ) ) {
				update_user_option( $user->ID, 'two_factor_authentication_lasttimeslot', $timeslot, true );
				return $userstate;
			} else {
				return new WP_Error( 'invalid_two_factor_authentication_token', __( '<strong>ERROR</strong>: The Website Admin Two-Factor Authentication code is incorrect or has expired.', 'two-factor-authentication' ) );		
			}
		}
		return $userstate;
	}
	
		
	public function profile_personal_options() {
		global $user_id, $is_profile_page;
		
		$RT_SEO_GTFA_hidefromuser = trim( get_user_option( 'two_factor_authentication_hidefromuser', $user_id ) );
		if ( $RT_SEO_GTFA_hidefromuser == 'enabled') return;
		
		$RT_SEO_GTFA_secret			= trim( get_user_option( 'two_factor_authentication_secret', $user_id ) );
		$RT_SEO_GTFA_enabled			= trim( get_user_option( 'two_factor_authentication_enabled', $user_id ) );
		$RT_SEO_GTFA_delay		= trim( get_user_option( 'two_factor_authentication_delay', $user_id ) );

		if ( '' == $RT_SEO_GTFA_secret ) {
			$RT_SEO_GTFA_secret = $this->createSecret();
		}
		

		?>
		<a name="tfa"></a> 
		<br><br>
		<hr>
		<br><br>
		<h3><?php _e( 'Website Admin Two-Factor Authentication', 'two-factor-authentication' );?></h3>
		<table class="form-table">
		<tbody>
		<tr>
		<th scope="row"><?php _e( 'Enable', 'two-factor-authentication' ); ?></th>
		<td>
		<input name="RT_SEO_GTFA_enabled" id="RT_SEO_GTFA_enabled" class="tog" type="checkbox" <?php echo checked( $RT_SEO_GTFA_enabled, 'enabled', false ); ?>/><?php if ($this->whiteListed) : ?><span class="description" style="color:green;font-weight:bold;"> <?php _e( 'Your IP address is in the whitelist ', 'two-factor-authentication' ); ?></span>
		<?php endif; ?>
		</td>
		</tr>

		<?php if ( $is_profile_page || IS_PROFILE_PAGE ) : ?>
			<tr>
			<th scope="row"><?php _e( 'Simplified mode', 'two-factor-authentication' ); ?></th>
			<td>
			<input name="RT_SEO_GTFA_delay" id="RT_SEO_GTFA_delay" class="tog" type="checkbox" <?php echo checked( $RT_SEO_GTFA_delay, 'enabled', false ); ?>/><span class="description"><?php _e(' Simplified mode allows for more time drifting on your phone clock (&#177;2 min).','two-factor-authentication'); ?></span>
			</td>
			</tr>
			<tr>
			<th></th>
			<td>
			<img src="<?php echo plugins_url('../images/tfa.png', __FILE__); ?>"/>
			</td>
			</tr>
			<tr>
			<th><label for="RT_SEO_GTFA_secret"><?php _e('Secret','two-factor-authentication'); ?></label></th>
			<td>
			<input name="RT_SEO_GTFA_secret" style="text-align:center;" id="RT_SEO_GTFA_secret" class="regular-text" value="<?php echo $RT_SEO_GTFA_secret; ?>" readonly="readonly"  type="text" size="25" />
			<input name="RT_SEO_GTFA_newsecret" id="RT_SEO_GTFA_newsecret" value="<?php _e("Create new secret",'two-factor-authentication'); ?>"   type="button" class="button" />
			<input name="show_qr" id="show_qr" value="<?php _e("Show/Hide QR code",'two-factor-authentication'); ?>"   type="button" class="button" onclick="ShowOrHideQRCode();" />
			</td>
			</tr>	
			<tr>
			<th></th>
			<td><div id="RT_SEO_GTFA_QR_INFO" style="display:none;margin-left:3%;" >
			<div id="RT_SEO_GTFA_QRCODE" style=""></div>
			<span class="description"><br/> <?php _e( 'Scan this with the Google Authenticator app.', 'two-factor-authentication' ); ?></span>
			</div></td>
			</tr>

		<?php endif; ?>

		</tbody></table>
		<hr>
		<script type="text/javascript">
		var RT_SEO_GTFAnonce='<?php echo wp_create_nonce('two_factor_authenticationaction'); ?>';


		//Create new secret and display it
		jQuery('#RT_SEO_GTFA_newsecret').bind('click', function() {
			// Remove existing QRCode
			jQuery('#RT_SEO_GTFA_QRCODE').html("");
			var data=new Object();
			data['action']	= 'two_factor_authentication_action';
			data['nonce']	= RT_SEO_GTFAnonce;
			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#RT_SEO_GTFA_secret').val(response['new-secret']);
				var qrcode="otpauth://totp/<?php echo urlencode(get_site_url());?>:<?php echo urlencode(wp_get_current_user()->user_login);?>?secret="+jQuery('#RT_SEO_GTFA_secret').val()+"&issuer=<?php echo urlencode(get_site_url());?>";
				jQuery('#RT_SEO_GTFA_QRCODE').qrcode(qrcode);
				jQuery('#RT_SEO_GTFA_QR_INFO').show('slow');
			});  	
		});

		// If the user starts modifying the description, hide the qrcode
		jQuery('#RT_SEO_GTFA_description').bind('focus blur change keyup', function() {
			// Only remove QR Code if it's visible
			if (jQuery('#RT_SEO_GTFA_QR_INFO').is(':visible')) {
				jQuery('#RT_SEO_GTFA_QR_INFO').hide('slow');
				jQuery('#RT_SEO_GTFA_QRCODE').html("");
			}
		});


		function ShowOrHideQRCode() {
			if (jQuery('#RT_SEO_GTFA_QR_INFO').is(':hidden')) {
				var qrcode="otpauth://totp/<?php echo urlencode(get_site_url());?>:<?php echo urlencode(wp_get_current_user()->user_login);?>?secret="+jQuery('#RT_SEO_GTFA_secret').val()+"&issuer=<?php echo urlencode(get_site_url());?>";
				jQuery('#RT_SEO_GTFA_QRCODE').qrcode(qrcode);
				jQuery('#RT_SEO_GTFA_QR_INFO').show('slow');
			} else {
				jQuery('#RT_SEO_GTFA_QR_INFO').hide('slow');
				jQuery('#RT_SEO_GTFA_QRCODE').html("");
			}
		}
		
		jQuery( document ).ready(function() {
			ShowOrHideQRCode();
		});
	</script>
	<?php
	}	
	
	
	public function personal_options_update() {

		global $user_id;
		$RT_SEO_GTFA_hidefromuser = trim( get_user_option( 'two_factor_authentication_hidefromuser', $user_id ) );
		if ( $RT_SEO_GTFA_hidefromuser == 'enabled') return;
		$RT_SEO_GTFA_enabled	= ! empty( $_POST['RT_SEO_GTFA_enabled'] );
		$RT_SEO_GTFA_delay	= ! empty( $_POST['RT_SEO_GTFA_delay'] );
		$RT_SEO_GTFA_secret	= trim( $_POST['RT_SEO_GTFA_secret'] );	
		if ( ! $RT_SEO_GTFA_enabled ) {
			$RT_SEO_GTFA_enabled = 'disabled';
		} else {
			$RT_SEO_GTFA_enabled = 'enabled';
		}
		if ( ! $RT_SEO_GTFA_delay ) {
			$RT_SEO_GTFA_delay = 'disabled';
		} else {
			$RT_SEO_GTFA_delay = 'enabled';
		}
		update_user_option( $user_id, 'two_factor_authentication_enabled', $RT_SEO_GTFA_enabled, true );
		update_user_option( $user_id, 'two_factor_authentication_delay', $RT_SEO_GTFA_delay, true );
		update_user_option( $user_id, 'two_factor_authentication_secret', $RT_SEO_GTFA_secret, true );
	}

    public function createSecret($secretLength = 16)
    {
        $validChars = $this->_getBase32LookupTable();
        if ($secretLength < 16 || $secretLength > 128) {
            throw new Exception('Bad secret length');
        }
        $secret = '';
        $rnd = false;
        if (function_exists('random_bytes')) {
            $rnd = random_bytes($secretLength);
        } elseif (function_exists('mcrypt_create_iv')) {
            $rnd = mcrypt_create_iv($secretLength, MCRYPT_DEV_URANDOM);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $rnd = openssl_random_pseudo_bytes($secretLength, $cryptoStrong);
            if (!$cryptoStrong) {
                $rnd = false;
            }
        }
        if ($rnd !== false) {
            for ($i = 0; $i < $secretLength; ++$i) {
                $secret .= $validChars[ord($rnd[$i]) & 31];
            }
        } else {
            throw new Exception('No source of secure random');
        }

        return $secret;
    }


    public function getCode($secret, $timeSlice = null)
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }
        $secretkey = $this->_base32Decode($secret);

        // Pack time into binary string
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        // Hash it with users secret key
        $hm = hash_hmac('SHA1', $time, $secretkey, true);
        // Use last nipple of result as index/offset
        $offset = ord(substr($hm, -1)) & 0x0F;
        // grab 4 bytes of the result
        $hashpart = substr($hm, $offset, 4);

        // Unpak binary value
        $value = unpack('N', $hashpart);
        $value = $value[1];
        // Only 32 bits
        $value = $value & 0x7FFFFFFF;

        $modulo = pow(10, $this->_codeLength);

        return str_pad($value % $modulo, $this->_codeLength, '0', STR_PAD_LEFT);
    }

	
	public function verifyCode( $secretkey, $thistry, $relaxedmode, $lasttimeslot ) {
		if ( strlen( $thistry ) != 6) {
			return false;
		} else {
			$thistry = intval ( $thistry );
		}
		if ( $relaxedmode == 'enabled' ) {
			$firstcount = -4;
			$lastcount  =  4; 
		} else {
			$firstcount = -1;
			$lastcount  =  1; 	
		}	
		$tm = floor( time() / 30 );	
		$secretkey=$this->_base32Decode($secretkey);
		for ($i=$firstcount; $i<=$lastcount; $i++) {

			$time=chr(0).chr(0).chr(0).chr(0).pack('N*',$tm+$i);
			$hm = hash_hmac( 'SHA1', $time, $secretkey, true );
			$offset = ord(substr($hm,-1)) & 0x0F;
			$hashpart=substr($hm,$offset,4);
			$value=unpack("N",$hashpart);
			$value=$value[1];

			$value = $value & 0x7FFFFFFF;
			$value = $value % 1000000;
			if ( $value === $thistry ) {
				if ( $lasttimeslot >= ($tm+$i) ) {
					error_log("Website Admin Two-Factor Authentication plugin: Man-in-the-middle attack detected (Could also be 2 legit login attempts within the same 30 second period)");
					return false;
				}
				return $tm+$i;
			}
		}
		return false;
	}


    protected function _base32Decode($secret)
    {
        if (empty($secret)) {
            return '';
        }
        $base32chars = $this->_getBase32LookupTable();
        $base32charsFlipped = array_flip($base32chars);

        $paddingCharCount = substr_count($secret, $base32chars[32]);
        $allowedValues = array(6, 4, 3, 1, 0);
        if (!in_array($paddingCharCount, $allowedValues)) {
            return false;
        }
        for ($i = 0; $i < 4; ++$i) {
            if ($paddingCharCount == $allowedValues[$i] &&
                substr($secret, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i])) {
                return false;
            }
        }
        $secret = str_replace('=', '', $secret);
        $secret = str_split($secret);
        $binaryString = '';
        for ($i = 0; $i < count($secret); $i = $i + 8) {
            $x = '';
            if (!in_array($secret[$i], $base32chars)) {
                return false;
            }
            for ($j = 0; $j < 8; ++$j) {
                $x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for ($z = 0; $z < count($eightBits); ++$z) {
                $binaryString .= (($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48) ? $y : '';
            }
        }

        return $binaryString;
    }


    protected function _getBase32LookupTable()
    {
        return array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
            '=',  
        );
    }

	
	public function ajaxHandler() {
		global $user_id;
		check_ajax_referer( 'two_factor_authenticationaction', 'nonce' );
		$secret = $this->createSecret();
		$result = array( 'new-secret' => $secret );
		header( 'Content-Type: application/json' );
		echo json_encode( $result );
		die(); 
	}
	
}

$RT_SEO_Tfa = new RT_SEO_Tfa;
