<?php

class RT_SEO_Validation
{
	var $limit_empty_titles = 50;
	var $limit_title_more_than_max = 50;
	var $limit_title_less_than_min = 50;
	var $limit_empty_description = 100;
	var $limit_description_more_than_max = 50;
	var $limit_description_less_than_min = 50;
	var $limit_low_words_content = 100;
	var $limit_images_empty_or_no_alt = 50;
	var $limit_too_long_url = 50;
	var $limit_no_h1_tag = 50;
	var $limit_h1_tag_more_than_one = 50;
	var $limit_same_title_and_h1 = 50;
	var $limit_duplicated_titles = 50;
	var $limit_images_optimizer = 0;
	var $isPaid;
	
	public function __construct ($data) {
		$this->isPaid = $data;
	}
	
	public function getLimits() {	
		if ($this->isPaid === true) {
			return array(
				"limit_empty_titles" => $this->limit_empty_titles,
				"limit_title_more_than_max" => $this->limit_title_more_than_max,
				"limit_title_less_than_min" => $this->limit_title_less_than_min,
				"limit_empty_description" => $this->limit_empty_description,
				"limit_description_more_than_max" => $this->limit_description_more_than_max,
				"limit_description_less_than_min" => $this->limit_description_less_than_min,
				"limit_low_words_content" => $this->limit_low_words_content,
				"limit_images_empty_or_no_alt" => $this->limit_images_empty_or_no_alt,
				"limit_too_long_url" => $this->limit_too_long_url,
				"limit_no_h1_tag" => $this->limit_no_h1_tag,
				"limit_h1_tag_more_than_one" => $this->limit_h1_tag_more_than_one,
				"limit_same_title_and_h1" => $this->limit_same_title_and_h1,
				"limit_duplicated_titles" => $this->limit_duplicated_titles,
				"limit_images_optimizer" => $this->limit_images_optimizer,
				);
		}
		return false;
	}
	
	public static function writeLicenceInfo($data) {
		$licFile = str_replace("\\", "/", str_replace("classes", "", dirname(__FILE__) . '/tmp/license_info.dat'));
		$data = json_encode($data);
		$data = base64_encode($data);
		$md5Data = md5($data);
		return file_put_contents($licFile, $md5Data . "\n" . $data);
	}
	
	public static function readLicenceInfo() {
		$licFile = str_replace("\\", "/", str_replace("classes", "", dirname(__FILE__) . '/tmp/license_info.dat'));
		if (!is_file($licFile)) return false;
		$data = file($licFile);
		if (md5(trim($data[1])) !== trim($data[0])) {
			unlink($licFile);
			return false;
		} else {
			$data = json_decode(base64_decode($data[1]), true);
			if (!is_array($data)) return false;
			return $data;
		}
		
	}
 
}


?>