<?php

if (!class_exists('RT_SEO_Import')) {
	class RT_SEO_Import extends RT_SEO_Helper
	{
		
		public $platforms = array();
		
		function __construct() {
			
			parent::__construct();
			
			$this->platforms = array(
				'Real-Time SEO' => array(
					'Custom Doctitle'  => 'rtseo_title',
					'META Description' => 'rtseo_description',
					'META Keywords' => 'rtseo_keywords',
					'noindex'    => 'rtseo_noindex',
				),
				'Add Meta Tags' => array(
					'Custom Doctitle'  => '_amt_title',
					'META Description' => '_amt_description',
					'META Keywords'    => '_amt_keywords',
				),
				'All in One SEO Pack' => array(
					'Custom Doctitle'  => '_aioseop_title',
					'META Description' => '_aioseop_description',
					'META Keywords'    => '_aioseop_keywords',
				),
				'Greg\'s High Performance SEO' => array(
					'Custom Doctitle'  => '_ghpseo_secondary_title',
					'META Description' => '_ghpseo_alternative_description',
					'META Keywords'    => '_ghpseo_keywords',
				),
				'Headspace2' => array(
					'Custom Doctitle'  => '_headspace_page_title',
					'META Description' => '_headspace_description',
					'META Keywords'    => '_headspace_keywords',
					'Custom Scripts'   => '_headspace_scripts',
				),
				'Infinite SEO' => array(
					'Custom Doctitle'  => '_wds_title',
					'META Description' => '_wds_metadesc',
					'META Keywords'    => '_wds_keywords',
					'noindex'          => '_wds_meta-robots-noindex',
					'nofollow'         => '_wds_meta-robots-nofollow',
					'Canonical URI'    => '_wds_canonical',
					'Redirect URI'     => '_wds_redirect',
				),
				'Jetpack Advanced SEO' => array(
					'META Description' => 'advanced_seo_description',
				),
				'Meta SEO Pack' => array(
					'META Description' => '_msp_description',
					'META Keywords'    => '_msp_keywords',
				),
				'Platinum SEO' => array(
					'Custom Doctitle'  => 'title',
					'META Description' => 'description',
					'META Keywords'    => 'keywords',
				),
				'SEO Title Tag' => array(
					'Custom Doctitle'  => 'title_tag',
					'META Description' => 'meta_description',
				),
				'SEO Ultimate' => array(
					'Custom Doctitle'  => '_su_title',
					'META Description' => '_su_description',
					'META Keywords'    => '_su_keywords',
					'noindex'          => '_su_meta_robots_noindex',
					'nofollow'         => '_su_meta_robots_nofollow',
				),
				'Yoast SEO' => array(
					'Custom Doctitle'  => '_yoast_wpseo_title',
					'META Description' => '_yoast_wpseo_metadesc',
					'META Keywords'    => '_yoast_wpseo_metakeywords',
					'noindex'          => '_yoast_wpseo_meta-robots-noindex',
					'nofollow'         => '_yoast_wpseo_meta-robots-nofollow',
					'Canonical URI'    => '_yoast_wpseo_canonical',
					'Redirect URI'     => '_yoast_wpseo_redirect',
				),
			);

		

		}
		
		public function import_panel() {
		?>
			<div id="main" class="ui main" style="margin-top: 20px; width:99%;max-width: 1100px;">
			<h2 class="ui dividing header">Import</h2>
			<?php	$this->process_form(); ?>

			<div class="ui black segment">
				<p>
					Click "Analyze" for a list of elements you are able to convert, along with the number of records that will be converted. Some platforms do not share similar elements, or store data in a non-standard way. These records will remain unchanged. Any compatible elements will be displayed for your review. Also, some records will be ignored if the post/page in question already contains a record for that particular SEO element in the new platform.
				</p>
			</div>

			<div class="ui black segment">
				<p>
					Click "Convert" to perform the conversion. After the conversion is complete, you will be alerted to how many records were converted, and how many records had to be ignored, based on the criteria above.
				</p>
			</div>

			<form method="post" action="<?php echo admin_url( 'admin.php?page=rt_seo_import' ); ?>">
			<?php

			echo 'Convert inpost SEO data from: ';
			$this->generate_select( 'platform_old', $this->platforms );

			?>

			<div class="bottom-buttons">
				<input type="hidden" name="rt_seo_import_nonce" value="<?php echo wp_create_nonce( 'rt-seo-import' ); ?>">
				<input type="submit" name="analyze" id="analyze" class="ui medium secondary button mrt-top-15" value="Analyze">
				<input type="submit" name="submit"id="submit"  class="ui medium secondary button mrt-top-15" value="Convert">
			</div>
			</form>
			</div>
		<?php
		
		}
			
		public function analyze( $old_platform = '', $new_platform = 'Real-Time SEO' ) {

			global $wpdb;

			$output = new stdClass;

			if ( empty( $this->platforms[ $old_platform ] ) || empty( $this->platforms[ $new_platform ] ) ) {
				$output->WP_Error = 1;
				return $output;
			}

			$output->update   = 0;
			$output->ignore   = 0;
			$output->elements = array();

			foreach ( (array) $this->platforms[ $old_platform ] as $label => $meta_key ) {

				if ( empty( $this->platforms[ $new_platform ][ $label ] ) ) {
					continue;
				}

				$output->elements[] = $label;

				$ignore = 0;

				$update = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s", $meta_key ) );
				$update = count( (array) $update );

				$output->update = $output->update + (int) $update;
				$output->ignore = $output->ignore + (int) $ignore;

			} 

			return $output;

		}


		public function convert( $old_platform = '', $new_platform = 'Real-Time SEO', $delete_old = false ) {

			$output = new stdClass;

			if ( empty( $this->platforms[ $old_platform ] ) || empty( $this->platforms[ $new_platform ] ) ) {
				$output->WP_Error = 1;
				return $output;
			}

			$output->updated = 0;
			$output->deleted = 0;
			$output->ignored = 0;

			foreach ( (array) $this->platforms[ $old_platform ] as $label => $meta_key ) {

				if ( empty( $this->platforms[ $new_platform ][ $label ] ) ) {
					continue;
				}

				$old_key = $this->platforms[ $old_platform ][ $label ];
				$new_key = $this->platforms[ $new_platform ][ $label ];

				$result = $this->meta_key_convert( $old_key, $new_key, $delete_old );
				
				if ( is_wp_error( $result ) ) {
					continue;
				}
				$output->updated = $output->updated + (int) $result->updated;
				$output->ignored = $output->ignored + (int) $result->ignored;

			}

			return $output;

		}


		public function meta_key_convert( $old_key = '', $new_key = '', $delete_old = false ) {

			do_action( 'pre_seodt_meta_key_convert_before', $old_key, $new_key, $delete_old );

			global $wpdb;

			$output = new stdClass;

			if ( ! $old_key || ! $new_key ) {

				$output->WP_Error = 1;
				return $output;

			}

			$exclude = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value != '' AND meta_value != 0", $new_key ) );

			if ( ! $exclude ) {

				$output->updated = $wpdb->update( $wpdb->postmeta, array( 'meta_key' => $new_key ), array( 'meta_key' => $old_key ) );
				$output->deleted = 0;
				$output->ignored = 0;

			} else {
				foreach ( (array) $exclude as $key => $value ) {
					$not_in[] = $value->post_id;
				}
				$not_in = implode( ', ', (array) $not_in );

				$output->updated = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key = %s AND post_id NOT IN ($not_in)", $new_key, $old_key ) );
				$output->deleted = 0;
				$output->ignored = count( $exclude );
			}

			return $output;

		}
			
		public function generate_select( $name, $platforms ) {

			printf( '<select name="%s" class="ui dropdown">', esc_attr( $name ) );
			printf( '<option value="">%s</option>', 'Choose platform:' );
			
			array_shift($platforms);
			
			printf( '<optgroup label="%s">', 'Platforms' );
			foreach ( $platforms as $platform => $data ) {
				$selected = (isset($_REQUEST['platform_old']) && ($_REQUEST['platform_old'] === $platform)) ? 'selected' : '';
				printf( '<option value="%s" %s>%s</option>', esc_attr( $platform ), $selected, esc_html( $platform ) );
			}
			echo '</optgroup>';
			echo '</select>';

		}
		
			
			
		public function process_form() {
			
			if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rt_seo_import_nonce'])) {
				if (!wp_verify_nonce($_POST['rt_seo_import_nonce'], 'rt-seo-import' )) {
					$message_data = array(
						'type' => 'alert',
						'message' => 'Session has expired.',
					);
					$this->PrintIconMessage($message_data);
					return false;
				}

				$args = wp_parse_args( $_REQUEST, array(
					'analyze'      => 0,
					'platform_old' => '',
				) );

				if ( ! $args['platform_old'] ) {
					$message_data = array(
						'type' => 'alert',
						'message' => 'You must choose platform before submitting.',
					);
					$this->PrintIconMessage($message_data);
					return false;
				}


				if ( $args['analyze'] ) {
					$this->analysis_result = $this->analyze( $args['platform_old']);
					
					if ( is_wp_error( $this->analysis_result ) ) {
						$message_data = array(
							'type' => 'alert',
							'message' => 'Something went wrong. Please make your selection and try again',
						);
						$this->PrintIconMessage($message_data);
						return false;
					}
					
					$msg = '<p><b>Compatible Elements:</b></p><ol>';
					foreach ( (array) $this->analysis_result->elements as $element ) {
							$msg .= "<li>$element</li>";
						}
					$msg .= "</ol><p>The analysis found {$this->analysis_result->update}  compatible database records to be converted.</p>";	
					
					$message_data = array(
						'type' => 'info_white',
						'message' => $msg,
					);
					$this->PrintIconMessage($message_data);
					return true;

				}

				$this->conversion_result = $this->convert( $args['platform_old'] );

				if ( is_wp_error( $this->conversion_result ) ) {
					$message_data = array(
						'type' => 'alert',
						'message' => 'Something went wrong. Please make your selection and try again',
					);
					$this->PrintIconMessage($message_data);
					return false;
				}
						
				$updated  = isset( $this->conversion_result->updated ) ? $this->conversion_result->updated : 0;
				$ignored = isset( $this->conversion_result->ignored ) ? $this->conversion_result->ignored : 0;

				$message_data = array(
					'type' => 'ok',
					'message' => "<p><b>$updated</b> records were updated</p><p><b>$ignored</b> records were ignored</p>",
				);
				$this->PrintIconMessage($message_data);
				return true;
			}
		}
	

		
	}
	
	$RT_SEO_Import = new RT_SEO_Import();
	

}


