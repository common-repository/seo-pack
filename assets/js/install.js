var i =1;
var timer;
var id_none;
var id_block;

function prev(){
	if (jQuery('#step_9').css('display') == "block") {
		id_none = jQuery('#step_9');
		id_block = jQuery('#step_8');
		jQuery('#next').text('N E X T');
		toBlur();

	} else if (jQuery('#step_8').css('display') == "block") {
		id_none = jQuery('#step_8');
		id_block = jQuery('#step_7');
		toBlur();

	} else if (jQuery('#step_7').css('display') == "block") {
		id_none = jQuery('#step_7');
		id_block = jQuery('#step_6');
		toBlur();
	} else if (jQuery('#step_6').css('display') == "block") {
		id_none = jQuery('#step_6');
		id_block = jQuery('#step_5');
		toBlur();

	}  else if (jQuery('#step_5').css('display') == "block") {
		id_none = jQuery('#step_5');
		id_block = jQuery('#step_4');
		toBlur();

	} else if (jQuery('#step_4').css('display') == "block") {
		id_none = jQuery('#step_4');
		id_block = jQuery('#step_3');
		toBlur();

	} else if (jQuery('#step_3').css('display') == "block") {
		id_none = jQuery('#step_3');
		id_block = jQuery('#step_2');
		toBlur();
		
	}  else if (jQuery('#step_2').css('display') == "block") {
		id_none = jQuery('#step_2');
		id_block = jQuery('#step_1');
		jQuery('#prev').css('display','none');	
		toBlur();	
	} 
}

