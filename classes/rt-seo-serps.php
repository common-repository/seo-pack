<?php

class RT_SEO_Serps extends RT_SEO_Helper
{
	public function getSerps($country) {
		
		$json = RT_SEO_Helper::checkFileInCache($country);
		
		if (!$json) {
			$url = 'https://portal.seoguarding.com/api/index.php?action=get_sensor_data&country='. $country;
			 
			$client = EasyRequest::create($url);
			$client->send();
			$json = $client->getResponseBody();
			file_put_contents(RT_SEO_TMP_PATH . $country, $json);
		}
		
		if ($json) {
			if ($result = json_decode($json, true)) {
				if ($result['status'] === 'ok') {
					return json_decode($result['json'], true);
				}
			} else {
				@unlink(RT_SEO_TMP_PATH . $country);
				return false;
			}
		}
		
		return false;
	}




	
	public function getSerpsBlock() {
		$country = isset($_GET['country']) ? $_GET['country'] : 'US';
		$categoryId = isset($_GET['id']) ? $_GET['id'] : 100;
		$serps = $this->getSerps($country);
		
		if (!is_array($serps) || empty($serps)) return false;

		foreach ($serps as $serp) {
			
			if (($serp['category'] == $categoryId) || (is_null($serp['category']) && $categoryId == 100)) {
				
				
				$ranks[] = "['".$serp['date']."', ".$serp['rank']."],";
				
				$tmp_d = strtotime($serp['date']);
				$dates[] = "'".date("d M", $tmp_d)."'";


			}
		}
		
		?>

			<script src="https://code.highcharts.com/highcharts.js"></script>

			  <div class="two wide column">
				<div class="ui vertical fluid tabular tiny menu">
				
				<?php foreach (RT_SEO_Helper::$categories_left as $id => $category) :?>
				  <a class="item<?php if ($categoryId == $id) echo ' active'?>" href="<?php echo get_admin_url() . 'admin.php?page=rt_seo_dashboard&id=' . $id . '&country=' . $country; ?>"><?php echo $category; ?></a>
				  
					<?php endforeach; ?>
				  
				  
				  
				  
				  
				  
				</div>
			  </div>

            <div class="twelve wide column">
                <div class="ui segment">
<div class="ui fluid search selection dropdown">
  <input type="hidden" onChange="counrtyRedirect(this.value);" name="country" value="<?php echo $country; ?>">
  <i class="dropdown icon"></i>
  <div class="default text">Select Country</div>
  <div class="menu">
  <div class="item" data-value="US"><i class="us flag"></i>United States</div>
  <div class="item" data-value="UK"><i class="gb flag"></i>United Kingdom</div>
  <div class="item" data-value="DE"><i class="de flag"></i>Germany</div>
  <div class="item" data-value="IT"><i class="it flag"></i>Italy</div>
  <div class="item" data-value="ES"><i class="es flag"></i>Spain</div>
  <div class="item" data-value="FR"><i class="fr flag"></i>France</div>
  <div class="item" data-value="AU"><i class="au flag"></i>Australia</div>
</div>
 </div>


                
                    <div  id="container" class="ui relaxed divided items"></div>
<script>

function counrtyRedirect(country) {
	window.location.href = '<?php echo get_admin_url() . 'admin.php?page=rt_seo_dashboard&id=' . $categoryId . '&country='; ?>' + country;
}


jQuery('.ui.dropdown')
  .dropdown({'set selected': 'US'});
jQuery(function () { 
    
    Highcharts.chart('container', {
      chart: {
        scrollablePlotArea: {
          minWidth: 600,
          scrollPositionX: 1
        }
      },
      title: {
        text: 'SERP volatility for the last 30 days'
      },
      xAxis: {
        categories: [<?php echo implode($dates, ", "); ?>]
      },
      yAxis: {
        title: {
          text: ''
        },
        minorGridLineWidth: 0,
        gridLineWidth: 0,
        alternateGridColor: null,
        plotBands: [{ 
          from: 0,
          to: 2,
          color: '#ffffff',
          label: {
            text: 'Low',
            style: {
              color: '#000000'
            }
          }
        }, { 
          from: 2,
          to: 5,
          color: '#eeeeee',
          label: {
            text: 'Normal',
            style: {
              color: '#000000'
            }
          }
        }, { 
          from: 5,
          to: 8,
          color: '#ffffff',
          label: {
            text: 'High',
            style: {
              color: '#000000'
            }
          }
        }, {
          from: 8,
          to: 10,
          color: '#eeeeee',
          label: {
            text: 'Very High',
            style: {
              color: '#000000'
            }
          }
        }]
      },
      tooltip: {
        valueSuffix: ''
      },
      plotOptions: {
        spline: {
          lineWidth: 4,
          states: {
            hover: {
              lineWidth: 5
            }
          },
          marker: {
            enabled: true
          },

        }
      },
      series: [{
        name: 'Rank: ',
        color: 'rgb(0, 102, 204)',
        data: [
            <?php echo implode($ranks, "\n"); ?>
        ]
    
      }],
      navigation: {
        menuItemStyle: {
          fontSize: '10px'
        }
      }
    });

});
</script>
                </div>
            </div>
			  <div class="two wide column">
				<div class="ui vertical fluid tabular right tiny menu">
				
				<?php foreach (RT_SEO_Helper::$categories_right as $id => $category) :?>
				  <a class="item<?php if ($categoryId == $id) echo ' active'?>" href="<?php echo get_admin_url() . 'admin.php?page=rt_seo_dashboard&id=' . $id . '&country=' . $country;  ?>"><?php echo $category; ?></a>
				  
					<?php endforeach; ?>
				  
				  
				  
				  
				  
				  
				</div>
			  </div>
		<?php
	}
}
$RT_SEO_Serps = new RT_SEO_Serps();
?>