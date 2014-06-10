<?php 
class Facebook {
	private		$share_defaults = array();

	public function getShareURL ($arr = array()) {
		//alias url and title so i don't have to remember it's "u" and "t"
		if (!empty($arr['url'])) {
			$arr['u'] = $arr['url'];
			unset($arr['url']);
		}
		if (!empty($arr['title'])) {
			$arr['t'] = $arr['title'];
			unset($arr['title']);
		}
		$arr = array_merge($this->_getShareDefaults(), $arr);
		$url = 'http://www.facebook.com/sharer.php?' . http_build_query($arr);
		return $url;
	}
	
	public function getLikeURL ($arr = array('screen_name'=>'riotstudios')) {
		$url = 'https://twitter.com/intent/user?' . http_build_query($arr);
		return $url;
	}
	
	private function _getShareDefaults () {
		if (!empty($this->share_defaults)) {
			return $this->share_defaults;
		}
		$this->share_defaults['u'] = get_permalink( $post->ID );
		$this->share_defaults['t'] = 'Riot Studios';
		return $this->share_defaults;
	}
}