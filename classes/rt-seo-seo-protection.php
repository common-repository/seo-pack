<?php



class RT_SEO_Protection
{
    public static $search_words = array(
        0 => 'document.write(',
    	6 => 'document.createElement(',
    	20 => 'display:none',
    	21 => 'poker',
    	22 => 'casino',
    	48=> 'hacked',
    	49=> 'cialis ',
    	52=> 'viagra '
    );
    
    
    public static function PrepareResults($results)
    {
        $a = array(
            'WORDS' => array(),
            'A' => array(),
            'IFRAME' => array(),
            'SCRIPT' => array()
        );
        
        //return $results;
        
        if (isset($results['posts']['WORDS']) && count($results['posts']['WORDS']))
        {
            foreach ($results['posts']['WORDS'] as $post_id => $post_arr)
            {
                foreach ($post_arr as $word)
                {
                    $a['WORDS'][$word] = $word;
                }
            }
        }
        
        if (isset($results['posts']['A']) && count($results['posts']['A']))
        {
            foreach ($results['posts']['A'] as $posts)
            {
                if (count($posts))
                {
                    foreach ($posts as $post_id => $post_arr)
                    {
                        if (count($post_arr))
                        {
                            foreach ($post_arr as $post_link => $post_txt)
                            {
                                $a['A'][$post_link] = $post_txt;
                            }
                        }
                    }
                }
            }
        }
        
        
        if (isset($results['posts']['IFRAME']) && count($results['posts']['IFRAME']))
        {
            foreach ($results['posts']['IFRAME'] as $posts)
            {
                if (count($posts))
                {
                    foreach ($posts as $post_id => $post_link)
                    {
                        $a['IFRAME'][$post_link] = $post_link;
                    }
                }
            }
        }
        
        //print_r($results['posts']['IFRAME']);exit;
        if (isset($results['posts']['SCRIPT']) && count($results['posts']['SCRIPT']))
        {
            foreach ($results['posts']['SCRIPT'] as $post_id => $post_arr)
            {
                foreach ($post_arr as $js_link => $js_code)
                {
                    if (strpos($js_link, "javascript code") !== false) $a['SCRIPT'][md5($js_code)] = $js_code;
                    else $a['SCRIPT'][md5($js_link)] = $js_link;
                }
            }
        }
        
        //echo '0000'.$post_link;exit;
        //print_r($a); exit;
        
        ksort($a['A']);
        ksort($a['SCRIPT']);
        sort($a['IFRAME']);
        return $a;
        
    }


    public static function GetPostTitle_by_ID($post_id)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'posts';
        
        $rows = $wpdb->get_results( 
        	"
        	SELECT post_title
        	FROM ".$table_name."
            WHERE ID = ".$post_id."
            LIMIT 1;
        	"
        );
        
        if (count($rows)) return $rows[0]->post_title;
        else return false;
    }
    
    public static function GetPostTitles_by_IDs($post_ids = array())
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'posts';
        
        $rows = $wpdb->get_results( 
        	"
        	SELECT ID, post_title
        	FROM ".$table_name."
            WHERE ID IN (".implode(",", $post_ids).")
        	"
        );
        
        if (count($rows)) 
        {
            $a = array();
            foreach ($rows as $row)
            {
                $a[$row->ID] = $row->post_title;
            }
            return $a;
        }
        else return false;
    }
    
    public static function MakeAnalyze()
    {
        //error_reporting(0);
        ignore_user_abort(true);
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'posts';
        
        $rows = $wpdb->get_results( 
        	"
        	SELECT ID, post_content AS val_data
        	FROM ".$table_name."
			WHERE post_type in ('post', 'page')
        	"
        );
        
        $a = array();
        if (count($rows))
        {
            
            $domain = RT_SEO_helper::PrepareDomain(get_site_url());
            
            $a['total_scanned'] = count($rows);
            
            foreach ($rows as $row)
            {
                //$post_content = $row->val_data;
				$post_content = "<html><body>".$row->val_data."</body></html>";
                
                foreach (self::$search_words as $find_block)
                {
                    if (stripos($post_content, $find_block) !== false)
                    {
                        $a['posts']['WORDS'][$row->ID][] = $find_block;
                    }
                }
                
                $html = str_get_html($post_content);
                
                if ($html !== false)
                {
                    $tmp_a = array();
                    
                    // Tag A
                    foreach($html->find('a') as $e) 
                    {
                        $link = strtolower(trim($e->href));
                        if (strpos($link, $domain) !== false) continue;     // Skip own links
                        if (strpos($link, "mailto:") !== false) continue;
                        if (strpos($link, "callto:") !== false) continue;
                        if ( $link[0] == '?' || $link[0] == '/' ) continue;
                        if ( $link[0] != 'h' && $link[1] != 't' && $link[2] != 't' && $link[3] != 'p' ) continue;
                        
                        //$tmp_s = $link.' <span class="color_light_grey">[Txt: '.strip_tags($e->outertext).']</span>';

                        /*$tmp_data = array(
                            'l' => $link,
                            't' => strip_tags($e->outertext)
                        );
                        $tmp_a[$link] = $tmp_data;*/
                        $tmp_a[$link] = strip_tags($e->outertext);
                        
                        $a['posts']['A'][$row->ID][] = $tmp_a;
                    }
                    
                    
                    
                    // Tag IFRAME
                    foreach($html->find('iframe') as $e) 
                    {
                        $link = strtolower(trim($e->src));
                        if (strpos($link, $domain) !== false) continue;     // Skip own links
                        if ( $link[0] == '?' || $link[0] == '/' ) continue;
                        if ( $link[0] != 'h' && $link[1] != 't' && $link[2] != 't' && $link[3] != 'p' ) continue;
                        
                        /*$tmp_data = array(
                            'l' => $link,
                            't' => 'iframe'
                        );
                        $tmp_a[$link] = $tmp_data;*/
                        
                        $a['posts']['IFRAME'][$row->ID][] = $link;
                    }
                    
                    
                    
                	// Tag SCRIPT
                	foreach($html->find('script') as $e)
                	{
                	    if (isset($e->src)) 
                        {
                            $link = strtolower(trim($e->src));
                        
                            if (strpos($link, $domain) !== false) continue;     // Skip own links
                            if ( $link[0] == '?' || $link[0] == '/' ) continue;
                            if ( $link[0] != 'h' && $link[1] != 't' && $link[2] != 't' && $link[3] != 'p' ) continue;
                            
                            $t = '';
                        }
                        else  {
                            $link = 'javascript code '.rand(1, 1000);
                            $t = $e->innertext;
                        }
                        
                        /*$tmp_data = array(
                            'l' => $link,
                            't' => $t
                        );*/
                        $tmp_a[$link] = $t;
                        
                        $a['posts']['SCRIPT'][$row->ID] = $tmp_a;
                    }
                    
                }
                
                unset($html);
            }
            
        }
        
        // save results
        $data = array(
            'progress_status' => 0,
            'results' => json_encode($a),
            'latest_scan_date_seo' => date("Y-m-d H:i:s")
        );
        RT_SEO_Helper::Set_SQL_Params($data);
    }
    


}

?>