<?php

if (!class_exists('RT_SEO_Pack')) {

class RT_SEO_Pack extends RT_SEO_Helper
{
    var $maximum_description_length_yellow = 150;
    var $minimum_title_length = 10;
    var $maximum_title_length = 80;
    var $minimum_keywords_length = 100;
    var $maximum_keywords_length = 255;
    var $minimum_description_length = 20;
    var $maximum_description_length = 200;
    var $maximum_url_length = 80;
    var $idEmptyPostName = null;
    var $strTitleForReference = null;
	


  function __construct(){
    global $rtsp_options;

    if( is_admin() ) {
      parent::__construct();
      $this->plugin_slug = 'rt_seo_pack';
      global $rt_seo_pack_version;
      if( get_option('rt_seo_pack_version') != $rt_seo_pack_version ) {
        $this->activate();
      }
    } else {
      if( !isset($rtsp_options['rtseo_dont_use_desc_for_excerpt']) || !$rtsp_options['rtseo_dont_use_desc_for_excerpt'] ) {
        add_filter( 'get_the_excerpt', array( $this, 'description_for_excerpt' ) );
      }
      add_action( 'genesis_entry_content', array( $this, 'description_for_genesis_maybe' ) );
    }
  }

	public function isPostInstall() {
		if ( is_admin() && get_option( 'rtseo_do_install', false ) ) {
			delete_option( 'rtseo_do_install' );
			$this->goPostInstall();
			exit;
		}
	}

	public function goPostInstall() {
		if ( is_admin() ) {
			wp_redirect( admin_url() . '?page=rtseop_install&rtseop_install_nonce=' . wp_create_nonce( 'seop-install-nonce' ), 307 );
			exit;
		}
	}

  function activate() {
    global $rt_seo_pack_version;
    $rtsp_options = ( get_option('rtseop_options') ) ? get_option('rtseop_options') : array();
    if( !isset($rtsp_options['rtseo_shorten_slugs']) ) {
      update_option( $this->plugin_slug.'_deferred_notices', 'Realtime SEO will from now on automatically shorten your new post slugs to 3 most important keywords. You can disable this option in its <a href="'.$this->get_admin_page_url().'">Settings</a>.' );
    }
    global $rtsp_default_options;
    if( $rtsp_default_options === null ) return;

    $rtsp_options = array_merge( $rtsp_default_options, $rtsp_options );
    update_option( 'rtseop_options', $rtsp_options );

    update_option('rt_seo_pack_version', $rt_seo_pack_version);
  }




  function admin_init() {
    if( isset($_GET['page']) && $_GET['page'] == $this->plugin_slug ) {
      wp_enqueue_script('common');
      wp_enqueue_script('wp-lists');
      wp_enqueue_script('postbox');
    }
  }

  function description_for_excerpt( $excerpt ) {
    global $post;
    if( $description = get_post_meta( $post->ID, 'rtseo_description', true ) ) {
      if( strlen($description) > 0 ) {
        return $description;
      }
    }
    return $excerpt;
  }

  function description_for_genesis( $output ) {
    global $post;
    if( !is_singular() ) {
      if( stripos($post->post_content,'<!--more-->') === false ) {  //   If there is no read more tag it should show just the description.
        $description = trim( get_post_meta( $post->ID, 'rtseo_description', true ) );
        if( strlen($description) > 0 ) {
          return $description;
        } else if( isset($post->post_type) && ( $post == 'post' || $post == 'page' ) ) {
          remove_filter( 'the_content', array( $this, 'description_for_genesis' ) );
          $output = get_the_excerpt();
          add_filter( 'the_content', array( $this, 'description_for_genesis' ) );
        }

      } else {  //  In addition, no images from the posts should be shown only text and the featured image as now.
        if( stripos($output,'<h5') !== false ) $output = preg_replace( '~<h5.*?><a.*?><img.*?></a>[\s\S]*?</h5>~', '', $output );
        $output = preg_replace( '~<img.*?>~', '', $output );

      }

    }
    return $output;
  }


  function description_for_genesis_maybe() {
    if( !is_singular() ) {
      add_filter( 'the_content', array( $this, 'description_for_genesis' ) );
    }
  }

  function strtolower($str)
  {
    global $UTF8_TABLES;
    return strtr($str, $UTF8_TABLES['strtolower']);
  }

  function strtoupper($str)
  {
    global $UTF8_TABLES;
    return strtr($str, $UTF8_TABLES['strtoupper']);
  }

  function capitalize($s)
  {
    $s = trim($s);
    $tokens = explode(' ', $s);
    while (list($key, $val) = each($tokens)) {
            $tokens[$key] = trim($tokens[$key]);
            $tokens[$key] = strtoupper(substr($tokens[$key], 0, 1)) . substr($tokens[$key], 1);
    }
    $s = implode(' ', $tokens);
    return $s;

  }

  function curPageURL() {
   $pageURL = 'http';
   if ( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
   $pageURL .= "://";
   if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
   } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
   }
   return $pageURL;
  }

  function is_static_front_page()
  {
    global $wp_query;

    $post = $wp_query->get_queried_object();

    return get_option('show_on_front') == 'page' && is_page() && $post->ID == get_option('page_on_front');
  }

  function is_static_posts_page()
  {
    global $wp_query;

    $post = $wp_query->get_queried_object();
	
	if (is_object($post)) {
		return get_option('show_on_front') == 'page' && is_home() && $post->ID == get_option('page_for_posts');
	}
	return false;
  }

  function rtseop_mrt_exclude_this_page()
  {
    global $rtsp_options;

    $currenturl = trim(esc_url($_SERVER['REQUEST_URI'], '/'));

    if( isset($rtsp_options['rtseo_ex_pages']) ) {
      $excludedstuff = explode(',', $rtsp_options['rtseo_ex_pages']);
      foreach ($excludedstuff as $exedd)
      {
        $exedd = trim($exedd);

        if ($exedd)
        {
          if (stristr($currenturl, $exedd))
          {
            return true;
          }
        }
      }
    }

    return false;
  }

  function output_callback_for_title($content)
  {
    return $this->rewrite_title($content);
  }

  function internationalize($in, $key = '')
  {
    global $rtsp_options;

    if (function_exists('langswitch_filter_langs_with_message'))
    {
      $in = langswitch_filter_langs_with_message($in);
    }

    if (function_exists('polyglot_filter'))
    {
      $in = polyglot_filter($in);
    }

    if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage'))
    {
      $in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($in);
    }

    if (!empty($key) && function_exists('pll_default_language'))
    {
      $lang     = pll_current_language() ? pll_current_language() : pll_default_language();
      $lang_key = $key.'_'.$lang;
      if (!empty($rtsp_options[$lang_key])) {
        $in = $rtsp_options[$lang_key];
      }
    }

    $in = apply_filters('localization', $in);

    return $in;
  }

  function SortByLength( $strA, $strB ){
    return strlen( $strB ) - strlen( $strA );
  }


  function GeneratePostSlug( $strSlug, $idPost, $keywords = 3 ){
    global $wpdb;

    $aSlug = explode( '-', $strSlug );

    if( 3 >= count( $aSlug ) ) return $strSlug;
    if( 20 >= strlen( $strSlug ) ) return $strSlug;

    $aSlug = array_unique( $aSlug );
    $aSortSlug = $aSlug;
    usort( $aSortSlug, array( $this, 'SortByLength' ) );
    $aSortSlug = array_slice( $aSortSlug, 0, $keywords );

    $aSlug = array_intersect( $aSlug, $aSortSlug );
    $strSlugNew = implode( '-', $aSlug );

    if( $idPost ){
      $aPost = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = '".$wpdb->escape( $strSlugNew )."' AND `ID` != {$idPost} AND post_type != 'revision'" );
      $i = 0;

      if( count($aSortSlug) >= $keywords ) {
        if( $aPost ) {
          $strSlug = $this->GeneratePostSlug( $strSlug, $idPost, ++$keywords );
        } else {
          $strSlug = $strSlugNew;
        }
      } else {
        while( count( $aPosts ) ) {
          if( $i ) $strNewSlug = $strSlug . '-' . ($i+1);
          else $strNewSlug = $strSlug . '-1';

          $i++;
          $aPosts = $wpdb->get_results( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = '".$wpdb->escape( $strNewSlug )."' AND `ID` != {$idPost}" );
        }
        if( $strNewSlug ) $strSlug = $strNewSlug;
      }
    }

    return $strSlug;
  }

  function rtseo_unique_post_slug( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ){
    global $rtsp_options, $post;

    if( !isset($rtsp_options['rtseo_shorten_slugs']) || !$rtsp_options['rtseo_shorten_slugs'] )
      return $slug;


    if( null === $post ){

      if( !isset( $_POST['action'] ) || isset( $_POST['new_slug'] ) )
        return $slug;

      $status = get_post_status( $post_ID );

      if( ! in_array( $status, array( 'draft' ) ) )
        return $slug;
    }
    else{
      if( !empty( $_POST['post_name'] ) || !empty( $post->post_name ) )
        return $slug;
    }

    $slug = $this->GeneratePostSlug( $slug, $post_ID );
    return $slug;
  }


  function init()
  {
    load_plugin_textdomain('rt_seo', false, dirname(plugin_basename(__FILE__)) . "/languages");
	
	if (!is_dir(dirname(__FILE__) . DIRSEP . 'tmp')) {
		mkdir(dirname(__FILE__) . DIRSEP . 'tmp');
	}
	
	$newsFile = dirname(__FILE__) . DIRSEP . 'tmp' . DIRSEP . 'realtime_seo.json';
	if (!is_file($newsFile) || ((time() - filemtime($newsFile)) > (60 * 60 * 24))) {
		$response = $this->http_get('http://portal.seoguarding.com/api/realtime_seo.json');
		$data = $response['body'];
		file_put_contents($newsFile, $data);
	}
  }

  function remove_canonical() {
    if (is_single() || is_page() || $this->is_static_posts_page()) {
      global $wp_query, $rtsp_options;
      $post = $wp_query->get_queried_object();


    }
  }

  function template_redirect()
  {

    global $wp_query;
    global $rtsp_options;

    $post = $wp_query->get_queried_object();

    if( isset($rtsp_options['rtseo_attachments']) && $rtsp_options['rtseo_attachments'] ) {
      if( is_attachment() ) {
        global $post;
        $aImage = wp_get_attachment_image_src($post->ID, 'full');
        if( isset($aImage[0]) ) {
          wp_redirect($aImage[0],301);
          exit;
        }
      }
    }


    if( $wp_query->is_404 && isset($wp_query->query['paged']) && $wp_query->query['paged'] > 0 ) {

      $aArgs = $wp_query->query;
      unset($aArgs['paged']);
      $objCheckPaging = new WP_Query( $aArgs );

      global $wp_rewrite;

      $sLink = false;
      if( $objCheckPaging->is_year ) {
        $sLink = get_year_link( $aArgs['year'] );

      } else if( $objCheckPaging->is_month ) {
        $sLink = get_month_link( $aArgs['year'], intval($aArgs['monthnum']) );

      } else if( $objCheckPaging->is_day ) {
        $sLink = get_day_link( $aArgs['year'], $aArgs['monthnum'], $aArgs['day'] );

      } else if( $objCheckPaging->is_category ) {
        if( isset($wp_query->query['category_name']) ) {
          $objCat = get_category_by_path( $wp_query->query['category_name'] );
          $iCatId = $objCat->term_id;
        }
        if( isset($iCatId) ) {
          $sLink = get_category_link($iCatId);
        }

      } else if( $objCheckPaging->is_author ) {
        if( isset($wp_query->query['author_name']) ) {
          $objAuthor = get_user_by( 'slug', $wp_query->query['author_name'] );
          $iAuthorId = $objAuthor->ID;
        }
        if( isset($iAuthorId) ) {
          $sLink = get_author_posts_url($iAuthorId);
        }

      }

      if( $objCheckPaging->max_num_pages > 0 && $sLink ) {
        if( $objCheckPaging->max_num_pages > 1 ) {
          $sLink = user_trailingslashit( trailingslashit($sLink).'page/'.$objCheckPaging->max_num_pages );
        }
        wp_redirect($sLink,301);
        exit;
      }
    }



    if ($this->rtseop_mrt_exclude_this_page())
    {
      return;
    }

    if (is_feed())
    {
      return;
    }


    if ($rtsp_options['rtseo_rewrite_titles'] || ( is_object( $post ) && isset($post->ID) && get_post_meta($post->ID, "rtseo_title", true) ) || is_home() || $this->is_static_front_page() )
    {
      ob_start(array($this, 'output_callback_for_title'));
    }
  }


  function wp_head()
  {
    if (is_feed()) // ignore logic if it's a feed
    {
      return;
    }

    global $wp_query;
    global $rtsp_options;

    $post = $wp_query->get_queried_object();

        global $wp_rewrite;

        if($wp_rewrite->using_permalinks() && (is_category() || is_tag() || is_tax())){
            $taxonomy = $wp_query->tax_query->queries[0]["taxonomy"];
            $term = $wp_query->tax_query->queries[0]["terms"][0];

            $prev = "";
            $next = "";

            $page = 0;

            if(isset($wp_query->query["paged"]))
                $page = intval($wp_query->query["paged"]);

            $posts_per_page = $wp_query->query_vars["posts_per_page"];
            $found_posts = $wp_query->found_posts;
            $root = get_term_link($term,$taxonomy);


            if($page){

                if($page-1<2){
                    $prev = user_trailingslashit( trailingslashit($root) );
                }else{
                    $prev = user_trailingslashit( trailingslashit($root).'page/'.($page-1) );
                }

                if($found_posts>$posts_per_page*$page){
                    $next = user_trailingslashit( trailingslashit($root).'page/'.($page+1) );
                }
            } else {
                if($found_posts > $posts_per_page){
                    $next = user_trailingslashit( trailingslashit($root).'page/2/' );
                }
            }

            if($prev){
                echo "<link rel='prev' href='$prev' />";
            }

            if($next){
                echo "<link rel='next' href='$next' />";
            }
        }

    $meta_string = null;

    if ($this->is_static_posts_page())
    {
      $title = strip_tags(apply_filters('single_post_title', $post->post_title));
    }

    if (is_single() || is_page())
    {
      if( isset($meta_robots) && !empty($meta_robots) ) {
        $meta_string .= '<meta name="robots" content="'.implode(',',$meta_robots).'" />'."\n";
      }
    }

    if ($this->rtseop_mrt_exclude_this_page())
    {
      return;
    }


    if ($rtsp_options['rtseo_rewrite_titles']     || 1>0)
    {
      if (function_exists('ob_list_handlers'))
      {
        $active_handlers = ob_list_handlers();
      }
      else
      {
        $active_handlers = array();
      }

      if(
        sizeof($active_handlers) > 0 &&
        is_string($active_handlers[sizeof($active_handlers) - 1]) && strtolower($active_handlers[sizeof($active_handlers) - 1]) == strtolower('RT_SEO_Pack::output_callback_for_title') &&
        isset($active_handlers[sizeof($active_handlers) - 1][1]) && is_string($active_handlers[sizeof($active_handlers) - 1][1]) && $active_handlers[sizeof($active_handlers) - 1][1] == 'output_callback_for_title'
        )
      {
        ob_end_flush(); 
      }
      else
      {
        // something went wrong
      }
    }

    if ((is_home() && stripcslashes( $this->internationalize( $rtsp_options['rtseo_home_keywords'], 'rtseo_home_keywords' ) ) &&
      !$this->is_static_posts_page()) || $this->is_static_front_page())
    {
      $keywords = trim( stripcslashes( $this->internationalize($rtsp_options['rtseo_home_keywords'], 'rtseo_home_keywords') ) );
    }
    else
    {
      $keywords = $this->get_all_keywords();
    }

    if (is_single() || is_page() || $this->is_static_posts_page())
    {
      if ($this->is_static_front_page())
      {
        $description = trim(stripcslashes($this->internationalize($rtsp_options['rtseo_home_description'], 'rtseo_home_description')));
		if (!$description) {
			$description = $this->get_post_description($post);
			$description = apply_filters('rtseop_description', $description);
		}
      }
      else
      {
        $description = $this->get_post_description($post);
        $description = apply_filters('rtseop_description', $description);
      }
    }
    elseif (is_home())
    {
		$description = trim(stripcslashes($this->internationalize($rtsp_options['rtseo_home_description'], 'rtseo_home_description')));
		if (!$description) {
			$description = $this->get_post_description($post);
			$description = apply_filters('rtseop_description', $description);
		}
    }
    elseif (is_category())
    {
      $description = $this->internationalize(category_description());
    }

    if (isset($description) && !(is_home() && is_paged()))
    {
      $description = trim(strip_tags($description));
      $description = str_replace('"', '', $description);

      $description = str_replace("\r\n", ' ', $description);

      $description = str_replace("\n", ' ', $description);

      if (!isset($meta_string))
      {
        $meta_string = '';
      }

      $description_format = stripslashes( $rtsp_options['rtseo_description_format'] );

      if (!isset($description_format) || empty($description_format))
      {
        $description_format = "%description%";
      }

      $description = str_replace('%description%', $description, $description_format);
      $description = str_replace('%blog_title%', get_bloginfo('name'), $description);
      $description = str_replace('%blog_description%', get_bloginfo('description'), $description);
      $description = str_replace('%wp_title%', $this->get_original_title(), $description);
      $description = trim( str_replace('%page%', $this->paged_description(), $description) );
      $description = __( $description );

      if ($rtsp_options['rtseo_can'] && is_attachment())
      {
        $url = $this->rtseo_mrt_get_url($wp_query);

        if ($url)
        {
          preg_match_all('/(\d+)/', $url, $matches);

          if (is_array($matches))
          {
            $uniqueDesc = join('', $matches[0]);
          }
        }

        $description .= ' ' . $uniqueDesc;
      }

      $meta_string .= '<meta name="description" content="' . esc_attr($description) . '" />';
    }

    $keywords = apply_filters('rtseop_keywords', $keywords);

    if (isset($keywords) && !empty($keywords) && !(is_home() && is_paged()))
    {
      if (isset($meta_string))
      {
        $meta_string .= "\n";
      }

      $meta_string .= '<meta name="keywords" content="' . esc_attr($keywords) . '" />';
    }

    if (function_exists('is_tag'))
    {
      $is_tag = is_tag();
    }

    if ((is_category() && $rtsp_options['rtseo_category_noindex']) ||
      (!is_category() && is_archive() &&!$is_tag && $rtsp_options['rtseo_archive_noindex']) ||
      ($rtsp_options['rtseo_tags_noindex'] && $is_tag) ||
                        (is_search() && $rtsp_options['rtseo_search_noindex'])
                        )
    {
      if (isset($meta_string))
      {
        $meta_string .= "\n";
      }

      $meta_string .= '<meta name="robots" content="noindex,follow" />';
    }

    $page_meta = stripcslashes($rtsp_options['rtseo_page_meta_tags']);
    $post_meta = stripcslashes($rtsp_options['rtseo_post_meta_tags']);
    $home_meta = stripcslashes($rtsp_options['rtseo_home_meta_tags']);

    if (is_page() && isset($page_meta) && !empty($page_meta) || $this->is_static_posts_page())
    {
      if (isset($meta_string))
      {
        $meta_string .= "\n";
      }

      $meta_string .= $page_meta;
    }

    if (is_single() && isset($post_meta) && !empty($post_meta))
    {
      if (isset($meta_string))
      {
        $meta_string .= "\n";
      }

      $meta_string .= $post_meta;
    }

    if (is_home() && !empty($home_meta))
    {
      if (isset($meta_string))
      {
        $meta_string .= "\n";
      }

      $meta_string .= $home_meta;
    }

    $home_google_site_verification_meta_tag = isset( $rtsp_options['rtseo_home_google_site_verification_meta_tag'] ) ? stripcslashes($rtsp_options['rtseo_home_google_site_verification_meta_tag']) : NULL;
    $home_yahoo_site_verification_meta_tag = isset( $rtsp_options['rtseo_home_yahoo_site_verification_meta_tag'] ) ? stripcslashes($rtsp_options['rtseo_home_yahoo_site_verification_meta_tag']) : NULL;
    $home_bing_site_verification_meta_tag = isset( $rtsp_options['rtseo_home_bing_site_verification_meta_tag'] ) ? stripcslashes($rtsp_options['rtseo_home_bing_site_verification_meta_tag']) : NULL;

    if (is_home() && !empty($home_google_site_verification_meta_tag))
    {
      if (isset($meta_string))
      {
        $meta_string .= "\n";
      }

      $meta_string .= wp_kses($home_google_site_verification_meta_tag, array('meta' => array('name' => array(), 'content' => array())));
    }

    if (is_home() && !empty($home_yahoo_site_verification_meta_tag))
    {
      if (isset($meta_string))
      {
        $meta_string .= "\n";
      }

      $meta_string .= wp_kses($home_yahoo_site_verification_meta_tag, array('meta' => array('name' => array(), 'content' => array())));
    }

    if (is_home() && !empty($home_bing_site_verification_meta_tag))
    {
      if (isset($meta_string))
      {
        $meta_string .= "\n";
      }

      $meta_string .= wp_kses($home_bing_site_verification_meta_tag, array('meta' => array('name' => array(), 'content' => array())));
    }

    if ($meta_string != null)
    {
      echo wp_kses($meta_string, array('meta' => array('name' => array(), 'content' => array()))) . "\n";
    }

    if ($rtsp_options['rtseo_can'])
    {
        $url = $this->rtseo_mrt_get_url($wp_query);

         $url = apply_filters('rtseop_canonical_url', $url);
      if ($url)
      {
        echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";
      }
    }

	if ( !is_singular() ) return;
	
	$post_type = get_post_type();
	
	if ( is_singular() && $post_type && in_array( $post_type, apply_filters( 'noindex-pages-post-types', array( 'page' ) ) ) ) {
		$noindex = get_post_meta( get_the_ID(), 'rtseo_noindex', true );
		
		if ( (int) $noindex === 1 ) {
			echo '<meta name="robots" content="noindex" />' . "\n";
		}
	}
	
  }


  function hatom_microformat_replace() {
      global $rtsp_options;

      if( !isset($rtsp_options['rtseo_hentry']) || ( $rtsp_options['rtseo_hentry'] != '1' && strcmp($rtsp_options['rtseo_hentry'],'on') ) )
          ob_start(array($this,'hatom_microformat_callback'));
  }

  function hatom_microformat_callback($buffer) {

      $new_buffer = preg_replace( '~(class=["\'][^"\']*)hfeed\s?~', '$1', $buffer );
      $new_buffer = preg_replace( '~(class=["\'][^"\']*)vcard\s?~', '$1', $new_buffer );
      return $new_buffer;
  }




  function rtseo_mrt_get_url($query)
  {
    global $rtsp_options;

    if ($query->is_404 || $query->is_search)
    {
      return false;
    }

    $haspost = count($query->posts) > 0;
    $has_ut = function_exists('user_trailingslashit');

    if (get_query_var('m'))
    {
      $m = preg_replace('/[^0-9]/', '', get_query_var('m'));

      switch (strlen($m))
      {
      case 4:
        $link = get_year_link($m);
        break;
      case 6:
        $link = get_month_link(substr($m, 0, 4), substr($m, 4, 2));
        break;
      case 8:
        $link = get_day_link(substr($m, 0, 4), substr($m, 4, 2), substr($m, 6, 2));
        break;
      default:
        return false;
      }
    }
    elseif (($query->is_single || $query->is_page) && $haspost)
    {
      $post = $query->posts[0];
      $link = get_permalink($post->ID);
      $link = $this->yoast_get_paged($link);
    }
    elseif ($query->is_author && $haspost)
    {
      $author = get_userdata(get_query_var('author'));

      if ($author === false)
        return false;

      $link = get_author_link(false, $author->ID, $author->user_nicename);
    }
    elseif ($query->is_category && $haspost)
    {
      $link = get_category_link(get_query_var('cat'));
      $link = $this->yoast_get_paged($link);
    }
    elseif ($query->is_tag  && $haspost)
    {
      $tag = get_term_by('slug', get_query_var('tag'), 'post_tag');

      if (!empty($tag->term_id))
      {
        $link = get_tag_link($tag->term_id);
      }

      $link = $this->yoast_get_paged($link);
    }
    elseif ($query->is_day && $haspost)
    {
      $link = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
    }
    elseif ($query->is_month && $haspost)
    {
      $link = get_month_link(get_query_var('year'), get_query_var('monthnum'));
    }
    elseif ($query->is_year && $haspost)
    {
      $link = get_year_link(get_query_var('year'));
    }
    elseif ($query->is_home)
    {
      if ((get_option('show_on_front') == 'page') && ($pageid = get_option('page_for_posts')))
      {
        $link = get_permalink($pageid);
        $link = $this->yoast_get_paged($link);
        $link = trailingslashit($link);
      }
      else
      {
        $link = get_option('home');
        $link = $this->yoast_get_paged($link);
        $link = trailingslashit($link);
      }
    }
    else
    {
      return false;
    }

    return $link;
  }

  function yoast_get_paged($link)
  {
    $page = get_query_var('paged');

    if ($page && $page > 1)
    {
      $link = trailingslashit($link) ."page/". "$page";

      if ( function_exists('user_trailingslashit') )
      {
        $link = user_trailingslashit($link, 'paged');
      }
      else
      {
        $link .= '/';
      }
    }

    return $link;
  }


  function paged_description($description = NULL)
  {
    global $paged;
    global $rtsp_options;
    global $STagging;

    if( is_paged() )
    {
      $part = $this->internationalize( $rtsp_options['rtseo_paged_format'] );

      if( isset($part) || !empty($part) )
      {
        $part = trim($part);
        $part = str_replace('%page%', $paged, $part);
        $description .= $part;
      }
    }

    return $description;
  }


  function get_post_description($post)
  {
    global $rtsp_options;

    $description = trim(stripcslashes($this->internationalize(get_post_meta($post->ID, "rtseo_description", true))));

    if (!$description)
    {
      if(!$rtsp_options['rtseo_dont_use_excerpt']) {
        $description = $this->trim_excerpt_without_filters_full_length($this->internationalize($post->post_excerpt));
      }

      if (!$description && $rtsp_options["rtseo_generate_descriptions"])
      {
        $description = $this->trim_excerpt_without_filters($this->internationalize($post->post_content));
      }
    }

    $description = preg_replace("/\s\s+/", " ", $description);

    return $description;
  }

  function replace_title($content, $title)
  {
        $title = strip_tags(__($title));
    return preg_replace('/<title>(.*?)<\/title>/ms', '<title>' . esc_html($title) . '</title>', $content, 1);
  }

  function get_original_title()
  {
    global $wp_query;
    global $rtsp_options;

    if (!$wp_query)
    {
      return null;
    }

    $post = $wp_query->get_queried_object();

    global $s;

    $title = null;

    if (is_home())
    {
      $title = get_option('blogname');
    }
    elseif (is_single())
    {
      $title = $this->internationalize( get_the_title($post->ID) );
    }
    elseif (is_search() && isset($s) && !empty($s))
    {
      if (function_exists('attribute_escape'))
      {
        $search = attribute_escape(stripcslashes($s));
      }
      else
      {
        $search = wp_specialchars(stripcslashes($s), true);
      }

      $search = $this->capitalize($search);
      $title = $search;
    }
    elseif (is_category() && !is_feed())
    {
      $category_description = $this->internationalize(category_description());
      $category_name = ucwords($this->internationalize(single_cat_title('', false)));
      $title = $category_name;
    }
    elseif (is_page())
    {
      $title = $this->internationalize( get_the_title() );
    }
    elseif (function_exists('is_tag') && is_tag())
    {
      $tag = $this->internationalize(wp_title('', false));

      if ($tag)
      {
        $title = $tag;
      }
    }
    else if (is_archive())
    {
      $title = $this->internationalize(wp_title('', false));
    }
    else if (is_404())
    {
      $title_format = stripslashes( $rtsp_options['rtseo_404_title_format'] );

      $new_title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
      $new_title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $new_title);
      $new_title = str_replace('%request_url%', esc_url($_SERVER['REQUEST_URI']), $new_title);
      $new_title = str_replace('%request_words%', $this->request_as_words(esc_url($_SERVER['REQUEST_URI'])), $new_title);

      $title = $new_title;
    }

    return trim($title);
  }

  function paged_title($title)
  {
    global $paged;
    global $rtsp_options;
    global $STagging;

    if (is_paged() || (isset($STagging) && $STagging->is_tag_view() && $paged))
    {
      $part = stripslashes( $this->internationalize($rtsp_options['rtseo_paged_format']) );

      if (isset($part) || !empty($part))
      {
        $part = " " . trim($part);
        $part = str_replace('%page%', $paged, $part);
        $title .= $part;
      }
    }

    return $title;
  }

  function is_custom_post_type( $post = NULL )
  {
      $all_custom_post_types = get_post_types( array ( '_builtin' => FALSE ) );

      if ( empty ( $all_custom_post_types ) )
          return FALSE;

      $custom_types      = array_keys( $all_custom_post_types );
      $current_post_type = get_post_type( $post );

      if ( ! $current_post_type )
          return FALSE;

      return in_array( $current_post_type, $custom_types );
  }

  function rewrite_title($header)
  {
    global $rtsp_options;
    global $wp_query;

    if (!$wp_query)
    {
      return $header;
    }

    $post = $wp_query->get_queried_object();
    global $s;

    global $STagging;

    if (is_home() && !$this->is_static_posts_page() && stripcslashes( $this->internationalize($rtsp_options['rtseo_home_title'], 'rtseo_home_title') ) != '' )
    {

      $title = stripcslashes( $this->internationalize( $rtsp_options['rtseo_home_title'], 'rtseo_home_title' ) );

      if (empty($title))
      {
        $title = $this->internationalize(get_option('blogname'));
      }

      $title = $this->paged_title($title);
      $header = $this->replace_title($header, $title);

    }
    else if (is_attachment() && $rtsp_options['rtseo_rewrite_titles'])
    {
      $title = get_the_title($post->post_parent).' '.$post->post_title.' – '.get_option('blogname');
      $header = $this->replace_title($header,$title);
    }
    else if (is_single())
    {
      $authordata = get_userdata($post->post_author);
      $title = $this->internationalize(get_post_meta($post->ID, "rtseo_title", true));

      $post_type_obj = get_post_type_object( get_post_type( $post->ID ) );
      $post_type_name = $post_type_obj->labels->name;

      if (!$title)
      {
        $title = $this->internationalize(get_post_meta($post->ID, "title_tag", true));

        if (!$title)
        {
          $title = $this->internationalize( /*wp_title('', false)*/ get_the_title() );
        }
      }

      $category = '';

      if( $rtsp_options['rtseo_rewrite_titles'] ) {

        if( !is_singular( array('post')) ) {
          $taxonomies = get_post_taxonomies( $post->ID );
          foreach( $taxonomies as $taxonomy_name ) {
            if( in_array( $taxonomy_name, array( 'category', 'post_tag', 'nav_menu' ) ) )
              continue;

            $terms = get_the_terms( $post->id, $taxonomy_name );
            if( !$terms )
              continue;

            foreach( $terms as $term ){
              $category = $term->name;
            }
            break;
          }

          $title_format = stripslashes( $rtsp_options['rtseo_custom_post_title_format'] );
        }
        else {
          $categories = get_the_category();

          if (count($categories) > 0)
            $category = $categories[0]->cat_name;

          $title_format = stripslashes( $rtsp_options['rtseo_post_title_format'] );
        }

        $new_title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
        $new_title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $new_title);
        $new_title = str_replace('%post_title%', $title, $new_title);
        $new_title = str_replace('%post_type_name%', $post_type_name, $new_title);
        $new_title = str_replace('%category%', $category, $new_title);
        $new_title = str_replace('%category_title%', $category, $new_title);
        $new_title = str_replace('%post_author_login%', $authordata->user_login, $new_title);
        $new_title = str_replace('%post_author_nicename%', $authordata->user_nicename, $new_title);
        $new_title = str_replace('%post_author_firstname%', ucwords($authordata->first_name), $new_title);
        $new_title = str_replace('%post_author_lastname%', ucwords($authordata->last_name), $new_title);
      }
      /// Addition
      else
          $new_title = $title;

      $title = $new_title;
      $title = trim($title);
      $title = apply_filters('rtseo_title_single',$title);

      $header = $this->replace_title($header, $title);
    }
    elseif (is_search() && isset($s) && !empty($s)      && $rtsp_options['rtseo_rewrite_titles'])
    {
      if (function_exists('attribute_escape'))
      {
        $search = attribute_escape(stripcslashes($s));
      }
      else
      {
        $search = wp_specialchars(stripcslashes($s), true);
      }

      $search = $this->capitalize($search);
      $title_format = stripslashes( $rtsp_options['rtseo_search_title_format'] );

      $title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
      $title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $title);
      $title = str_replace('%search%', $search, $title);

      $header = $this->replace_title($header, $title);
    }
    elseif (is_category() && !is_feed()     && $rtsp_options['rtseo_rewrite_titles'])
    {
      global $cat;
      $category_titles = get_option('rtseop_category_titles');
      $category_description = $this->internationalize(strip_tags(category_description()));

      if( $category_titles !== false && isset($cat) && intval($cat) && isset($category_titles[$cat]) && !empty($category_titles[$cat]) ){
        $title = $category_titles[$cat];
      }
      else{

        if($rtsp_options['rtseo_cap_cats'])
        {
          $category_name = ucwords($this->internationalize(single_cat_title('', false)));
        }
        else
        {
          $category_name = $this->internationalize(single_cat_title('', false));
        }

        $title_format = stripslashes( $rtsp_options['rtseo_category_title_format'] );
        $title = str_replace('%category_title%', $category_name, $title_format);
      }

      $title = str_replace('%category_description%', $category_description, $title);
      $title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title);
      $title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $title);
      $title = $this->paged_title($title);


      $header = $this->replace_title($header, $title);
    }

    elseif (is_page() || $this->is_static_front_page())

    {
      $authordata = get_userdata($post->post_author);

      if ($this->is_static_front_page())
      {

        if ( stripcslashes( $this->internationalize($rtsp_options['rtseo_home_title'], 'rtseo_home_title') ) )
        {
          $home_title = stripcslashes( $this->internationalize( $rtsp_options['rtseo_home_title'], 'rtseo_home_title' ) );
          $home_title = apply_filters('rtseop_home_page_title',$home_title);

          $header = $this->replace_title($header, $home_title);
        }
      }
      else
      {
        $title = $this->internationalize(get_post_meta($post->ID, "rtseo_title", true));

        if (!$title)
        {
          $title = $this->internationalize( /*wp_title('', false)*/ get_the_title($post->ID) );
        }

                                if( $rtsp_options['rtseo_rewrite_titles'] ) {

                                    $title_format = stripslashes( $rtsp_options['rtseo_page_title_format'] );

                                    $new_title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
                                    $new_title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $new_title);
                                    $new_title = str_replace('%page_title%', $title, $new_title);
                                    $new_title = str_replace('%page_author_login%', $authordata->user_login, $new_title);
                                    $new_title = str_replace('%page_author_nicename%', $authordata->user_nicename, $new_title);
                                    $new_title = str_replace('%page_author_firstname%', ucwords($authordata->first_name), $new_title);
                                    $new_title = str_replace('%page_author_lastname%', ucwords($authordata->last_name), $new_title);

                                } else $new_title = $title;

        $title = trim($new_title);
        $title = apply_filters('rtseop_title_page', $title);

        $header = $this->replace_title($header, $title);
      }
    }
    elseif (function_exists('is_tag') && is_tag()       && $rtsp_options['rtseo_rewrite_titles'])
    {
      $tag = single_term_title( '', false );

      if ($tag)
      {
        $tag = $this->capitalize($tag);
        $title_format = stripslashes( $rtsp_options['rtseo_tag_title_format'] );

        $title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
        $title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $title);
        $title = str_replace('%tag%', $tag, $title);
        $title = $this->paged_title($title);

        $header = $this->replace_title($header, $title);
      }
    }
    elseif (isset($STagging) && $STagging->is_tag_view()        && $rtsp_options['rtseo_rewrite_titles']) // simple tagging support
    {
      $tag = $STagging->search_tag;

      if ($tag)
      {
        $tag = $this->capitalize($tag);
        $title_format = stripslashes( $rtsp_options['rtseo_tag_title_format'] );

        $title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
        $title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $title);
        $title = str_replace('%tag%', $tag, $title);
        $title = $this->paged_title($title);

        $header = $this->replace_title($header, $title);
      }
    }
    else if (is_tax() && $rtsp_options['rtseo_rewrite_titles']) {
      $t_sep = ' ';
      $title_format = stripslashes( $rtsp_options['rtseo_custom_taxonomy_title_format'] );
      $term = get_queried_object();
      $tax = get_taxonomy( $term->taxonomy );
      $sCategoryName = $tax->labels->name;
      $sCategoryTitle = single_term_title('', false );
      $new_title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
      $new_title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $new_title);
      $new_title = str_replace('%tax_type_title%', $sCategoryName, $new_title);
      $new_title = str_replace('%tax_title%', $sCategoryTitle, $new_title);

      $title = trim($new_title);
      $title = $this->paged_title($title);

      $header = $this->replace_title($header, $title);
    }
    else if (is_archive()       && $rtsp_options['rtseo_rewrite_titles'])
    {
      if ( is_author() ) {
        $title_format = stripslashes( $rtsp_options['rtseo_author_title_format'] );

        $author     = $wp_query->get_queried_object();

        $new_title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
        $new_title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $new_title);
        $new_title = str_replace('%author%', $author->display_name, $new_title);
        $new_title = str_replace('%author_firstname%', $author->first_name, $new_title);
        $new_title = str_replace('%author_lastname%', $author->last_name, $new_title);
      }
      else {
        $title_format = stripslashes( $rtsp_options['rtseo_archive_title_format'] );
        $t_sep = ' ';
        if( is_date() ) {
          global $wp_locale;
          $m = get_query_var('m');
          $year = get_query_var('year');
          $monthnum = get_query_var('monthnum');
          $day = get_query_var('day');

          if( !empty($m) ) {
            $my_year = substr($m, 0, 4);
            $my_month = $wp_locale->get_month(substr($m, 4, 2));
            $my_day = intval(substr($m, 6, 2));
            $archive_title = $my_year . ( $my_month ? $t_sep . $my_month : '' ) . ( $my_day ? $t_sep . $my_day : '' );
          }
          if( !empty($year) ) {
            $archive_title = $year;
            if ( !empty($monthnum) )
              $archive_title .= $t_sep . $wp_locale->get_month($monthnum);
            if ( !empty($day) )
              $archive_title .= $t_sep . zeroise($day, 2);
          }
        } else if (is_post_type_archive()) {
          $term = get_queried_object();
          $archive_title = $term->labels->name;
        }

        $new_title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
        $new_title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $new_title);
        $new_title = str_replace('%date%', $archive_title, $new_title);
      }

      $title = trim($new_title);
      $title = $this->paged_title($title);

      $header = $this->replace_title($header, $title);
    }
    else if (is_404()       && $rtsp_options['rtseo_rewrite_titles'])
    {
      $title_format = stripslashes( $rtsp_options['rtseo_404_title_format'] );

      $new_title = str_replace('%blog_title%', $this->internationalize(get_bloginfo('name')), $title_format);
      $new_title = str_replace('%blog_description%', $this->internationalize(get_bloginfo('description')), $new_title);
      $new_title = str_replace('%request_url%', esc_url($_SERVER['REQUEST_URI']), $new_title);
      $new_title = str_replace('%request_words%', $this->request_as_words(esc_url($_SERVER['REQUEST_URI'])), $new_title);
      $new_title = str_replace('%404_title%', $this->internationalize(wp_title('', false)), $new_title);

      $header = $this->replace_title($header, $new_title);
    }

    return $header;
  }
  
  function request_as_words($request)
  {
    $request = htmlspecialchars($request);
    $request = str_replace('.html', ' ', $request);
    $request = str_replace('.htm', ' ', $request);
    $request = str_replace('.', ' ', $request);
    $request = str_replace('/', ' ', $request);

    $request_a = explode(' ', $request);
    $request_new = array();

    foreach ($request_a as $token)
    {
      $request_new[] = ucwords(trim($token));
    }

    $request = implode(' ', $request_new);

    return $request;
  }

  function trim_excerpt_without_filters($text)
  {
    $text = str_replace(']]>', ']]&gt;', $text);
    $text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);
    $text = strip_tags($text);

    $max = $this->maximum_description_length;

    if ($max < strlen($text))
    {
      while ($text[$max] != ' ' && $max > $this->minimum_description_length)
      {
        $max--;
      }
    }

    $text = substr($text, 0, $max);

    return trim(stripcslashes($text));
  }

  function trim_excerpt_without_filters_full_length($text)
  {
    $text = str_replace(']]>', ']]&gt;', $text);
    $text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);
    $text = strip_tags($text);

    return trim(stripcslashes($text));
  }

  function get_all_keywords()
  {
    global $posts;
    global $rtsp_options;

    if (is_404())
    {
      return null;
    }

    if (!is_home() && !is_page() && !is_single() &&!$this->is_static_front_page() && !$this->is_static_posts_page())
    {
      return null;
    }

    $keywords = array();

    if (is_array($posts))
    {
         $aIDs = array();
         foreach( $posts as $objPost ) $aIDs[] = $objPost->ID;

         if( function_exists( 'update_meta_cache' ) ) update_meta_cache( 'post', $aIDs );
         if( ( $rtsp_options['rtseo_use_tags_as_keywords'] || ( $rtsp_options['rtseo_use_categories'] && !is_page() ) )
               && function_exists( 'wp_get_object_terms' )
               && function_exists( 'wp_cache_add' ) )
         {
            $aTax = array();
            if( $rtsp_options['rtseo_use_tags_as_keywords'] ) $aTax[] = 'post_tag';
            if( $rtsp_options['rtseo_use_categories'] && !is_page() ) $aTax[] = 'category';

            $aRawTerms = array();
            if( 0 < count( $aIDs ) && 0 < count( $aTax ) )
               $aRawTerms = wp_get_object_terms( $aIDs, $aTax, array( 'orderby' => 'count', 'order' => 'DESC', 'fields' => 'all_with_object_id' ) );
            $aTags = array();
            $aCats = array();


            foreach( $aRawTerms as $objTerm ){
               if( !isset( $aTags[$objTerm->object_id] ) ) $aTags[$objTerm->object_id] = array();
               if( !isset( $aCats[$objTerm->object_id] ) ) $aCats[$objTerm->object_id] = array();

               if( 'category' == $objTerm->taxonomy ) $aCats[$objTerm->object_id][] = $objTerm;
               if( 'post_tag' == $objTerm->taxonomy ) $aTags[$objTerm->object_id][] = $objTerm;
            }

            if( $rtsp_options['rtseo_use_categories'] && !is_page() )
               foreach( $aCats as $id => $aPostCats )
                  wp_cache_add( $id, $aPostCats, 'category_relationships');
            if( $rtsp_options['rtseo_use_tags_as_keywords'] )
               foreach( $aTags as $id => $aPostTags )
                  wp_cache_add( $id, $aPostTags, 'post_tag_relationships');
         }


      foreach ($posts as $post)
      {
        if ($post)
        {
          $keywords_a = $keywords_i = null;
          $description_a = $description_i = null;

          $id = is_attachment() ? $post->post_parent : $post->ID; // if attachment then use parent post id

          if ($rtsp_options['rtseo_use_tags_as_keywords'])
          {
            if (function_exists('get_the_tags'))
            {
              $tags = get_the_tags($id);

              if ($tags && is_array($tags))
              {
                foreach ($tags as $tag)
                {
                  $keywords[] = $this->internationalize($tag->name);
                }
              }
            }
          }

          $autometa = stripcslashes(get_post_meta($id, 'autometa', true));

          if (isset($autometa) && !empty($autometa))
          {
            $autometa_array = explode(' ', $autometa);

            foreach ($autometa_array as $e)
            {
              $keywords[] = $e;
            }
          }
		  
		  $postKeywords = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($id, 'rtseo_keywords', true))));
		  
          if (isset($postKeywords) && !empty($postKeywords))
          {
            $postKeywordsArray = explode(' ', $postKeywords);

            foreach ($postKeywordsArray as $e)
            {
              $keywords[] = $e;
            }
          }

          if ($rtsp_options['rtseo_use_categories'] && !is_page())
          {
            $categories = get_the_category($id);

            foreach ($categories as $category)
            {
              $keywords[] = $this->internationalize($category->cat_name);
            }
          }
        }
      }
    }

    return $this->get_unique_keywords($keywords);
  }


  function post_meta_tags($id)
  {

    if( isset( $_POST['rtseo_edit'] ) ) {
      $awmp_edit = $_POST['rtseo_edit'];
    }
    if( isset( $_POST['nonce-rtseopedit'] ) ) {
      $nonce = $_POST['nonce-rtseopedit'];
    }

    if (isset($awmp_edit) && !empty($awmp_edit) && wp_verify_nonce($nonce, 'edit-rtseopnonce'))
    {


		$description = ( isset( $_POST["rtseo_description"] ) && $_POST["rtseo_description"] != 'Using post excerpt, type your SEO meta description here.' ) ? $_POST["rtseo_description"] : NULL;
		$keywords = ( isset( $_POST["rtseo_keywords"] ) ) ? $_POST["rtseo_keywords"] : NULL;
		$title = isset( $_POST["rtseo_title"] ) ? $_POST["rtseo_title"] : NULL;
		$noindex = isset( $_POST["rtseo_noindex"] ) ? $_POST["rtseo_noindex"] : NULL;


        delete_post_meta($id, 'rtseo_description');
        delete_post_meta($id, 'rtseo_title');
        delete_post_meta($id, 'rtseo_noindex');
        delete_post_meta($id, 'rtseo_keywords');

        if (isset($description) && !empty($description))
        {
			add_post_meta($id, 'rtseo_description', $description);
        }

        if (isset($keywords) && !empty($keywords))
        {
			add_post_meta($id, 'rtseo_keywords', $keywords);
        }

        if (isset($title) && !empty($title) && $title != get_the_title( $id ) )
        {
			add_post_meta($id, 'rtseo_title', $title);
        }

        if (isset($noindex) && !empty($noindex))
        {
			add_post_meta($id, 'rtseo_noindex', true);
        } else {
			add_post_meta($id, 'rtseo_noindex', false);
		}

    }
  }


  function get_unique_keywords($keywords)
  {
    $small_keywords = array();

    foreach ($keywords as $word)
    {
      if (function_exists('mb_strtolower'))
        $small_keywords[] = mb_strtolower($word, get_bloginfo('charset'));
      else
        $small_keywords[] = $this->strtolower($word);
    }

    $keywords_ar = array_unique($small_keywords);

    return implode(',', $keywords_ar);
  }


  function is_admin()
  {
    return current_user_can('level_8');
  }


  function microdata_category_links( $sHTML ) {
    $sHTML = preg_replace( '~rel=[\'"].*?[\'"]~', '', $sHTML );
    return $sHTML;
  }

  function admin_menu()
  {
	global $RT_SEO_Dashboard, $RT_SEO_Security, $RT_SEO_Ssl, $RT_SEO_Redirect, $RT_SEO_Images, $RT_SEO_Content_Protection, $RT_SEO_BadBotBlocker, $RT_SEO_Import, $RT_SEO_Vitals;
    $menu_hook = add_menu_page('rt_seo_dashboard', 'RealTime SEO', 'activate_plugins', 'rt_seo_dashboard', array($RT_SEO_Dashboard, 'dashboard_panel'), plugins_url( 'images/icon.svg', __FILE__ ));
	add_action( 'load-' . $menu_hook, array( $this, 'enqueue_rtseo_assets' ) );
	add_submenu_page( 'rt_seo_dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'rt_seo_dashboard', array($RT_SEO_Dashboard, 'dashboard_panel')	 );
	$settings_hook = add_submenu_page( 'rt_seo_dashboard', 'Settings', 'Settings', 'manage_options', $this->plugin_slug, array($this, 'options_panel') );
	add_action( 'load-' . $settings_hook, array( $this, 'enqueue_rtseo_assets' ) );
	$security_hook = add_submenu_page( 'rt_seo_dashboard', 'Security', 'Security', 'manage_options', 'rt_seo_security', array($RT_SEO_Security, 'security_panel') );
	add_action( 'load-' . $security_hook, array( $this, 'enqueue_rtseo_assets' ) );
	$ssl_hook = add_submenu_page( 'rt_seo_dashboard', 'SSL', 'SSL', 'manage_options', 'rt_seo_ssl', array($RT_SEO_Ssl, 'ssl_panel') );
	add_action( 'load-' . $ssl_hook, array( $this, 'enqueue_rtseo_assets' ) );
	
	$images_hook = add_submenu_page( 'rt_seo_dashboard', 'Image Optimizer', 'Image Optimizer', 'manage_options', 'rt_seo_images', array($RT_SEO_Images, 'images_panel') );
	add_action( 'load-' . $images_hook, array( $this, 'enqueue_rtseo_assets' ) );
	
	$redirect_hook = add_submenu_page( 'rt_seo_dashboard', 'Redirects', 'Redirects', 'manage_options', 'rt_seo_redirect', array($RT_SEO_Redirect, 'redirect_panel') );
	add_action( 'load-' . $redirect_hook, array( $this, 'enqueue_rtseo_assets' ) );
	$protection_hook = add_submenu_page( 'rt_seo_dashboard', 'SEO Protection', 'SEO Protection', 'manage_options', 'rt_seo_protection', array($RT_SEO_Content_Protection, 'protection_panel') );
	add_action( 'load-' . $protection_hook, array( $this, 'enqueue_rtseo_assets' ) );
	$antibot_hook = add_submenu_page( 'rt_seo_dashboard', 'Bad Bot Blocker', 'Bad Bot Blocker', 'manage_options', 'rt_seo_badbot_blocker', array($RT_SEO_BadBotBlocker, 'badbot_panel') );
	add_action( 'load-' . $antibot_hook, array( $this, 'enqueue_rtseo_assets' ) );
	$import_hook = add_submenu_page( 'rt_seo_dashboard', 'Import Settings', 'Import Settings', 'manage_options', 'rt_seo_import', array($RT_SEO_Import, 'import_panel') );
	add_action( 'load-' . $import_hook, array( $this, 'enqueue_rtseo_assets' ) );
	$vitals_hook = add_submenu_page( 'rt_seo_dashboard', 'Core Web Vitals', 'Core Web Vitals', 'manage_options', 'rt_seo_vitals', array($RT_SEO_Vitals, 'vitals_panel') );
	add_action( 'load-' . $vitals_hook, array( $this, 'enqueue_rtseo_assets' ) );
	add_submenu_page( null, 'Installation Wizard', 'Installation Wizard', 'manage_options', 'rtseop_install&rtseop_install_nonce=' . wp_create_nonce( 'seop-install-nonce' ), array('SGEOP_Page_Install', 'run') );
	/*
	$get_pro_hook = add_submenu_page('rt_seo_dashboard', 'Upgrade to PRO', 'Upgrade to PRO', 'manage_options', 'get_pro', array( $this, 'get_pro' ) );
	add_action("load-" . $get_pro_hook, array( $this, 'get_pro' ) );
	*/
  }

	public function get_pro() {
		header("Location:". RT_SEO_Helper::$LINKS['get_pro']);
		exit;

	}
  
	public function enqueue_rtseo_assets() {
		global $wp_scripts;
		wp_enqueue_script( 'rtseop-install-js', plugins_url( 'assets/js/install.js', __FILE__ ) );
		wp_enqueue_script( 'rtseop-install-js2', plugins_url( 'assets/js/modal.min.js', __FILE__ ) );
		wp_enqueue_script( 'rtseop-install-js3', plugins_url( 'assets/js/semantic.min.js', __FILE__ ) );
		wp_enqueue_style( 'rtseop-install-css3', plugins_url( 'assets/css/semantic.min.css', __FILE__ ) );

	}

  function sitemap_settings_advanced() {
    global $rtsp_options;
	?>

    <h3 class="ui header"><i class="sitemap icon"></i>Sitemap settings</h3>
    
	<div class="ui segment" style="max-width: 1100px;">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					<?php _e( 'Enable Sitemap', 'rt_seo' )?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_enable_sitemap_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rt_seo_enable_sitemap_tip" style="display:none">
					<?php _e( 'Check to enable generating sitemap. It will be generated automatically 1 time per 24 hours', 'rt_seo' )?>
				</p>

			</div>

			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_enable_sitemap" <?php if ( $rtsp_options['rtseo_enable_sitemap'] ) echo "checked=\"1\""; ?>  onclick="toggleVisibility('rtseo_sitemap_options');"/>
							<label></label>
					    </div>
				    </div>
				</div>
			</div>
			<div style="width: 100%; margin: auto; margin-bottom:20px;text-align:left; display:<?php if ($rtsp_options['rtseo_enable_sitemap']) echo 'block'; else echo 'none'; ?>" id="rtseo_sitemap_options">

				<div class="ui segment">
				<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								<?php _e( 'Include Archive Pages', 'rt_seo' )?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_inc_archives_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rt_seo_inc_archives_tip" style="display:none">
								<?php _e( 'Check to include date archive pages, such as YYYY or YYYY/MM, in your sitemap.', 'rt_seo' )?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
								  <div class="ui fitted toggle checkbox">
										<input type="checkbox" name="rtseo_inc_archives" <?php if ( $rtsp_options['rtseo_inc_archives'] ) echo "checked=\"1\""; ?>/>
									<label></label>
								  </div>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								<?php _e( 'Include Author Pages', 'rt_seo' )?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_inc_authors_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rt_seo_inc_authors_tip" style="display:none">
								<?php _e( 'Check to include author pages in your sitemap.', 'rt_seo' )?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
								  <div class="ui fitted toggle checkbox">
										<input type="checkbox" name="rtseo_inc_authors" <?php if ( $rtsp_options['rtseo_inc_authors'] ) echo "checked=\"1\""; ?>/>
									<label></label>
								  </div>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								<?php _e( 'Empty Author Pages', 'rt_seo' )?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_empty_author_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rt_seo_empty_author_tip" style="display:none">
								<?php _e( 'Check to include author page in your sitemap if the author(s) has not published any pages or posts yet.', 'rt_seo' )?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
								  <div class="ui fitted toggle checkbox">
										<input type="checkbox" name="rtseo_empty_author" <?php if ( $rtsp_options['rtseo_empty_author'] ) echo "checked=\"1\""; ?>/>
									<label></label>
								  </div>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								<?php _e( 'Include Category Pages', 'rt_seo' )?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_inc_categories_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rt_seo_inc_categories_tip" style="display:none">
								<?php _e( 'Check to include category pages in your sitemap.', 'rt_seo' )?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
								  <div class="ui fitted toggle checkbox">
										<input type="checkbox" name="rtseo_inc_categories" <?php if ( $rtsp_options['rtseo_inc_categories'] ) echo "checked=\"1\""; ?>/>
									<label></label>
								  </div>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								<?php _e( 'Include Tag Pages', 'rt_seo' )?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_inc_tags_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rt_seo_inc_tags_tip" style="display:none">
								<?php _e( 'Check to include tag pages in your sitemap.', 'rt_seo' )?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
								  <div class="ui fitted toggle checkbox">
										<input type="checkbox" name="rtseo_inc_tags" <?php if ( $rtsp_options['rtseo_inc_tags'] ) echo "checked=\"1\""; ?>/>
									<label></label>
								  </div>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								<?php _e( 'Include Custom Posts', 'rt_seo' )?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_inc_custom_posts_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rt_seo_inc_custom_posts_tip" style="display:none">
								<?php _e( 'Check to include custom posts in your sitemap.', 'rt_seo' )?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
								  <div class="ui fitted toggle checkbox">
										<input type="checkbox" name="rtseo_inc_custom_posts" <?php if ( $rtsp_options['rtseo_inc_custom_posts'] ) echo "checked=\"1\""; ?>/>
									<label></label>
								  </div>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e( 'Exclude Pages &amp; Posts', 'rt_seo' )?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_exclude_pages_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rt_seo_exclude_pages_tip" style="display:none">
								<?php _e( 'IDs of pages and/or posts you do not wish to include in your sitemap separated by commas (\',\'):', 'rt_seo' )?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input type="text" size="55" name="rtseo_exclude_pages" value="<?php if (isset($rtsp_options['rtseo_exclude_pages'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_exclude_pages']));?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								<?php _e( 'Generate Mobile Sitemap', 'rt_seo' )?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_mobile_sitemap_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rt_seo_mobile_sitemap_tip" style="display:none">
								<?php _e( 'Check if this plugin is installed on an mobile-only site, such as m.example.com or example.mobi.', 'rt_seo' )?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
								  <div class="ui fitted toggle checkbox">
										<input type="checkbox" name="rtseo_mobile_sitemap" <?php if ( $rtsp_options['rtseo_mobile_sitemap'] ) echo "checked=\"1\""; ?>/>
									<label></label>
								  </div>
								</div>
							</div>
						</div>
					</div>

				</div>
		</div>
		</div>
	</div>

	<?php
  }




  function admin_settings_advanced() {
    global $rtsp_options;

    if( !function_exists('pll_languages_list') ) {
      $languages      = array();
      $default_lang   = '';
    }
    else {
      $language_list  = pll_languages_list();
      $default_lang   = pll_default_language();
      $languages      = array_diff( $language_list, array( $default_lang ) ); // additional lang without default one
    }



  ?>
<!-- <style>
 .ui.toggle.checkbox input:focus:checked~.box:before,
 .ui.toggle.checkbox input:checked~.box:before,
.ui.toggle.checkbox input:focus:checked~label:before,
.ui.toggle.checkbox input:checked~label:before {
    background-color: green !important
}

.ui.toggle.checkbox .box:before, .ui.toggle.checkbox label:before {
    background-color: grey;
}
 </style> -->


<h3 class="ui header"><i class="cogs icon"></i>General settings</h3>

<div class="ui" style="max-width: 1100px;">

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Website API key:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_website_api_key_tip');"><i class="question circle icon"></i></a>
					<a style="cursor:pointer;" onclick="document.location.href = 'admin.php?page=rt_seo_pack&refresh';" title="<?php _e('Update', 'rt_seo')?>"><i class="sync icon"></i></a></p>
				</h3>
				<p id="rtseo_website_api_key_tip" style="display:none">
					<?php _e('Your website API key', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<input type="text" name="rtseo_website_api_key" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_website_api_key']))?>" />
					</div>
				</div>

			</div>
		</div>
	</div>
	
	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('HomePage Title:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_home_title_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_home_title_tip" style="display:none">
					<?php _e('This Title will appear in various places around the web, including the tab in your web browser, it also will be pulled in as the anchor text when sharing on other websites and social media, and most importantly of all, your title tag will show up as the big blue link in Google search results. <a href = "https://seoguarding.com/kb/seo-title-examples/" target ="_blank">More information</a>', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<input type="text" name="rtseo_home_title" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_home_title']))?>" />
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('HomePage Description:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_home_description_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_home_description_tip" style="display:none">
					<?php _e('A meta description is a summary of 150 - 200 characters in length that describes the content of a web page. Search engines show it in search results when the meta description also includes the keywords being searched. Dont forget to add your brand name at the end. <a href = "/kb/right-meta-description/" target ="_blank">More information</a> ', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="55" rows="4" style="resize: none;" name="rtseo_home_description"><?php echo esc_attr(stripcslashes($rtsp_options['rtseo_home_description']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('HomePage Keywords (separate with commas):', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_home_keywords_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_home_keywords_tip" style="display:none">
					<?php _e('Meta keywords are no longer an important part of the Google ranking algorithm. (More attention ought to be paid to Title Tags and Meta Descriptions than to Meta Keywords.) But they can nonetheless still play a small but helpful part in attracting searchers to your site. <a href = "https://seoguarding.com/kb/meta-keywords/" target =\"_blank\">More information</a>', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="55" rows="4" style="resize: none;" name="rtseo_home_keywords"><?php echo esc_attr(stripcslashes($rtsp_options['rtseo_home_keywords'])); ?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
				   <p style="font-size:16px;font-weight:bold;">
					  <?php _e('Warn me when publishing without a title:', 'rt_seo')?>
				   <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_warnings_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_warnings_tip" style="display:none">
					<?php _e("The title tag is a very strong signal for search bots to understand what the page is all about, so you should use this factor as effectively as possible. Enable this option and our plugin will warn you about the error if you forget to specify the title for the page. Default: checked.", 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_publ_warnings" <?php if ($rtsp_options['rtseo_publ_warnings']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
				   <p style="font-size:16px;font-weight:bold;">
					  <?php _e('Shorten Page URL / Post Slug:', 'rt_seo')?>
				   <a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_shorten_slugs');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_shorten_slugs" style="display:none">
					<?php _e('This feature will automatically shorten the page URL so that the URL is not too long. You can also edit your URLs later in a manual mode. <a href = "https://seoguarding.com/kb/correct-url-address/" target ="_blank">More information</a>', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_shorten_slugs" <?php if( isset($rtsp_options['rtseo_shorten_slugs']) && $rtsp_options['rtseo_shorten_slugs'] ) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Canonical URLs:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_can_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_can_tip" style="display:none">
					<?php _e("This feature will help you to avoid Google penalty for duplicate content. Our plugin will write canonical URLs automatically for all WordPress pages. <a href='https://seoguarding.com/kb/canonical-urls/' target='_blank'>More information</a>.", 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_can" <?php if ($rtsp_options['rtseo_can']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Redirect attachment links to the file URLs:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_attachments_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_attachments_tip" style="display:none">
					<?php _e("WordPress often creates not useful pages with addresses like this: http://yoursite.com/?attachment{id}. Google considers these attachments separate pages, so instead of 3-4 posts or pages you will see in Google index hundreds of pages you didn’t know about. We recommend that you enable this feature.", 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_attachments" <?php if ($rtsp_options['rtseo_attachments']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Enable shortlinks in header:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_shortlinks_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_shortlinks_tip" style="display:none">
					<?php _e("We don't recommend using the Wordpress <a href='http://microformats.org/wiki/rel-shortlink' target= '_blank'>shortlinks</a> if you use permalinks on your website. You might want to use third party link shortening services instead.", 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_shortlinks" <?php if ($rtsp_options['rtseo_shortlinks']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Enable hAtom microformat :', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_hentry_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_hentry_tip" style="display:none">
					<?php _e("hAtom is a microformat for identifying semantic information in weblog posts and practically any other place Atom may be used, such as news articles. hAtom content is easily added to most blogs by simple modifications to the blog’s template definitions”. This microformat is implemented on any site by adding it’s specific classes to markup. In this case classes aren’t used for styling elements but for highlighting the elements of microformat.", 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_hentry" <?php if ($rtsp_options['rtseo_hentry']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Rewrite Titles:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_rewrite_titles_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_rewrite_titles_tip" style="display:none">
					<?php _e("Note that this is all about the title tag. This is what you see in your browser's window title bar. This is NOT visible on a page, only in the window title bar and of course in the source. If set, all page, post, category, search and archive page titles get rewritten. You can specify the format for most of them. For example: The default templates puts the title tag of posts like this: Blog Archive >> Blog Name >> Post Title (maybe I've overdone slightly). This is far from optimal. With the default post title format, Rewrite Title rewrites this to Post Title | Blog Name. If you have manually defined a title (in one of the text fields for Real Time SEO Plugin input) this will become the title of your post in the format string.", 'rt_seo')?>
				</p>

			</div>

			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_rewrite_titles" <?php if ($rtsp_options['rtseo_rewrite_titles']) echo 'checked="checked"'; ?> onclick="toggleVisibility('rtseo_rewrite_titles_options');" />
							<label></label>
					    </div>
				    </div>
				</div>
			</div>
			<div style="width: 80%; margin: auto;text-align:left; display:<?php if ($rtsp_options['rtseo_rewrite_titles']) echo 'block'; else echo 'none'; ?>" id="rtseo_rewrite_titles_options">
				<div class="ui segment">
					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
									<?php _e('Post Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_post_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_post_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%post_title% - The original title of the post', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%category_title% - The (main) category of the post', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%category% - Alias for %category_title%', 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%post_author_login% - This post's author' login", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%post_author_nicename% - This post's author' nicename", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%post_author_firstname% - This post's author' first name (capitalized)", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%post_author_lastname% - This post's author' last name (capitalized)", 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_post_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_post_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
									<?php _e('Custom Post Type Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_custom_post_title_format');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_custom_post_title_format" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%post_title% - The original title of the post', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%post_type_name% - The name of custom post type', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%category_title% - The (main) category of the post', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%category% - Alias for %category_title%', 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%post_author_login% - This post's author' login", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%post_author_nicename% - This post's author' nicename", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%post_author_firstname% - This post's author' first name (capitalized)", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%post_author_lastname% - This post's author' last name (capitalized)", 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input  style="width: 100%;" name="rtseo_custom_post_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_custom_post_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Page Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_page_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_page_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%page_title% - The original title of the page', 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%page_author_login% - This page's author' login", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%page_author_nicename% - This page's author' nicename", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%page_author_firstname% - This page's author' first name (capitalized)", 'rt_seo'); echo('</li>');
									echo('<li>'); _e("%page_author_lastname% - This page's author' last name (capitalized)", 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_page_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_page_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Category Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_category_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_category_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%category_title% - The original title of the category', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%category_description% - The description of the category', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_category_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_category_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Author Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_author_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_author_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%author% - Author name (display name)"', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%author_firstname% - Author first name"', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%author_lastname% - Author last name"', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_author_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_author_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Archive Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_archive_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_archive_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%date% - The original archive title given by wordpress, e.g. "2007" or "2007 August"', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_archive_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_archive_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Tag Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_tag_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_tag_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%tag% - The name of the tag', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_tag_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_tag_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Custom taxonomy Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_custom_taxonomy_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_custom_taxonomy_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%tax_title% - Your actual taxonomy category title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%tax_type_title% - Your taxonomy title', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_custom_taxonomy_title_format" value="<?php if (isset($rtsp_options['rtseo_custom_taxonomy_title_format'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_custom_taxonomy_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Search Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_search_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_search_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%search% - What was searched for', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_search_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_search_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Description Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_description_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_description_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%description% - The original description as determined by the plugin, e.g. the excerpt if one is set or an auto-generated one if that option is set', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%wp_title% - The original wordpress title, e.g. post_title for posts', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%page% - Page number for paged category archives', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_description_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_description_format'])); ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('404 Title Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_404_title_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_404_title_format_tip" style="display:none">
								<?php
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%request_url% - The original URL path, like "/url-that-does-not-exist/"', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%request_words% - The URL path in human readable form, like "Url That Does Not Exist"', 'rt_seo'); echo('</li>');
									echo('<li>'); _e('%404_title% - Additional 404 title input"', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_404_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_404_title_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>

					<div class="ui grid">
						<div class="eight wide column">
							<h3 class="ui header">
								<p style="font-weight:bold;">
								  <?php _e('Paged Format:', 'rt_seo')?>
								<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_paged_format_tip');"><i class="question circle icon"></i></a></p>
							</h3>
							<div id="rtseo_paged_format_tip" style="display:none">
								<?php
									_e('This string gets appended/prepended to titles when they are for paged index pages (like home or archive pages).', 'rt_seo');
									_e('The following macros are supported:', 'rt_seo');
									echo('<ul>');
									echo('<li>'); _e('%page% - The page number', 'rt_seo'); echo('</li>');
									echo('</ul>');
								?>
							</div>
						</div>
						<div class="ui top aligned center aligned eight wide column">
							<div class="ui top aligned center aligned form full_h">
								<div class="field">
									<input size="59" style="width: 100%;" name="rtseo_paged_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_paged_format'])); ?>"/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Use Categories for META Keywords:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_use_categories_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_use_categories_tip" style="display:none">
					<?php _e('Enable this feature to use keywords categories as META keywords for an article automatically.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_use_categories" <?php if ($rtsp_options['rtseo_use_categories']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Use Tags for META Keywords:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_use_tags_as_keywords_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_use_tags_as_keywords_tip" style="display:none">
					<?php _e('Enable this feature for our plugin to use Tags as keywords for an article automatically.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_use_tags_as_keywords" <?php if ($rtsp_options['rtseo_use_tags_as_keywords']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Dynamically Generate Keywords for Posts and Pages:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_dynamic_postspage_keywords_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_dynamic_postspage_keywords_tip" style="display:none">
					<?php _e('Enable this feature for our plugin to generate META keywords automatically.', 'rt_seo') ?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_dynamic_postspage_keywords" <?php if ($rtsp_options['rtseo_dynamic_postspage_keywords']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Remove Category rel attribute:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_remove_category_rel_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_remove_category_rel_tip" style="display:none">
					<?php _e('WordPress has a handy little function called the_category() which outputs a link to the current post category in the loop. Unfortunately it adds a rel tag that breaks HTML5 validation. To stop WordPress from adding rel="category tag" to your category links just turn this feature on.', 'rt_seo') ?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_remove_category_rel" <?php if ($rtsp_options['rtseo_remove_category_rel']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;"">
					  <?php _e('Use Noindex for Categories:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_category_noindex_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_category_noindex_tip" style="display:none">
					<?php _e('The NOINDEX tag tells search engines that the content/page should not be indexed by search bots. Hence, non-indexable information will not appear in the search results. Such tags are often used to close useless pages with duplicated content or something else. Use noindex for categories. If you do not lead traffic to categories pages, you can hide them from indexing. Inexperienced webmasters often close them on information sites, because the categories are kind of double.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_category_noindex" <?php if ($rtsp_options['rtseo_category_noindex']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Use Noindex for Archives:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_archive_noindex_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_archive_noindex_tip" style="display:none">
					<?php _e('Check this for excluding tag pages from being crawled. Useful for avoiding duplicate content.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_archive_noindex" <?php if ($rtsp_options['rtseo_archive_noindex']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Use Noindex for Tag Archives:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_tags_noindex_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_tags_noindex_tip" style="display:none">
					<?php _e('Tags are intended for more convenient and fast navigation of visitors on your site. On the one hand, they make visitors’ life simple, but on the other hand, they generate pages with duplicate content. For example, yoursite.com/post can be duplicated at yoursite.com/tag/awesomepost, which can affect your position in the Google search results drastically. We strongly recommend that you enable this option.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_tags_noindex" <?php if ($rtsp_options['rtseo_tags_noindex']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Use Noindex for Search Results:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_search_noindex_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_search_noindex_tip" style="display:none">
					<?php _e('There is no point for Google to index search results on your website. It can result in large number of pages getting into Google and can even be considered as SPAM. We recommend that you turn this option on.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_search_noindex" <?php if ($rtsp_options['rtseo_search_noindex']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Generate Descriptions:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_generate_descriptions_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_generate_descriptions_tip" style="display:none">
					<?php _e("Enable this feature for our plugin to use your pages content to generate a META Description automatically.", 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_generate_descriptions" <?php if ($rtsp_options['rtseo_generate_descriptions']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Capitalize Category Titles:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_cap_cats_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_cap_cats_tip" style="display:none">
					<?php _e("Check this and Category Titles will have the first letter of each word capitalized.", 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_cap_cats" <?php if ($rtsp_options['rtseo_cap_cats']) echo 'checked="checked"'; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Exclude Pages:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_ex_pages_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_ex_pages_tip" style="display:none">
					<?php _e("This feature is useful for those pages and modules you would like to exclude from our module. For example, if you use the FAQ system, or HelpDesk, then enter yoursite.com/faq or yoursite.com/helpdesk. Separate pages with commas.", 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="57" rows="3" style="resize: none;" name="rtseo_ex_pages"><?php if( isset( $rtsp_options['rtseo_ex_pages'] ) ) echo esc_attr(stripcslashes($rtsp_options['rtseo_ex_pages']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Additional Post Headers:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_post_meta_tags_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_post_meta_tags_tip" style="display:none">
				 <?php
					  _e('What you enter here will be copied verbatim to your header on post pages. You can enter whatever additional headers you want here, even references to stylesheets.', 'rt_seo');
					  echo '<br/>';
					  _e('NOTE: This field currently only support meta tags.', 'rt_seo');
                  ?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="57" rows="3" style="resize: none;" name="rtseo_post_meta_tags"><?php echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_post_meta_tags']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Additional Page Headers:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_page_meta_tags_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_page_meta_tags_tip" style="display:none">
                  <?php
					  _e('What you enter here will be copied verbatim to your header on pages. You can enter whatever additional headers you want here, even references to stylesheets.', 'rt_seo');
					  echo '<br/>';
					  _e('NOTE: This field currently only support meta tags.', 'rt_seo');
                  ?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="57" rows="3" style="resize: none;" name="rtseo_page_meta_tags"><?php echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_page_meta_tags']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Google Verification Meta Tag:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_home_google_site_verification_meta_tag_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_home_google_site_verification_meta_tag_tip" style="display:none">
                  <?php
					  _e('What you enter here will be copied verbatim to your header on the home page. Webmaster Tools provides the meta tag in XHTML syntax.', 'rt_seo');
					  echo('<br/>');
					  echo('1. '); _e('On the Webmaster Tools Home page, click Verify this site next to the site you want.', 'rt_seo');
					  echo('<br/>');
					  echo('2. '); _e('In the Verification method list, select Meta tag, and follow the steps on your screen.', 'rt_seo');
					  echo('<br/>');
					  _e('Once you have added the tag to your home page, click Verify.', 'rt_seo');
                  ?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="57" rows="3" style="resize: none;" name="rtseo_home_google_site_verification_meta_tag"><?php if( isset( $rtsp_options['rtseo_home_google_site_verification_meta_tag'] ) ) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_home_google_site_verification_meta_tag']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Yahoo Verification Meta Tag:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_home_yahoo_site_verification_meta_tag');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_home_yahoo_site_verification_meta_tag" style="display:none">
                  <?php _e('Put your Yahoo site verification tag for your homepage here.', 'rt_seo'); ?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="57" rows="3" style="resize: none;" name="rtseo_home_yahoo_site_verification_meta_tag"><?php if( isset( $rtsp_options['rtseo_home_yahoo_site_verification_meta_tag'] ) ) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_home_yahoo_site_verification_meta_tag']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>




	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Bing Verification Meta Tag:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_home_bing_site_verification_meta_tag');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_home_bing_site_verification_meta_tag" style="display:none">
                  <?php _e('Put your Bing site verification tag for your homepage here.', 'rt_seo'); ?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="57" rows="3" style="resize: none;" name="rtseo_home_bing_site_verification_meta_tag"><?php if( isset( $rtsp_options['rtseo_home_bing_site_verification_meta_tag'] ) ) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_home_bing_site_verification_meta_tag']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>


	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					<?php _e('Turn off excerpts for descriptions:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_dont_use_excerpt_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_dont_use_excerpt_tip" style="display:none">
					<?php _e("Since Typepad export is containing auto generated excerpts for the most of the time we use this option a lot.", 'rt_seo'); ?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_dont_use_excerpt" <?php if ($rtsp_options['rtseo_dont_use_excerpt']) echo "checked=\"1\""; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					<?php _e('Turn off descriptions for excerpts:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_dont_use_desc_for_excerpt_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_dont_use_desc_for_excerpt_tip" style="display:none">
					<?php _e("By default Realtime SEO will show meta description when post excerpt is called in the theme and it's not filled in. Also, if you use Genesis theme with its setting of 'Display post content' for 'Content archives' it will put in meta description instead if no read more tag is found and strip images from it as well.", 'rt_seo'); ?>
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rtseo_dont_use_desc_for_excerpt" <?php if ($rtsp_options['rtseo_dont_use_desc_for_excerpt']) echo "checked=\"1\""; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					<?php _e('Disable ads:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rt_seo_ads_disabled_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rt_seo_ads_disabled_tip" style="display:none">
					With this feature you can use code like <tt>!get_option('rt_seo_ads_disabled')</tt> in your <a href="https://wordpress.org/plugins/widget-logic/" target="_blank">Widget Logic</a> conditions to make all ad widgets disappear at once.
				</p>
			</div>
			<div class="ui top aligned center aligned eight wide column">
				<div class="ui top aligned center aligned form full_h">
				    <div class="field">
					    <div class="ui fitted toggle checkbox">
							<input type="checkbox" name="rt_seo_ads_disabled" <?php if ( get_option('rt_seo_ads_disabled') ) echo "checked=\"1\""; ?>/>
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
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Custom Header code:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_custom_header_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_custom_header_tip" style="display:none">
                  <?php _e('Here you can add any code you need to insert in &lt;head&gt; section.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="57" rows="3" style="resize: none;" name="rtseo_custom_header"><?php if (isset($rtsp_options['rtseo_custom_header'])) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_custom_header']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
				   <p style="font-size:16px;font-weight:bold;">
					  <?php _e('Footer code:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_custom_footer_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_custom_footer_tip" style="display:none">
                  <?php _e('Insert any tracking code which should be right before the closing &lt;/body&gt; tag on the site.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<textarea cols="57" rows="3" style="resize: none;" name="rtseo_custom_footer"><?php if (isset($rtsp_options['rtseo_custom_footer'])) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_custom_footer']))?></textarea>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					<?php _e('Google Analytics ID:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_ganalytics_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_ganalytics_tip" style="display:none">
                  <?php _e('Enter your google analytics ID. Example: UA-12345678-9', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<input type="text" size="55" name="rtseo_ganalytics_ID" value="<?php if (isset($rtsp_options['rtseo_ganalytics_ID'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_ganalytics_ID']))?>" />
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Statcounter Project ID:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_statcounter_tip_project');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_statcounter_tip_project" style="display:none">
                  <?php _e('Enter your project ID. You can obtain them from Statcounter administation > Project > Reinstall Code > Default Guide. Look for <i>sc_project</i> variable in code.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<input type="text" size="55" name="rtseo_statcounter_project" value="<?php if (isset($rtsp_options['rtseo_statcounter_project'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_statcounter_project']))?>" />
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="ui segment">
		<div class="ui grid">
			<div class="eight wide column">
				<h3 class="ui header">
					<p style="font-size:16px;font-weight:bold;">
					  <?php _e('Statcounter Security ID:', 'rt_seo')?>
					<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibility('rtseo_statcounter_tip');"><i class="question circle icon"></i></a></p>
				</h3>
				<p id="rtseo_statcounter_tip" style="display:none">
                  <?php _e('Enter your security ID. You can obtain them from Statcounter administation > Project > Reinstall Code > Default Guide. Look for <i>sc_security</i> variable in code.', 'rt_seo')?>
				</p>
			</div>
			<div class="ui top aligned eight wide column">
				<div class="ui top aligned form full_h">
					<div class="field">
						<input type="text" size="55" name="rtseo_statcounter_security" value="<?php if (isset($rtsp_options['rtseo_statcounter_security'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_statcounter_security']))?>" />
					</div>
				</div>

			</div>
		</div>
	</div>
</div>


  <?php
  }

  function options_panel()
  {
    $message = null;
	
    global $rtsp_options;
	
	if(isset($_GET['refresh'])) {
		$key = (strlen($rtsp_options['rtseo_website_api_key']) === 32) ? $rtsp_options['rtseo_website_api_key'] : false;
		$this->checkLicenceInfo($key, false);
	}
	
    if (!$rtsp_options['rtseo_cap_cats'])
      $rtsp_options['rtseo_cap_cats'] = '1';

    if( isset($_POST['action']) && $_POST['action'] == 'rtseo_update'){

      if( isset($_POST['rt_seo_ads_disabled']) ) {
        update_option('rt_seo_ads_disabled', 1 );
      } else {
        update_option('rt_seo_ads_disabled', 0 );
      }



      if( isset( $_POST['Submit_Default'] ) && $_POST['Submit_Default'] != '')
      {
        $nonce = $_POST['nonce-rtseop'];

        if (!wp_verify_nonce($nonce, 'rtseopack'))
          die ( 'Security Check - If you receive this in error, log out and back in to WordPress');



        $message = __("Realtime SEO Settings Reset.", 'rt_seo');

        delete_option('rtseop_options');

        global $rtsp_default_options;
        $res_rtsp_options = $rtsp_default_options;

        update_option('rtseop_options', $res_rtsp_options);
      }


      if( isset( $_POST['Submit'] ) && $_POST['Submit'] != '')
      {

		if ( isset( $_POST['done_post_install'] ) && $_POST['done_post_install'] == 1) {
			add_option('done_post_install', 1);
		}


        $nonce = $_POST['nonce-rtseop'];

        if (!wp_verify_nonce($nonce, 'rtseopack'))
          die ( 'Security Check - If you receive this in error, log out and back in to WordPress');

        $message = __("Realtime SEO Settings Updated.", 'rt_seo');

        $rtsp_options['rtseo_can'] = isset( $_POST['rtseo_can'] ) ? $_POST['rtseo_can'] : NULL;
        $rtsp_options['rtseo_mobile_sitemap'] = isset( $_POST['rtseo_mobile_sitemap'] ) ? $_POST['rtseo_mobile_sitemap'] : NULL;
        $rtsp_options['rtseo_enable_sitemap'] = isset( $_POST['rtseo_enable_sitemap'] ) ? $_POST['rtseo_enable_sitemap'] : NULL;
        $rtsp_options['rtseo_exclude_pages'] = isset( $_POST['rtseo_exclude_pages'] ) ? $_POST['rtseo_exclude_pages'] : NULL;
        $rtsp_options['rtseo_inc_custom_posts'] = isset( $_POST['rtseo_inc_custom_posts'] ) ? $_POST['rtseo_inc_custom_posts'] : NULL;
        $rtsp_options['rtseo_inc_tags'] = isset( $_POST['rtseo_inc_tags'] ) ? $_POST['rtseo_inc_tags'] : NULL;
        $rtsp_options['rtseo_inc_categories'] = isset( $_POST['rtseo_inc_categories'] ) ? $_POST['rtseo_inc_categories'] : NULL;
        $rtsp_options['rtseo_empty_author'] = isset( $_POST['rtseo_empty_author'] ) ? $_POST['rtseo_empty_author'] : NULL;
        $rtsp_options['rtseo_inc_authors'] = isset( $_POST['rtseo_inc_authors'] ) ? $_POST['rtseo_inc_authors'] : NULL;
        $rtsp_options['rtseo_inc_archives'] = isset( $_POST['rtseo_inc_archives'] ) ? $_POST['rtseo_inc_archives'] : NULL;
        $rtsp_options['rtseo_shortlinks'] = isset( $_POST['rtseo_shortlinks'] ) ? $_POST['rtseo_shortlinks'] : NULL;
        $rtsp_options['rtseo_hentry'] = isset( $_POST['rtseo_hentry'] ) ? $_POST['rtseo_hentry'] : NULL;
        $rtsp_options['rtseo_home_title'] = isset( $_POST['rtseo_home_title'] ) ? $_POST['rtseo_home_title'] : NULL;
        $rtsp_options['rtseo_home_description'] = isset( $_POST['rtseo_home_description'] ) ? $_POST['rtseo_home_description'] : NULL;
        $rtsp_options['rtseo_home_keywords'] = isset( $_POST['rtseo_home_keywords'] ) ? $_POST['rtseo_home_keywords'] : NULL;

        if( function_exists('pll_languages_list') ) {
          foreach ( pll_languages_list() as $lang ) {
            if ( $lang == pll_default_language() ) continue;

            $rtsp_options['rtseo_home_title_'.$lang] = isset( $_POST['rtseo_home_title_'.$lang] ) ? $_POST['rtseo_home_title_'.$lang] : NULL;
            $rtsp_options['rtseo_home_description_'.$lang] = isset( $_POST['rtseo_home_description_'.$lang] ) ? $_POST['rtseo_home_description_'.$lang] : NULL;
            $rtsp_options['rtseo_home_keywords_'.$lang] = isset( $_POST['rtseo_home_keywords_'.$lang] ) ? $_POST['rtseo_home_keywords_'.$lang] : NULL;
          }
        }

        $rtsp_options['rtseo_rewrite_titles'] = isset( $_POST['rtseo_rewrite_titles'] ) ? $_POST['rtseo_rewrite_titles'] : NULL;
        $rtsp_options['rtseo_post_title_format'] = isset( $_POST['rtseo_post_title_format'] ) ? $_POST['rtseo_post_title_format'] : NULL;
        $rtsp_options['rtseo_custom_post_title_format'] = isset( $_POST['rtseo_custom_post_title_format'] ) ? $_POST['rtseo_custom_post_title_format'] : NULL;
        $rtsp_options['rtseo_page_title_format'] = isset( $_POST['rtseo_page_title_format'] ) ? $_POST['rtseo_page_title_format'] : NULL;
        $rtsp_options['rtseo_category_title_format'] = isset( $_POST['rtseo_category_title_format'] ) ? $_POST['rtseo_category_title_format'] : NULL;
        $rtsp_options['rtseo_author_title_format'] = isset( $_POST['rtseo_author_title_format'] ) ? $_POST['rtseo_author_title_format'] : NULL;
        $rtsp_options['rtseo_archive_title_format'] = isset( $_POST['rtseo_archive_title_format'] ) ? $_POST['rtseo_archive_title_format'] : NULL;
        $rtsp_options['rtseo_custom_taxonomy_title_format'] = isset( $_POST['rtseo_custom_taxonomy_title_format'] ) ? $_POST['rtseo_custom_taxonomy_title_format'] : NULL;
        $rtsp_options['rtseo_tag_title_format'] = isset( $_POST['rtseo_tag_title_format'] ) ? $_POST['rtseo_tag_title_format'] : NULL;
        $rtsp_options['rtseo_search_title_format'] = isset( $_POST['rtseo_search_title_format'] ) ? $_POST['rtseo_search_title_format'] : NULL;
        $rtsp_options['rtseo_description_format'] = isset( $_POST['rtseo_description_format'] ) ? $_POST['rtseo_description_format'] : NULL;
        $rtsp_options['rtseo_404_title_format'] = isset( $_POST['rtseo_404_title_format'] ) ? $_POST['rtseo_404_title_format'] : NULL;
        $rtsp_options['rtseo_paged_format'] = isset( $_POST['rtseo_paged_format'] ) ? $_POST['rtseo_paged_format'] : NULL;
        $rtsp_options['rtseo_use_categories'] = isset( $_POST['rtseo_use_categories'] ) ? $_POST['rtseo_use_categories'] : NULL;
        $rtsp_options['rtseo_dynamic_postspage_keywords'] = isset( $_POST['rtseo_dynamic_postspage_keywords'] ) ? $_POST['rtseo_dynamic_postspage_keywords'] : NULL;
        $rtsp_options['rtseo_remove_category_rel'] = isset( $_POST['rtseo_remove_category_rel'] ) ? $_POST['rtseo_remove_category_rel'] : NULL;
        $rtsp_options['rtseo_category_noindex'] = isset( $_POST['rtseo_category_noindex'] ) ? $_POST['rtseo_category_noindex'] : NULL;
        $rtsp_options['rtseo_archive_noindex'] = isset( $_POST['rtseo_archive_noindex'] ) ? $_POST['rtseo_archive_noindex'] : NULL;
        $rtsp_options['rtseo_tags_noindex'] = isset( $_POST['rtseo_tags_noindex'] ) ? $_POST['rtseo_tags_noindex'] : NULL;
        $rtsp_options['rtseo_generate_descriptions'] = isset( $_POST['rtseo_generate_descriptions'] ) ? $_POST['rtseo_generate_descriptions'] : NULL;
        $rtsp_options['rtseo_cap_cats'] = isset( $_POST['rtseo_cap_cats'] ) ? $_POST['rtseo_cap_cats'] : NULL;
        $rtsp_options['rtseo_website_api_key'] = isset( $_POST['rtseo_website_api_key'] ) ? $_POST['rtseo_website_api_key'] : NULL;
        $rtsp_options['rtseo_post_meta_tags'] = isset( $_POST['rtseo_post_meta_tags'] ) ? $_POST['rtseo_post_meta_tags'] : NULL;
        $rtsp_options['rtseo_page_meta_tags'] = isset( $_POST['rtseo_page_meta_tags'] ) ? $_POST['rtseo_page_meta_tags'] : NULL;
        $rtsp_options['rtseo_home_meta_tags'] = isset( $_POST['rtseo_home_meta_tags'] ) ? $_POST['rtseo_home_meta_tags'] : NULL;
        $rtsp_options['rtseo_home_google_site_verification_meta_tag'] = isset( $_POST['rtseo_home_google_site_verification_meta_tag'] ) ? $_POST['rtseo_home_google_site_verification_meta_tag'] : NULL;
        $rtsp_options['rtseo_home_bing_site_verification_meta_tag'] = isset( $_POST['rtseo_home_bing_site_verification_meta_tag'] ) ? $_POST['rtseo_home_bing_site_verification_meta_tag'] : NULL;
        $rtsp_options['rtseo_home_yahoo_site_verification_meta_tag'] = isset( $_POST['rtseo_home_yahoo_site_verification_meta_tag'] ) ? $_POST['rtseo_home_yahoo_site_verification_meta_tag'] : NULL;

        $rtsp_options['rtseo_custom_header'] = isset( $_POST['rtseo_custom_header'] ) ? $_POST['rtseo_custom_header'] : NULL;
        $rtsp_options['rtseo_custom_footer'] = isset( $_POST['rtseo_custom_footer'] ) ? $_POST['rtseo_custom_footer'] : NULL;
        $rtsp_options['rtseo_ganalytics_ID'] = isset( $_POST['rtseo_ganalytics_ID'] ) ? $_POST['rtseo_ganalytics_ID'] : NULL;
        $rtsp_options['rtseo_statcounter_security'] = isset( $_POST['rtseo_statcounter_security'] ) ? $_POST['rtseo_statcounter_security'] : NULL;
        $rtsp_options['rtseo_statcounter_project'] = isset( $_POST['rtseo_statcounter_project'] ) ? $_POST['rtseo_statcounter_project'] : NULL;


        $rtsp_options['rtseo_ex_pages'] = isset( $_POST['rtseo_ex_pages'] ) ? $_POST['rtseo_ex_pages'] : NULL;
        $rtsp_options['rtseo_use_tags_as_keywords'] = isset( $_POST['rtseo_use_tags_as_keywords'] ) ? $_POST['rtseo_use_tags_as_keywords'] : NULL;

        $rtsp_options['rtseo_search_noindex'] = isset( $_POST['rtseo_search_noindex'] ) ? $_POST['rtseo_search_noindex'] : NULL;
        $rtsp_options['rtseo_dont_use_excerpt'] = isset( $_POST['rtseo_dont_use_excerpt'] ) ? $_POST['rtseo_dont_use_excerpt'] : NULL;
        $rtsp_options['rtseo_dont_use_desc_for_excerpt'] = isset( $_POST['rtseo_dont_use_desc_for_excerpt'] ) ? $_POST['rtseo_dont_use_desc_for_excerpt'] : NULL;
        $rtsp_options['rtseo_shorten_slugs'] = isset( $_POST['rtseo_shorten_slugs'] ) ? true : false;
        $rtsp_options['rtseo_attachments'] = isset( $_POST['rtseo_attachments'] ) ? true : false;
        $rtsp_options['rtseo_publ_warnings'] = isset( $_POST['rtseo_publ_warnings'] ) ? $_POST['rtseo_publ_warnings'] : 0;

        if (update_option('rtseop_options', $rtsp_options));

        if (function_exists('wp_cache_flush'))
        {
          wp_cache_flush();
        }
      }

    }

?>
<?php if ($message) : ?>
  <div id="message" class="updated fade">
    <p>
      <?php echo $message; ?>
    </p>
  </div>
<?php endif; ?>
  <div id="dropmessage" class="updated" style="display:none;"></div>
    <style type="text/css">
    .postbox-container { min-width: 100% !important; }
  </style>
  <div class="wrap">

      <div>
          <div id="icon-options-general" class="icon32"><br /></div>
          <h2 class="ui dividing header">
          <?php _e('Realtime SEO Settings', 'rt_seo'); ?>
          </h2>
      </div><br>

    <div style="clear:both;"></div>
<script type="text/javascript">

function toggleVisibility (blockId, hide )
{
    if (jQuery('#' +blockId).css('display') == 'none')
        {
            jQuery('#' +blockId).animate({height: 'show'}, 500);
        }
    else
        {
            jQuery('#' +blockId).animate({height: 'hide'}, 500);
        }
}
</script>
    <form name="dofollow" action="" method="post">

<?php

$rtsp_options = get_option('rtseop_options');


$this->admin_settings_advanced();

$this->sitemap_settings_advanced();

?>

      <p class="submit">
        <input type="hidden" name="action" value="rtseo_update" />
        <input type="hidden" name="nonce-rtseop" value="<?php echo esc_attr(wp_create_nonce('rtseopack')); ?>" />
        <input type="hidden" name="page_options" value="rtseo_home_description" />
        <input type="submit" class='medium ui secondary button' name="Submit" value="<?php _e('Update Options', 'rt_seo')?> &raquo;" />
        <input type="submit" class='medium ui button' name="Submit_Default" value="<?php _e('Reset Settings to Defaults', 'rt_seo')?> &raquo;" />
      </p>
    </form>
    <script type="text/javascript">
      //<![CDATA[
      jQuery(document).ready( function($) {
        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
        postboxes.add_postbox_toggles('rt_seo_settings');

        var match;
        if( match = window.location.hash.match(/rtseo\S+/) ){
          $('#'+match[0]).parents('.postbox').removeClass('closed');
          $('#'+match[0]+'_tip').show();
        }
      });
      //]]>
    </script>
  </div>
  <?php
  } // options_panel

	function done_post_install_notice(){
		global $pagenow;
		if ( ( $pagenow == 'index.php' ) &&  get_option( 'done_post_install', false )) {

			echo '<div class="notice notice-success is-dismissible">
				<p>Realtime SEO Pack successfuly configured!</p>
				<p>Click on <a href="options-general.php?page=rt_seo_pack">Realtime SEO Pack</a> to edit settings.</p>
				</div>';
				delete_option( 'done_post_install' );

		}
	}


  function get_adjacent_post_where( $sql ) {
    global $post;

    $affected_post_types = apply_filters( 'sg_get_adjacent_post_where_post_types', array( 'page' ) );

    if( array_search( $post->post_type, $affected_post_types ) !== FALSE) {
      $ids = implode( ',', $ids );
      $sql .= ' AND p.ID NOT IN ('.$ids.')';
    }

    return $sql;
  }



  function post_class( $classes ) {
    foreach( $classes AS $key => $item ) {
      if( $item == 'hentry' ) {
        unset( $classes[$key] );
      }
    }
    return $classes;
  }

  function replace_attachment_links( $content ) {
    global $rtsp_options;
    if( isset($rtsp_options['rtseo_attachments']) && !$rtsp_options['rtseo_attachments'] ) {
      return $content;
    }

    global $wpdb;

    $content = preg_replace_callback( '~<a[^>]*?href="(.*?)"[^>]*?rel=".*?wp-att-(\d+).*?"[^>]*?>\s*?<img[^>]*?src="(.*?)"[^>]*?class=".*?wp-image-(\d+).*?"[^>]*?>\s*?</a>~', array( $this, 'replace_attachment_links_callback' ), $content );
    return $content;
  }




  function replace_attachment_links_callback( $aMatch ) {
    if( $aMatch[4] == $aMatch[2] ) {
      $aMatch[0] = str_replace( $aMatch[1], preg_replace( '~-\d{3,4}x\d{3,4}(\.\S{3,4})$~', '$1', $aMatch[3]), $aMatch[0] );
    }

    return $aMatch[0];
  }




  function script_permalink_replacement( $data ){

    $permalink = $this->curPageURL();

    if( !empty($permalink) ){
      $data = str_replace('%permalink%', $permalink, $data );
    }

    return $data;
  }


  function script_header_content(){
    global $rtsp_options;

    if( isset( $rtsp_options['rtseo_custom_header'] ) && !empty( $rtsp_options['rtseo_custom_header'] ) ){

      $data = $this->script_permalink_replacement( $rtsp_options['rtseo_custom_header'] );
      echo stripcslashes( $data ) . "\n";
    }
  }



  function script_footer_content(){
    global $rtsp_options;

    if( isset( $rtsp_options['rtseo_custom_footer'] ) && !empty( $rtsp_options['rtseo_custom_footer'] ) ){

      $data = $this->script_permalink_replacement( $rtsp_options['rtseo_custom_footer'] );
      echo stripcslashes($data) . "\n";
    }

    if( isset( $rtsp_options['rtseo_ganalytics_ID'] ) && !empty( $rtsp_options['rtseo_ganalytics_ID'] ) ){
      echo stripcslashes("<script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

              ga('create', '".$rtsp_options['rtseo_ganalytics_ID']."', 'auto');
              ga('send', 'pageview');

            </script>") . "\n";

    }

    if( isset( $rtsp_options['rtseo_statcounter_security'] ) && !empty( $rtsp_options['rtseo_statcounter_security'] )
        && isset( $rtsp_options['rtseo_statcounter_project'] ) && !empty( $rtsp_options['rtseo_statcounter_project'] )){
      echo stripcslashes('<!-- Start of StatCounter Code for Default Guide -->
            <script type="text/javascript">
            var sc_project='.$rtsp_options['rtseo_statcounter_project'].';
            var sc_invisible=1;
            var sc_security="'.$rtsp_options['rtseo_statcounter_security'].'";
            var sc_https=1;
            var scJsHost = (("https:" == document.location.protocol) ?
            "https://secure." : "http://www.");
            document.write("<sc"+"ript type=\'text/javascript\' src=\'" +
            scJsHost+
            "statcounter.com/counter/counter.js\' defer></"+"script>");
            </script>
            <noscript><div class="statcounter"><a style="cursor:pointer;" title="free hit
            counter" href="http://statcounter.com/free-hit-counter/"
            target="_blank"><img class="statcounter"
            src="//c.statcounter.com/'.$rtsp_options['rtseo_statcounter_project'].'/0/'.$rtsp_options['rtseo_statcounter_security'].'/1/"
            alt="free hit counter"></a></div></noscript>
            <!-- End of StatCounter Code for Default Guide -->') . "\n";
    }
  }




  function manage_category_columns( $columns ){
    add_action('admin_footer', array($this,'manage_category_rtseo_title_js') );

    $new_columns  = array_slice($columns, 0, 2)
                  + array('rtseo_title' => "SEO Title")
                  + array_slice($columns, 2);

    return $new_columns;
  }




  function manage_category_custom_columns( $content, $column_name, $term_id ){
    if( $column_name != 'rtseo_title' ){
      return $content;
    }

    $category_titles = get_option('rtseop_category_titles');
    $value = ( isset($category_titles[$term_id]) && strlen(trim($category_titles[$term_id])) > 0 ) ? $category_titles[$term_id] : '';

    $content .= "<input class='rtseo_title' type='text' name='rtseo_title[$term_id]' value='$value'>";

    return $content;
  }




  function manage_category_process_action(){
    if( !isset( $_POST['rt_seo_category_update'] ) ){
      return;
    }

    $seo_titles = $_POST['rtseo_title'];
    if( isset($seo_titles) && !empty($seo_titles) ){
      $category_titles = get_option('rtseop_category_titles');

      if( !$category_titles){
        $category_titles = array();
      }

      foreach($seo_titles as $term_id => $title ){
        if(  strlen(trim($title)) > 0 ){
          $category_titles[$term_id] = $title;
        }
      }

      update_option('rtseop_category_titles',$category_titles);
    }

    //clear after process,
    $_POST = array();
  }


  function manage_category_rtseo_title_js(){
  ?>
  <script type="text/javascript">
    function rtseo_show_update_button(){
      jQuery("input.rt_seo_category_update").show();

      jQuery(window).bind('beforeunload', function(){
        return 'Data you have entered are not be saved yet. Are you sure you want to leave?';
      });
    }

    jQuery(document).ready( function(){

      var update_rtseo_title_button = "<input class='button button-primary rt_seo_category_update' type='submit' name='rt_seo_category_update' value='Save SEO Titles' style='display:none' />";
      jQuery("div.actions").append(update_rtseo_title_button);

      jQuery("input.rtseo_title").keydown( rtseo_show_update_button );
      jQuery("input.rtseo_title").change( rtseo_show_update_button );

      jQuery(".rt_seo_category_update").click( function() {
        jQuery(window).unbind('beforeunload');
      });


    });

  </script>

  <style>
    input.rtseo_title{
      width: 100%;
    }
  </style>

  <?php
  }




}
}

$rtseo = new RT_SEO_Pack();
