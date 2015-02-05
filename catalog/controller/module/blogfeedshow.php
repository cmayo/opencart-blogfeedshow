<?php  
class ControllerModuleBlogfeedshow extends Controller {
	protected function index($setting) {
		$cachename='blogfeedshow.'.md5(serialize($setting));
		$this->load->model('tool/image');
		
		$this->language->load('module/blogfeedshow');

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$feed_entries_data = $this->cache->get($cachename);
		
		if (!$feed_entries_data) {
			$feed_entries_data = $this->parseFeed($setting);
			$this->cache->set($cachename, $feed_entries_data);
		}
		
		$this->data['feedentries'] = $feed_entries_data;
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/blogfeedshow.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/blogfeedshow.tpl';
		} else {
			$this->template = 'default/template/module/blogfeedshow.tpl';
		}
		
		$this->render();
	}
	
	private function parseFeed($setting) {
		$entries=array();
		
		$feed = file_get_contents($setting['url']);
		
		if ($feed) {
		
			$xml = simplexml_load_string($feed);
		
			for ($i=0; $i<(int)$setting['numposts']; $i++) {
				$item=$xml->channel->item[$i];
				
				$images = array();
				$content = $item->children("content", true);
				preg_match('!http://[a-zA-Z0-9\-\_\.\/\%]+\.(?:jpe?g|png|gif)!Ui' , $content->encoded , $images);
				
				$image = '';

				if (count($images)) {
					$info = pathinfo($images[0]);
				
					$temp_file=tempnam(sys_get_temp_dir(),$info['basename']);
					file_put_contents($temp_file, file_get_contents($images[0]));
		
					$extension = $info['extension'];
					
					$new_image = 'cache/' . utf8_substr($info['basename'], 0, utf8_strrpos($info['basename'], '.')) . '-' . $setting['width'] . 'x' . $setting['height'] . '.' . $extension;
					
					$imageObj = new Image($temp_file);
					$imageObj->resize($setting['width'], $setting['height'],'w');
					$imageObj->save(DIR_IMAGE . $new_image);
					
					$image = $this->config->get('config_url') . 'image/' . $new_image;
				}

				
				$entries[$i] = array(
					'id'			=> $i+1,
					'title'			=> (string)$item->title,
					'link'			=> (string)$item->link,
					'image'			=> $image,
					'day'			=> date('d', strtotime($item->pubDate)),
					'month'			=> date('m', strtotime($item->pubDate)),
					'year'			=> date('Y', strtotime($item->pubDate)),
					'description'	=> $this->limit_text(strip_tags($item->description),30),
				);
			}	
		}
		return $entries;
	}
	
	private function limit_text($str, $limit = 100, $end_char = '...') {
		if (trim($str) == '') {
			return $str;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);
			
		if (strlen($str) == strlen($matches[0])) {
			$end_char = '';
		}
		
		return rtrim($matches[0]).$end_char;
	}
}
?>