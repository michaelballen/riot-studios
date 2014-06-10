<?php
/*
 * Caches API calls to a local file which is updated on a 
 * given time interval.
 */
class API_Cache {

  private 
      $_update_interval // how often to update
	, $_update_count = 20 //how many updates to save
    , $_cache_file // file to save results to
    , $_api_call; // API call (URL with params)

  public function __construct ($tw, $int=2) {
    $this->_api_call = $tw;
    $this->_update_interval = $int * 60; // minutes to seconds
    $this->_cache_file = str_replace('/', '-', $tw) . '.json';
  }

  public function getCache () {
	if (!file_exists($this->_cache_file)) {
		return false;
	}
    return json_decode(file_get_contents($this->_cache_file));
  }

  /*
   * Makes the api call and updates the cache 
   */
  public function updateCache ($data, $replace=true) {
	$current = $this->getCache();
	
	if (is_array($data)) {
		if (empty($current)) {
			$current = array();
		}
		if ($replace === true) {
			$current = $data;
		} else if (is_int($replace)) {
			//only keep a certain number in the array
			if (count($data) >= $replace) {
				$current = $data;
			} else if ((count($data) + count($current)) > $replace) {
				array_splice($current, -($replace - count($data)));
				$current = array_merge($data, $current);
			} else {
				$current = array_merge($data, $current);
			}
		} else {
			$current = array_merge($data, $current);
		}
		$new_current_str = json_encode($current);
	}
    $fp = fopen($this->_cache_file, 'a+');
	if ($fp) {
		if (flock($fp, LOCK_EX)) {
			if (!empty($new_current_str)) {
				fseek($fp, 0);
				ftruncate($fp, 0);
				fwrite($fp, $new_current_str);
			}
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
	return $current;
  }
}