function next() {
	if (jQuery('#step_1').css('display') == "block") {
		id_none = jQuery('#step_1');
		id_block = jQuery('#step_2');
		toBlur();
		jQuery('#prev').css('display','inline-block');

	} else if (jQuery('#step_2').css('display') == "block") {
		id_none = jQuery('#step_2');
		id_block = jQuery('#step_3');
		toBlur();

	} else if (jQuery('#step_3').css('display') == "block") {
		id_none = jQuery('#step_3');
		id_block = jQuery('#step_4');
		toBlur();
	} else if (jQuery('#step_4').css('display') == "block") {
		id_none = jQuery('#step_4');
		id_block = jQuery('#step_5');
		toBlur();

	}  else if (jQuery('#step_5').css('display') == "block") {
		id_none = jQuery('#step_5');
		id_block = jQuery('#step_6');
		toBlur();

	} else if (jQuery('#step_6').css('display') == "block") {
		id_none = jQuery('#step_6');
		id_block = jQuery('#step_7');
		toBlur();

	} else if (jQuery('#step_7').css('display') == "block") {
		id_none = jQuery('#step_7');
		id_block = jQuery('#step_8');
		toBlur();
		
	}  else if (jQuery('#step_8').css('display') == "block") {
		id_none = jQuery('#step_8');
		id_block = jQuery('#step_9');
		toBlur();
		jQuery('#next').text('F i n i s h');	
	}  else if (jQuery('#step_9').css('display') == "block") {
		id_none = jQuery('#step_9');
		id_block = jQuery('#step_10');
		toBlur();
		jQuery('#next').hide();		

	if (jQuery('#rtseo_can').attr("checked") == 'checked') {
		rtseo_can = "on";
	} else {
		rtseo_can = "";
	}
	if (jQuery('#rtseo_shortlinks').attr("checked") == 'checked') {
		rtseo_shortlinks = "on";
	} else {
		rtseo_shortlinks = "";
	}
	if (jQuery('#rtseo_hentry').attr("checked") == 'checked') {
		rtseo_hentry = "on";
	} else {
		rtseo_hentry = "";
	}
	if (jQuery('#rtseo_rewrite_titles').attr("checked") == 'checked') {
		rtseo_rewrite_titles = "on";
	} else {
		rtseo_rewrite_titles = "";
	}
	if (jQuery('#rtseo_use_categories').attr("checked") == 'checked') {
		rtseo_use_categories = "on";
	} else {
		rtseo_use_categories = "";
	}
	if (jQuery('#rtseo_dynamic_postspage_keywords').attr("checked") == 'checked') {
		rtseo_dynamic_postspage_keywords = "on";
	} else {
		rtseo_dynamic_postspage_keywords = "";
	}
	if (jQuery('#rtseo_remove_category_rel').attr("checked") == 'checked') {
		rtseo_remove_category_rel = "on";
	} else {
		rtseo_remove_category_rel = "";
	}
	if (jQuery('#rtseo_category_noindex').attr("checked") == 'checked') {
		rtseo_category_noindex = "on";
	} else {
		rtseo_category_noindex = "";
	}
	if (jQuery('#rtseo_tags_noindex').attr("checked") == 'checked') {
		rtseo_tags_noindex = "on";
	} else {
		rtseo_tags_noindex = "";
	}
	if (jQuery('#rtseo_generate_descriptions').attr("checked") == 'checked') {
		rtseo_generate_descriptions = "on";
	} else {
		rtseo_generate_descriptions = "";
	}
	if (jQuery('#rtseo_use_tags_as_keywords').attr("checked") == 'checked') {
		rtseo_use_tags_as_keywords = "on";
	} else {
		rtseo_use_tags_as_keywords = "";
	}
	if (jQuery('#rtseo_search_noindex').attr("checked") == 'checked') {
		rtseo_search_noindex = "on";
	} else {
		rtseo_search_noindex = "";
	}
	if (jQuery('#rtseo_dont_use_excerpt').attr("checked") == 'checked') {
		rtseo_dont_use_excerpt = "on";
	} else {
		rtseo_dont_use_excerpt = "";
	}
	if (jQuery('#rtseo_dont_use_desc_for_excerpt').attr("checked") == 'checked') {
		rtseo_dont_use_desc_for_excerpt = "on";
	} else {
		rtseo_dont_use_desc_for_excerpt = "";
	}
	if (jQuery('#rtseo_shorten_slugs').attr("checked") == 'checked') {
		rtseo_shorten_slugs = "on";
	} else {
		rtseo_shorten_slugs = "";
	}
	if (jQuery('#rtseo_attachments').attr("checked") == 'checked') {
		rtseo_attachments = "on";
	} else {
		rtseo_attachments = "";
	}
	if (jQuery('#rtseo_publ_warnings').attr("checked") == 'checked') {
		rtseo_publ_warnings = "on";
	} else {
		rtseo_publ_warnings = "";
	}
	if (jQuery('#rtseo_inc_custom_posts').attr("checked") == 'checked') {
		rtseo_inc_custom_posts = "on";
	} else {
		rtseo_inc_custom_posts = "";
	}
	if (jQuery('#rtseo_mobile_sitemap').attr("checked") == 'checked') {
		rtseo_mobile_sitemap = "on";
	} else {
		rtseo_mobile_sitemap = "";
	}
	if (jQuery('#rtseo_inc_tags').attr("checked") == 'checked') {
		rtseo_inc_tags = "on";
	} else {
		rtseo_inc_tags = "";
	}
	if (jQuery('#rtseo_inc_categories').attr("checked") == 'checked') {
		rtseo_inc_categories = "on";
	} else {
		rtseo_inc_categories = "";
	}
	if (jQuery('#rtseo_empty_author').attr("checked") == 'checked') {
		rtseo_empty_author = "on";
	} else {
		rtseo_empty_author = "";
	}
	if (jQuery('#rtseo_inc_authors').attr("checked") == 'checked') {
		rtseo_inc_authors = "on";
	} else {
		rtseo_inc_authors = "";
	}
	if (jQuery('#rtseo_inc_archives').attr("checked") == 'checked') {
		rtseo_inc_archives = "on";
	} else {
		rtseo_inc_archives = "";
	}
	if (jQuery('#rtseo_enable_sitemap').attr("checked") == 'checked') {
		rtseo_enable_sitemap = "on";
	} else {
		rtseo_enable_sitemap = "";
	}
	rtseo_home_title = encodeURIComponent(jQuery('#rtseo_home_title').val());
	rtseo_home_description = encodeURIComponent(jQuery('#rtseo_home_description').val());
	rtseo_home_keywords = encodeURIComponent(jQuery('#rtseo_home_keywords').val());
	rtseo_post_title_format = encodeURIComponent(jQuery('#rtseo_post_title_format').val());
	rtseo_custom_post_title_format = encodeURIComponent(jQuery('#rtseo_custom_post_title_format_val').val());
	rtseo_page_title_format = encodeURIComponent(jQuery('#rtseo_page_title_format').val());
	rtseo_category_title_format = encodeURIComponent(jQuery('#rtseo_category_title_format').val());
	rtseo_author_title_format = encodeURIComponent(jQuery('#rtseo_author_title_format').val());
	rtseo_archive_title_format = encodeURIComponent(jQuery('#rtseo_archive_title_format').val());
	rtseo_custom_taxonomy_title_format = encodeURIComponent(jQuery('#rtseo_custom_taxonomy_title_format').val());
	rtseo_tag_title_format = encodeURIComponent(jQuery('#rtseo_tag_title_format').val());
	rtseo_search_title_format = encodeURIComponent(jQuery('#rtseo_search_title_format').val());
	rtseo_description_format = encodeURIComponent(jQuery('#rtseo_description_format').val());
	rtseo_404_title_format = encodeURIComponent(jQuery('#rtseo_404_title_format').val());
	rtseo_paged_format = encodeURIComponent(jQuery('#rtseo_paged_format').val());
	rtseo_post_meta_tags = encodeURIComponent(jQuery('#rtseo_post_meta_tags').val());
	rtseo_page_meta_tags = encodeURIComponent(jQuery('#rtseo_page_meta_tags').val());
	rtseo_home_meta_tags = encodeURIComponent(jQuery('#rtseo_home_meta_tags').val());
	rtseo_home_google_site_verification_meta_tag = encodeURIComponent(jQuery('#rtseo_home_google_site_verification_meta_tag').val());
	rtseo_home_bing_site_verification_meta_tag = encodeURIComponent(jQuery('#rtseo_home_bing_site_verification_meta_tag').val());
	rtseo_home_yahoo_site_verification_meta_tag = encodeURIComponent(jQuery('#rtseo_home_yahoo_site_verification_meta_tag').val());
	rtseo_custom_header = encodeURIComponent(jQuery('#rtseo_custom_header').val());
	rtseo_custom_footer = encodeURIComponent(jQuery('#rtseo_custom_footer').val());
	rtseo_ganalytics_ID = encodeURIComponent(jQuery('#rtseo_ganalytics_ID').val());
	rtseo_statcounter_security = encodeURIComponent(jQuery('#rtseo_statcounter_security').val());
	rtseo_statcounter_project = encodeURIComponent(jQuery('#rtseo_statcounter_project').val());
	rtseo_exclude_pages = encodeURIComponent(jQuery('#rtseo_exclude_pages').val());
	rtseo_ex_pages = encodeURIComponent(jQuery('#rtseo_ex_pages').val());
	nonce_rtseop = jQuery('#nonce-rtseop').val();

    jQuery.ajax({
        type: "POST",
        url: "/wp-admin/options-general.php?page=rt_seo_pack",
        data: "&rtseo_ex_pages=" + rtseo_ex_pages + "&rtseo_statcounter_project=" + rtseo_statcounter_project + "&rtseo_statcounter_security=" + rtseo_statcounter_security + "&rtseo_ganalytics_ID=" + rtseo_ganalytics_ID + "&rtseo_custom_footer=" + rtseo_custom_footer + "&rtseo_custom_header=" + rtseo_custom_header + "&rtseo_home_yahoo_site_verification_meta_tag=" + rtseo_home_yahoo_site_verification_meta_tag + "&rtseo_home_bing_site_verification_meta_tag=" + rtseo_home_bing_site_verification_meta_tag + "&rtseo_home_google_site_verification_meta_tag=" + rtseo_home_google_site_verification_meta_tag + "&rtseo_home_meta_tags=" + rtseo_home_meta_tags + "&rtseo_page_meta_tags=" + rtseo_page_meta_tags + "&rtseo_post_meta_tags=" + rtseo_post_meta_tags + "&rtseo_post_title_format=" + rtseo_post_title_format + "&rtseo_home_keywords=" + rtseo_home_keywords + "&rtseo_home_description=" + rtseo_home_description + "&rtseo_home_title=" + rtseo_home_title + "&rtseo_publ_warnings=" + rtseo_publ_warnings + "&rtseo_attachments=" + rtseo_attachments + "&rtseo_shorten_slugs=" + rtseo_shorten_slugs + "&rtseo_dont_use_desc_for_excerpt=" + rtseo_dont_use_desc_for_excerpt + "&rtseo_dont_use_excerpt=" + rtseo_dont_use_excerpt + "&rtseo_search_noindex=" + rtseo_search_noindex + "&rtseo_use_tags_as_keywords=" + rtseo_use_tags_as_keywords + "&rtseo_generate_descriptions=" + rtseo_generate_descriptions + "&rtseo_tags_noindex=" + rtseo_tags_noindex + "&rtseo_category_noindex=" + rtseo_category_noindex + "&rtseo_remove_category_rel=" + rtseo_remove_category_rel + "&rtseo_dynamic_postspage_keywords=" + rtseo_dynamic_postspage_keywords + "&rtseo_use_categories=" + rtseo_use_categories + "&rtseo_rewrite_titles=" + rtseo_rewrite_titles + "&rtseo_hentry=" + rtseo_hentry + "&rtseo_shortlinks=" + rtseo_shortlinks + "&rtseo_can=" + rtseo_can + "&rtseo_paged_format=" + rtseo_paged_format + "&rtseo_404_title_format=" + rtseo_404_title_format + "&rtseo_description_format=" + rtseo_description_format + "&rtseo_search_title_format=" + rtseo_search_title_format + "&rtseo_tag_title_format=" + rtseo_tag_title_format + "&rtseo_custom_taxonomy_title_format=" + rtseo_custom_taxonomy_title_format + "&rtseo_archive_title_format=" + rtseo_archive_title_format + "&rtseo_author_title_format=" + rtseo_author_title_format + "&rtseo_category_title_format=" + rtseo_category_title_format + "&rtseo_page_title_format=" + rtseo_page_title_format + "&rtseo_custom_post_title_format=" + rtseo_custom_post_title_format + "&rtseo_exclude_pages=" + rtseo_exclude_pages + "&rtseo_inc_custom_posts=" + rtseo_inc_custom_posts + "&rtseo_mobile_sitemap=" + rtseo_mobile_sitemap + "&rtseo_inc_tags=" + rtseo_inc_tags + "&rtseo_inc_categories=" + rtseo_inc_categories + "&rtseo_empty_author=" + rtseo_empty_author + "&rtseo_inc_authors=" + rtseo_inc_authors + "&rtseo_inc_archives=" + rtseo_inc_archives + "&rtseo_enable_sitemap=" + rtseo_enable_sitemap + "&nonce-rtseop=" + nonce_rtseop + "&Submit=ok&action=rtseo_update&done_post_install=1",		
        success: function(response){	
			setTimeout(function () {
			   window.location.href = "/wp-admin/admin.php?page=rt_seo_pack"; 
			}, 2000);

        },
		error: function(response){
			setTimeout(function () {
			   window.location.href = "/wp-admin/admin.php?page=rt_seo_pack";
			}, 2000);
		},
    });		
		
	}
	
}



