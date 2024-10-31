<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 



class SGEOP_Page_Install {
	
	protected $tag = 'admin_menu';

	public function run() {
		$menu_hook = add_submenu_page( null, 'SGSEOPINSTALL', 'SGSEOPINSTALL', 'manage_options', 'rtseop_install', array(
				$this,
				'content'
			) );

		add_action( 'load-' . $menu_hook, array( $this, 'enqueue_install_assets' ) );
	}

	public function enqueue_install_assets() {
		global $wp_scripts;
		wp_enqueue_script( 'rtseop-install-js', plugins_url( 'assets/js/install.js', dirname(__FILE__ ) ) );
		wp_enqueue_script( 'rtseop-install-js2', plugins_url( 'assets/js/modal.min.js', dirname(__FILE__ ) ) );
		wp_enqueue_script( 'rtseop-install-js3', plugins_url( 'assets/js/semantic.min.js', dirname(__FILE__ ) ) );
		wp_enqueue_style( 'rtseop-install-css3', plugins_url( 'assets/css/semantic.min.css', dirname(__FILE__ ) ) );
	}

	public function content() {
    global $rtsp_options;
		// Check nonce
		$installer_nonce = ( isset( $_GET['rtseop_install_nonce'] ) ? $_GET['rtseop_install_nonce'] : '' );
		if ( ! wp_verify_nonce( $installer_nonce, 'seop-install-nonce' ) ) {
			wp_die( 'Woah! It looks like something else tried to run the SG SEO for WordPress installation wizard! We were able to stop them, nothing was lost.' );
		}
		?>
		<style>

</style>
            <h1 class="ui center aligned dividing header"><i class="magic icon"></i>Configuration wizard</h1>

				<div id="blur" style="height:100%;">
					<div id="step_1" class="blurred" style="height:100%;display:block;">
						<h3 class="ui center aligned header"><?php _e('Step 1 of 9', 'rt_seo')?></h3>

                        <div class="ui container">
                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('HomePage Title:', 'rt_seo')?></h3>
    									<p>
    									  <?php _e('Here you can add a Title of your Home page. Title tags are displayed on search engine results pages (SERPs) as the clickable headline for a given result, and are important for usability, SEO, and social sharing. <a href = "https://seoguarding.com/kb/seo-title-examples/" target="_blank">More information</a>', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <input type="text" id="rtseo_home_title" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_home_title']))?>" />
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Home Page Description:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('The META description of your homepage. The meta description will then appear under your page’s URL in the search results. This is also known as a snippet. <a href = "https://seoguarding.com/kb/right-meta-description/" target ="_blank">More information</a>', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea style="resize: none;" id="rtseo_home_description"><?php echo esc_attr(stripcslashes($rtsp_options['rtseo_home_description']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('HomePage Keywords (separate with commas):', 'rt_seo')?></h3>
    									<p>
    										<?php _e('Meta Keywords are a specific type of meta tag that appear in the HTML code of a Web page and help tell search engines what the topic of the page is. <a href = "https://seoguarding.com/kb/meta-keywords/" target ="_blank">More information</a>', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea style="resize: none;" id="rtseo_home_keywords"><?php echo esc_attr(stripcslashes($rtsp_options['rtseo_home_keywords'])); ?></textarea>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('No Title Warning:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("The title tag is a very strong signal for search bots to understand what the page is all about, so you should use this factor as effectively as possible. Enable this option and our plugin will warn you about the error if you forget to specify the title for the page. Default: checked.", 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned center aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_publ_warnings" <?php if ( $rtsp_options['rtseo_publ_warnings'] == 1 ) echo 'checked="yes"'; ?> value="1">
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
                                        <h3 class="ui dividing header"><?php _e('Disable ads:', 'rt_seo')?></h3>
    									<p>
    										With this feature you can use code like <tt>!get_option('rt_seo_ads_disabled')</tt> in your <a href="https://wordpress.org/plugins/widget-logic/" target="_blank">Widget Logic</a> conditions to make all ad widgets disappear at once.
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
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

                        </div>
					</div>



					<div id="step_2" style="height:100%;display:none;">
                        <div class="ui container">
						  <h3 class="ui center aligned header"><?php _e('Step 2 of 9', 'rt_seo')?></h3>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e(' Page URL / Post Slug:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('This feature will automatically shorten the page URL so that the URL is not too long. You can also edit your URLs later in a manual mode. <a href = "https://seoguarding.com/kb/correct-url-address/" target ="_blank">More information</a>', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_shorten_slugs" <?php if( isset($rtsp_options['rtseo_shorten_slugs']) && $rtsp_options['rtseo_shorten_slugs'] ) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Canonical URLs:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("This feature will help you to avoid Google penalty for duplicate content. Our plugin will write canonical URLs automatically for all WordPress pages. <a href='https://seoguarding.com/kb/canonical-urls/' target='_blank'>More information</a>.", 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_can" <?php if ($rtsp_options['rtseo_can']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Redirect Attachment Pages to Original Post or Page:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("WordPress often creates trash pages with addresses like this: <i>http://yoursite.com/?attachment{id}</i>. Google considers these attachments separate pages, so instead of 3-4 posts or pages you will see in Google index hundreds of pages you didn’t know about. We recommend that you enable this feature.", 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_attachments" <?php if ($rtsp_options['rtseo_attachments']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Enable shortlinks in header:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("We don't recommend using the Wordpress <a href='http://microformats.org/wiki/rel-shortlink' target= '_blank'>shortlinks</a> if you use permalinks on your website. You might want to use third party link shortening services instead.", 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_shortlinks" <?php if ($rtsp_options['rtseo_shortlinks']) echo 'checked="checked"'; ?>/>
            										<label></label>
            									  </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
					</div>



					<div id="step_3" style="height:100%;display:none;">
                        <div class="ui container">
                            <h3 class="ui center aligned header"><?php _e('Step 3 of 9', 'rt_seo')?></h3>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Enable hAtom Microformat:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("hAtom is a microformat for identifying semantic information in weblog posts and practically any other place Atom may be used, such as news articles. hAtom content is easily added to most blogs by simple modifications to the blog’s template definitions”. This microformat is implemented on any site by adding it’s specific classes to markup. In this case classes aren’t used for styling elements but for highlighting the elements of microformat.", 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_hentry" <?php if ($rtsp_options['rtseo_hentry']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Use Categories for META Keywords:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('Enable this feature for our plugin to use keywords categories as keywords for an article automatically.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_use_categories" <?php if ($rtsp_options['rtseo_use_categories']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Use Tags for META Keywords:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('Enable this feature for our plugin to use Tags as keywords for an article automatically.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_use_tags_as_keywords" <?php if ($rtsp_options['rtseo_use_tags_as_keywords']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Generate Keywords for Posts and Pages:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('Enable this feature to generate keywords automatically.', 'rt_seo') ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_dynamic_postspage_keywords" <?php if ($rtsp_options['rtseo_dynamic_postspage_keywords']) echo 'checked="checked"'; ?>/>
            										<label></label>
            									  </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


	                   </div>
					</div>





					<div id="step_4" style="height:100%;display:none;">
                        <div class="ui container">
						  <h3 class="ui center aligned header"><?php _e('Step 4 of 9', 'rt_seo')?></h3>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Rewrite Titles:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("Note that this is all about the title tag. This is what you see in your browser's window title bar. This is NOT visible on a page, only in the window title bar and of course in the source. If set, all page, post, category, search and archive page titles get rewritten. You can specify the format for most of them. For example: The default templates puts the title tag of posts like this: Blog Archive >> Blog Name >> Post Title (maybe I've overdone slightly). This is far from optimal. With the default post title format, Rewrite Title rewrites this to Post Title | Blog Name. If you have manually defined a title (in one of the text fields for Real Time SEO Plugin input) this will become the title of your post in the format string.", 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_rewrite_titles" <?php if ($rtsp_options['rtseo_rewrite_titles']) echo 'checked="checked"'; ?> onclick="toggleVisibilityInstall('rtseo_rewrite_titles_options');" />
            										<label></label>
            									  </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <div style="display:<?php if ($rtsp_options['rtseo_rewrite_titles']) echo 'block'; else echo 'none'; ?>" id="rtseo_rewrite_titles_options">
                            <div class="ui segment">

                                <div class="ui grid">
                                    <div class="eleven wide column">
                                        <div class="ui grid">
                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Post Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_post_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_post_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_post_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Custom Post Type Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_custom_post_title_format');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_custom_post_title_format_val" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_custom_post_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Page Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_page_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_page_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_page_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Category Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_category_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_category_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_category_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Author Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_author_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_author_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_author_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Archive Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_archive_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_archive_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_archive_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Tag Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_tag_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_tag_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_tag_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Custom taxonomy Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_custom_taxonomy_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_custom_taxonomy_title_format" value="<?php if (isset($rtsp_options['rtseo_custom_taxonomy_title_format'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_custom_taxonomy_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Search Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_search_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_search_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_search_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Description Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_description_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_description_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_description_format'])); ?>" />
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('404 Title Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_404_title_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_404_title_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_404_title_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                            <div class="ui middle aligned eight wide column">
                                                <h4 class="ui header right aligned"><?php _e('Paged Format:', 'rt_seo')?>
                                                    <a style="cursor:pointer;font-weight:bold;" title="<?php _e('Click for Help!', 'rt_seo')?>" onclick="toggleVisibilityInstall('rtseo_paged_format_tip');">
                                                        <i class="question circle icon"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="ui middle aligned eight wide column">
                                                <div class="ui middle aligned form full_h">
                                                      <div class="field">
                                                            <input size="59" style="width: 100%;" id="rtseo_paged_format" value="<?php echo esc_attr(stripcslashes($rtsp_options['rtseo_paged_format'])); ?>"/>
                                                      </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="five wide column">

            								<div>
                                                <h5 class="ui dividing header"><?php _e('Click on <i class="question circle icon"></i>to get help', 'rt_seo'); ?></h5>
            								</div>
            								<div style="display:none" id="rtseo_post_title_format_tip">
            									<?php
            									_e('<br><b>The following macros are supported:</b>', 'rt_seo');
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
            								<div style="display:none" id="rtseo_custom_post_title_format">
            									<?php
            									_e('<br><b>The following macros are supported:</b>', 'rt_seo');
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
            								<div style="display:none" id="rtseo_page_title_format_tip">
            									<?php
            									_e('<br><b>The following macros are supported:</b>', 'rt_seo');
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
            							<div style="display:none" id="rtseo_category_title_format_tip">
            								<?php
            								_e('<br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%category_title% - The original title of the category', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%category_description% - The description of the category', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>
            							<div style="display:none" id="rtseo_author_title_format_tip">
            								<?php
            								_e('<br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%author% - Author name (display name)"', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%author_firstname% - Author first name"', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%author_lastname% - Author last name"', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>
            							<div style="display:none" id="rtseo_archive_title_format_tip">
            								<?php
            								_e('<br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%date% - The original archive title given by wordpress, e.g. "2007" or "2007 August"', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>
            							<div style="display:none" id="rtseo_tag_title_format_tip">
            								<?php
            								_e('<br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%tag% - The name of the tag', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>
            							<div style="display:none" id="rtseo_custom_taxonomy_title_format_tip">
            								<?php
            								_e('<br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%tax_title% - Your actual taxonomy category title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%tax_type_title% - Your taxonomy title', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>
            							<div style="display:none" id="rtseo_search_title_format_tip">
            								<?php
            								_e('<br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%search% - What was searched for', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>
            							<div style="display:none" id="rtseo_description_format_tip">
            								<?php
            								_e('<br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%description% - The original description as determined by the plugin, e.g. the excerpt if one is set or an auto-generated one if that option is set', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%wp_title% - The original wordpress title, e.g. post_title for posts', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%page% - Page number for paged category archives', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>
            							<div style="display:none" id="rtseo_404_title_format_tip">
            								<?php
            								_e('<br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%blog_title% - Your blog title', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%blog_description% - Your blog description', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%request_url% - The original URL path, like "/url-that-does-not-exist/"', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%request_words% - The URL path in human readable form, like "Url That Does Not Exist"', 'rt_seo'); echo('</li>');
            								echo('<li>'); _e('%404_title% - Additional 404 title input"', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>
            							<div style="display:none" id="rtseo_paged_format_tip">
            								<?php
            								_e('<br>This string gets appended/prepended to titles when they are for paged index pages (like home or archive pages).', 'rt_seo');
            								_e('<br><br><b>The following macros are supported:</b>', 'rt_seo');
            								echo('<ul>');
            								echo('<li>'); _e('%page% - The page number', 'rt_seo'); echo('</li>');
            								echo('</ul>');
            								?>
            							</div>


                                    </div>
                               </div>

                            </div>


					   </div>
					   </div>
					</div>




					<div id="step_5" style="height:100%;display:none;">
                        <div class="ui container">

                            <h3 class="ui center aligned header"><?php _e('Step 5 of 9', 'rt_seo')?></h3>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Remove Category rel attribute for validation:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('Check this if you want to remove attribute rel from links to categories. Useful for validation.', 'rt_seo') ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_remove_category_rel" <?php if ($rtsp_options['rtseo_remove_category_rel']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Use noindex for Categories:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('Check this for excluding category pages from being crawled. Useful for avoiding duplicate content.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_category_noindex" <?php if ($rtsp_options['rtseo_category_noindex']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Use noindex for Tag Archives:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('Check this for excluding tag pages from being crawled. Useful for avoiding duplicate content.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_tags_noindex" <?php if ($rtsp_options['rtseo_tags_noindex']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Use noindex for Search Results:', 'rt_seo')?></h3>
    									<p>
    										<?php _e('Check this for excluding search results from being crawled. Useful for avoiding duplicate content.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_search_noindex" <?php if ($rtsp_options['rtseo_search_noindex']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Autogenerate Descriptions:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("Check this and your META descriptions will get autogenerated if there's no excerpt.", 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_generate_descriptions" <?php if ($rtsp_options['rtseo_generate_descriptions']) echo 'checked="checked"'; ?>/>
            										<label></label>
            									  </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

	                   </div>
					</div>





					<div id="step_6" style="height:100%;display:none;">
                        <div class="ui container">

                            <h3 class="ui center aligned header"><?php _e('Step 6 of 9', 'rt_seo')?></h3>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Exclude Pages:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("Enter any comma separated pages here to be excluded by Realtime SEO Pack.  This is helpful when using plugins which generate their own non-WordPress dynamic pages.  Ex: <em>/forum/,/contact/</em>  For instance, if you want to exclude the virtual pages generated by a forum plugin, all you have to do is give forum or /forum or /forum/ or and any URL with the word \"forum\" in it, such as http://mysite.com/forum or http://mysite.com/forum/someforumpage will be excluded from Realtime SEO.", 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="62" rows="8" style="resize: none;" id="rtseo_ex_pages"><?php if( isset( $rtsp_options['rtseo_ex_pages'] ) ) echo esc_attr(stripcslashes($rtsp_options['rtseo_ex_pages']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Additional Post Headers:', 'rt_seo')?></h3>
    									<p>
    									  <?php
    									  _e('What you enter here will be copied verbatim to your header on post pages. You can enter whatever additional headers you want here, even references to stylesheets.', 'rt_seo');
    									  echo '<br/>';
    									  _e('NOTE: This field currently only support meta tags.', 'rt_seo');
    									  ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="62" rows="5" style="resize: none;" id="rtseo_post_meta_tags"><?php echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_post_meta_tags']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Additional Page Headers:', 'rt_seo')?></h3>
    									<p>
                                          <?php
                                          _e('What you enter here will be copied verbatim to your header on pages. You can enter whatever additional headers you want here, even references to stylesheets.', 'rt_seo');
                                          echo '<br/>';
                                          _e('NOTE: This field currently only support meta tags.', 'rt_seo');
                                          ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="62" rows="5" style="resize: none;" id="rtseo_page_meta_tags"><?php echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_page_meta_tags']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Additional Home Headers:', 'rt_seo')?></h3>
    									<p>
    									  <?php
    									  _e('What you enter here will be copied verbatim to your header on the home page. You can enter whatever additional headers you want here, even references to stylesheets.', 'rt_seo');
    									  echo '<br/>';
    									  _e('NOTE: This field currently only support meta tags.', 'rt_seo');
    									  ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="62" rows="5" style="resize: none;" id="rtseo_home_meta_tags"><?php echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_home_meta_tags']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

				        </div>
					</div>



					<div id="step_7" style="height:100%;display:none;">
                        <div class="ui container">
						    <h3 class="ui center aligned header"><?php _e('Step 7 of 9', 'rt_seo')?></h3>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Google Verification Meta Tag:', 'rt_seo')?></h3>
    									<p>
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
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="68" rows="7" style="resize: none;" id="rtseo_home_google_site_verification_meta_tag"><?php if( isset( $rtsp_options['rtseo_home_google_site_verification_meta_tag'] ) ) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_home_google_site_verification_meta_tag']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Yahoo Verification Meta Tag:', 'rt_seo')?></h3>
    									<p>
                                          <?php _e('Put your Yahoo site verification tag for your homepage here.', 'rt_seo'); ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="68" rows="3" style="resize: none;" id="rtseo_home_yahoo_site_verification_meta_tag"><?php if( isset( $rtsp_options['rtseo_home_yahoo_site_verification_meta_tag'] ) ) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_home_yahoo_site_verification_meta_tag']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Bing Verification Meta Tag:', 'rt_seo')?></h3>
    									<p>
                                          <?php _e('Put your Bing site verification tag for your homepage here.', 'rt_seo'); ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="68" rows="3" style="resize: none;" id="rtseo_home_bing_site_verification_meta_tag"><?php if( isset( $rtsp_options['rtseo_home_bing_site_verification_meta_tag'] ) ) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_home_bing_site_verification_meta_tag']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Turn off excerpts for descriptions:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("Since Typepad export is containing auto generated excerpts for the most of the time we use this option a lot.", 'rt_seo'); ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_dont_use_excerpt" <?php if ($rtsp_options['rtseo_dont_use_excerpt']) echo "checked=\"1\""; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e('Turn off descriptions for excerpts:', 'rt_seo')?></h3>
    									<p>
    										<?php _e("By default Realtime SEO will show meta description when post excerpt is called in the theme and it's not filled in. Also, if you use Genesis theme with its setting of 'Display post content' for 'Content archives' it will put in meta description instead if no read more tag is found and strip images from it as well.", 'rt_seo'); ?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_dont_use_desc_for_excerpt" <?php if ($rtsp_options['rtseo_dont_use_desc_for_excerpt']) echo "checked=\"1\""; ?>/>
            										<label></label>
            									  </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


					   </div>
					</div>




					<div id="step_8" style="height:100%;display:none;">
                        <div class="ui container">
						    <h3 class="ui center aligned header"><?php _e('Step 8 of 9', 'rt_seo')?></h3>


                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Header code:', 'rt_seo')?></h3>
    									<p>
                                          <?php _e('Insert any code which should be in the &lt;head&gt; section of the site.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="68" rows="3" style="resize: none;" id="rtseo_custom_header"><?php if (isset($rtsp_options['rtseo_custom_header'])) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_custom_header']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Footer code:', 'rt_seo')?></h3>
    									<p>
                                          <?php _e('Insert any code which should be right before the closing &lt;/body&gt; tag on the site.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <textarea cols="68" rows="3" style="resize: none;" id="rtseo_custom_footer"><?php if (isset($rtsp_options['rtseo_custom_footer'])) echo htmlspecialchars(stripcslashes($rtsp_options['rtseo_custom_footer']))?></textarea>
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Google Analytics ID:', 'rt_seo')?></h3>
    									<p>
    									  <?php _e('Enter your google analytics ID. Example: UA-12345678-9', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <input type="text" size="69" id="rtseo_ganalytics_ID" value="<?php if (isset($rtsp_options['rtseo_ganalytics_ID'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_ganalytics_ID']))?>" />
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Statcounter Project ID:', 'rt_seo')?></h3>
    									<p>
    									  <?php _e('Enter your project ID. You can obtain them from Statcounter administation > Project > Reinstall Code > Default Guide. Look for <i>sc_project</i> variable in code.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <input type="text" size="69" id="rtseo_statcounter_project" value="<?php if (isset($rtsp_options['rtseo_statcounter_project'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_statcounter_project']))?>" />
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e('Statcounter Security ID:', 'rt_seo')?></h3>
    									<p>
    									  <?php _e('Enter your security ID. You can obtain them from Statcounter administation > Project > Reinstall Code > Default Guide. Look for <i>sc_security</i> variable in code.', 'rt_seo')?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <input type="text" size="69" id="rtseo_statcounter_security" value="<?php if (isset($rtsp_options['rtseo_statcounter_security'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_statcounter_security']))?>" />
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


					        <input type="hidden" id="nonce-rtseop" value="<?php echo esc_attr(wp_create_nonce('rtseopack')); ?>" />
					        <input type="hidden" id="url" value="<?php echo get_site_url() . '/wp-admin/options-general.php?page=rt_seo_pack'; ?>" />

                        </div>
					</div>




					<div id="step_9" style="height:100%;display:none;">
                        <div class="ui container">
                            <h3 class="ui center aligned header"><?php _e('Step 9 of 9', 'rt_seo')?></h3>


                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e( 'Enable Sitemap', 'rt_seo' )?></h3>
    									<p>
    										<?php _e( 'Check to enable generating sitemap. It will be generated automatically 1 time per 24 hours', 'rt_seo' )?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_enable_sitemap" <?php if ($rtsp_options['rtseo_enable_sitemap']) echo 'checked="checked"'; ?> onclick="toggleVisibilityInstall('rtseo_sitemap_options');" />
            										<label></label>
            									  </div>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <div style="display:<?php if ($rtsp_options['rtseo_enable_sitemap']) echo 'block'; else echo 'none'; ?>" id="rtseo_sitemap_options">

                            <div class="ui segment">
                                <div class="ui grid">
                                    <div class="eight wide column">
                                        <h3 class="ui dividing header"><?php _e( 'Include Archive Pages', 'rt_seo' )?></h3>
    									<p>
    										<?php _e( 'Check to include date archive pages, such as YYYY or YYYY/MM, in your sitemap.', 'rt_seo' )?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_inc_archives" <?php if ($rtsp_options['rtseo_inc_archives']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e( 'Include Author Pages', 'rt_seo' )?></h3>
    									<p>
    										<?php _e( 'Check to include author pages in your sitemap.', 'rt_seo' )?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_inc_authors" <?php if ($rtsp_options['rtseo_inc_authors']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e( 'Empty Author Pages', 'rt_seo' )?></h3>
    									<p>
    										<?php _e( 'Check to include author page in your sitemap if the author(s) has not published any pages or posts yet.', 'rt_seo' )?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_empty_author" <?php if ($rtsp_options['rtseo_empty_author']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e( 'Include Category Pages', 'rt_seo' )?></h3>
    									<p>
    										<?php _e( 'Check to include category pages in your sitemap.', 'rt_seo' )?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_inc_categories" <?php if ($rtsp_options['rtseo_inc_categories']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e( 'Include Tag Pages', 'rt_seo' )?></h3>
    									<p>
    										<?php _e( 'Check to include tag pages in your sitemap.', 'rt_seo' )?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_inc_tags" <?php if ($rtsp_options['rtseo_inc_tags']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e( 'Include Custom Posts', 'rt_seo' )?></h3>
    									<p>
    										<?php _e( 'Check to include custom posts in your sitemap.', 'rt_seo' )?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned center aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
            									  <div class="ui fitted toggle checkbox">
            											<input type="checkbox" id="rtseo_inc_custom_posts" <?php if ($rtsp_options['rtseo_inc_custom_posts']) echo 'checked="checked"'; ?>/>
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
                                        <h3 class="ui dividing header"><?php _e( 'Exclude Pages &amp; Posts', 'rt_seo' )?></h3>
    									<p>
    									  <?php _e( 'IDs of pages and/or posts you do not wish to include in your sitemap separated by commas (\',\'):', 'rt_seo' )?>
    									</p>
                                    </div>
                                    <div class="ui middle aligned eight wide column">
                                        <div class="ui middle aligned form full_h">
                                              <div class="field">
                                                    <input type="text" size="55" id="rtseo_exclude_pages" value="<?php if (isset($rtsp_options['rtseo_exclude_pages'])) echo esc_attr(stripcslashes($rtsp_options['rtseo_exclude_pages']));?>" />
                                              </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


							</div>
                        </div>
					</div>



					<div id="step_10" style="height:100%;display:none;">

                        <br /><br /><br /><br />

						<h1 class="ui center aligned header"><i class="check square outline green icon"></i><?php _e('Realtime SEO Pack successfully configured!', 'rt_seo')?></h1>

                        <br /><br />

					</div>


				</div>



			<div class="ui container">
                <div class="ui grid">
                    <div class="ui center aligned sixteen wide column">
                        <div class="ui vertical segment">
						      <button onclick="prev()" id="prev" style="display:none;" class="medium ui secondary button">B A C K</button>
						      <button onclick="window.location.href='/wp-admin/admin.php?page=rt_seo_pack'" id="skip" class="medium ui button">C A N C E L</button>
						      <button onclick="next()" id="next" class="medium ui secondary button">N E X T</button>
                        </div>
                    </div>
                </div>
			</div>

		<?php


	}

}

$rtseo_install = new SGEOP_Page_Install();