function toBlur() {

	running = true
		if (running){		
			jQuery('#blur').css("opacity", i);		
			i = i - 0.05;

		if(i < 0) {
			running = false;
			id_none.css("display","none");
			id_block.css("display","block");
			setTimeout("fromBlur()",20);
		} else {
			 setTimeout("toBlur()", 20);
		}
		

	}
}

function fromBlur() {
	running = true;
		if (running){
		
			jQuery('#blur').css("opacity", i);
			
			i = i + 0.05;

		if(i > 1) {
			running = false;
			i = 1;
		}
		if(running) setTimeout("fromBlur()",20);

	}
}

/*

function toBlur() {
	running = true
	if (running){
		blurCount = 'blur('+i+'px)';			
		jQuery('#blur').css("-webkit-filter", blurCount);
		jQuery('#blur').css("-moz-filter", blurCount);
		jQuery('#blur').css("-ms-filter", blurCount);
		jQuery('#blur').css("filter", blurCount);			
		i = i + 3;
	if(i > 15) {
		running = false;
		id_none.css("display","none");
		id_block.css("display","block");
		setTimeout("fromBlur()",30);
		} else {
			 setTimeout("toBlur()", 30);
		}		
	}
}

function fromBlur() {
	running = true
	if (running){
		blurCount = 'blur('+i+'px)';			
		jQuery('#blur').css("-webkit-filter", blurCount);
		jQuery('#blur').css("-moz-filter", blurCount);
		jQuery('#blur').css("-ms-filter", blurCount);
		jQuery('#blur').css("filter", blurCount);			
		i--;
	if(i == -1) {
		running = false;
		i =1;
	}
	if(running) setTimeout("fromBlur()",30);
	}
}
*/
function toggleVisibilityInstall (blockId, hide ) 
{ 
	jQuery('#rtseo_post_title_format_tip').css('display','none');
	jQuery('#rtseo_custom_post_title_format').css('display','none');
	jQuery('#rtseo_page_title_format_tip').css('display','none');
	jQuery('#rtseo_category_title_format_tip').css('display','none');
	jQuery('#rtseo_author_title_format_tip').css('display','none');
	jQuery('#rtseo_archive_title_format_tip').css('display','none');
	jQuery('#rtseo_tag_title_format_tip').css('display','none');
	jQuery('#rtseo_custom_taxonomy_title_format_tip').css('display','none');
	jQuery('#rtseo_search_title_format_tip').css('display','none');
	jQuery('#rtseo_description_format_tip').css('display','none');
	jQuery('#rtseo_404_title_format_tip').css('display','none');
	jQuery('#rtseo_paged_format_tip').css('display','none');
    if (jQuery('#' +blockId).css('display') == 'none') 
        { 
            jQuery('#' +blockId).animate({height: 'show'}, 500); 
        } 
    else 
        {     
            jQuery('#' +blockId).animate({height: 'hide'}, 500); 
        } 
}