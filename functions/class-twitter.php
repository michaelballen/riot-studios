<?php 

class Twitter {
	public 		$user_data = array(),
				$tweets_per_page = 10;
	private		$share_defaults = array(),
				$consumer_key = 'CS4px7GAV0DAEjZZWnn4g',
				$consumer_secret = 'BQdfjx7odWrnmIxamNcaAHTgQswej7UJCfDZmAqhKk',
				$oauth_callback = 'http://riotstudios.com/api',
				$access_token = '185555088-Rglg3nfpHdnbTBvCN3WXKWJ5KszszTVH6wo9wh3j',
				$access_token_secret = 'UbSZQD5GAWTn1JRjVyi43ck2O14HbBmWbyAVw9wZMec';
	public function getMaxPages () {
		if (empty($this->tweets)) {
			$this->getRecentTweets();
		}
		return ceil(count($this->tweets) / $this->tweets_per_page);
	}
	public function getRecentTweets () {
		$tweets_action = 'search/tweets';		
		if (isset($this->tweets)) {
			//we've already fetched, just spit out
			return $this->tweets;
		}
		
		//setup to grab old update
		require_once('API_Cache/class-api_cache.php');
		$api_cache = new API_Cache($tweets_action);
		$this->tweets = $api_cache->getCache();
		
		//how long has it been since we checked for tweets?
		$last_checked = get_option('last_checked_tweets');
		$current_time = time();
		
		if ($last_checked !== false && $current_time - $last_checked < 120) {
			//we don't need to update, just grab the last update
			return $this->tweets;
		}
		//we do need to get new tweets, start by setting max id from last time
		$last_checked_id = get_option('last_checked_tweet_id');
		
		//load a new tweets obj
		/* Load required lib files. */
		require_once('twitteroauth/twitteroauth.php');
		$this->consumer_key = 'CS4px7GAV0DAEjZZWnn4g';
		$this->consumer_secret = 'BQdfjx7odWrnmIxamNcaAHTgQswej7UJCfDZmAqhKk';
		$this->oauth_callback = 'http://riotstudios.com/api';
		$this->access_token = '185555088-Rglg3nfpHdnbTBvCN3WXKWJ5KszszTVH6wo9wh3j';
		$this->access_token_secret = 'UbSZQD5GAWTn1JRjVyi43ck2O14HbBmWbyAVw9wZMec';
		
		/* Create a TwitterOauth object with consumer/user tokens. */
		$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_token_secret);
		
		/* statuses/mentions */
		$new_tweets = $connection->get($tweets_action, array(
			'q'=>'riotstudios OR believemefilm OR bocmovie OR from:riotstudios OR from:bocmovie OR from:believemefilm',
			'since_id'=>$last_checked_id,
			'result_type'=>'recent',
			'count'=>100
		));
		//if we got any new tweets
		if (count($new_tweets->statuses) > 0) {
			$this->tweets = $api_cache->updateCache($new_tweets->statuses, 200);
			update_option('last_checked_tweet_id', $this->tweets[0]->id);
		}
		update_option('last_checked_tweets', $current_time);
		return $this->tweets;
	}
	
	public function getFollowers () {
		$tweets_action = 'followers/list';		
		if (isset($this->followers)) {
			//we've already fetched, just spit out
			return $this->followers;
		}
		
		//setup to grab old update
		require_once('API_Cache/class-api_cache.php');
		$api_cache = new API_Cache($tweets_action);
		$this->followers = $api_cache->getCache();
		
		//how long has it been since we checked for tweets?
		$last_checked = get_option('last_checked_twitter_followers');
		$current_time = time();
		
		if ($last_checked !== false && $current_time - $last_checked < 120) {
			//we don't need to update, just grab the last update
			return $this->followers;
		}
		
		//we do need to recheck the followers, load a new tweets obj
		require_once('twitteroauth/twitteroauth.php');
		
		/* Create a TwitterOauth object with consumer/user tokens. */
		$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_token_secret);
		
		/* followers/list */
		$followers = $connection->get($tweets_action, array(
			'count'=>100,
			'skip_status'=>true,
			'include_user_entities'=>false
		));
		//print_r($followers);
		$this->followers = $followers->users;
		$api_cache->updateCache($followers->users);
		update_option('last_checked_twitter_followers', $current_time);
		return $this->followers;
	}
	
	public function showFollowers () {
		$followers = $this->getFollowers();
		foreach($followers as $k=>$v) {
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
				$v->profile_image_url = str_replace('http://a0', 'https://si0', $v->profile_image_url);
			}
		?>
		<div class="twitter-follower">
			<a target="_blank" href="http://twitter.com/<?php echo $v->screen_name; ?>">
				<img src="<?php echo $v->profile_image_url; ?>" />
				<div class="info-box">
					<span class="follower-name"><?php echo $v->name; ?></span><br><span class="screen-name"><?php _e('@' . $v->screen_name); ?></span>
				</div>
			</a>
		</div>
		<?php
		}
	}
	
	public function showRecentTweets ($page = 1, $echo = true) {
		$tweets = $this->getRecentTweets();
		$tweets_per_page = $this->tweets_per_page;
		$tweet_html = '';
		$tweet_text = '';
		$start = ($page - 1) * $tweets_per_page;
		$tweets = array_slice($tweets, $start, $tweets_per_page);
		
		wp_enqueue_script('twitter-widgets');
		foreach ($tweets as $k=>$v) :
			//print_r($v);
			$tweet_date  = new DateTime($v->created_at);
			$today = new DateTime();
			$time_diff = $tweet_date->diff($today);
			if ($time_diff->d >= 1 || $time_diff->m >= 1) {
				$date = date('M d', strtotime($v->created_at));
			} else {
				if ($time_diff->h >= 1) {
					$date = $time_diff->h . 'h';
				} else {
					$date = $time_diff->i . 'm';
				}
			}
			
			$tweet_text = $v->text;
			// check if any entites exist and if so, replace then with hyperlinked versions
			if (!empty($v->entities->urls) || !empty($v->entities->hashtags) || !empty($v->entities->user_mentions)) {
				if (is_array($v->entities->urls)) {
					foreach ($v->entities->urls as $url) {
						$find = $url->url;
						$replace = '<a target="_blank" href="' . $find . '">' . $url->display_url . '</a>';
						$tweet_text = str_replace($find, $replace, $tweet_text);
					}
				}
				if (is_array($v->entities->media)) {
					foreach ($v->entities->media as $tw_med) {
						$find = $tw_med->url;
						$replace = '<a target="_blank" href="' . $find . '">' . $tw_med->display_url . '</a>';
						$tweet_text = str_replace($find, $replace, $tweet_text);
					}
				}
				if (is_array($v->entities->hashtags)) {
					foreach ($v->entities->hashtags as $hashtag) {
						$find = '#' . $hashtag->text;
						$replace = '<a target="_blank" href="http://twitter.com/#!/search/%23' . $hashtag->text . '">' . $find . '</a>';
						$tweet_text = str_replace($find, $replace, $tweet_text);
		        	}
				}
				if (is_array($v->entities->user_mentions)) {
					foreach ($v->entities->user_mentions as $user_mention) {
						$find = "@" . $user_mention->screen_name;
						$replace = '<a target="_blank" href="http://twitter.com/' . $user_mention->screen_name . '">' . $find . '</a>';
						$tweet_text = str_ireplace($find, $replace, $tweet_text);
		        	}
				}
			}
			
			if ($v->retweeted_status) {
				$tweet_author = $v->retweeted_status->user;
				$tweet_text .= '<br><span class="ifontloop"></span> RT by <a target="_blank" href="http://twitter.com/' . $v->user->screen_name . '"><strong>' . $v->user->name . '</strong></a><br>';
			} else {
				$tweet_author = $v->user;
			}
			
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
				$tweet_author->profile_image_url = str_replace('http://a0', 'https://si0', $tweet_author->profile_image_url);
			}
			
			
			$tweet_html .= '<div class="tweet">';
			$tweet_html .= '<header>';
			$tweet_html .= '<a target="_blank" href="http://twitter.com/' . $tweet_author->screen_name . '" class="tweet-avatar">';
			$tweet_html .= '<img src="' . $tweet_author->profile_image_url . '" alt="' . $tweet_author->name . '\'s Twitter" />';
			$tweet_html .= '</a>';
			$tweet_html .= '<a target="_blank" href="http://twitter.com/' . $tweet_author->screen_name . '" class="tweet-author">' .  $tweet_author->name . '</a>';
			$tweet_html .= '<a target="_blank" href="http://twitter.com/' . $tweet_author->screen_name . '" class="tweet-username"> @' .  $tweet_author->screen_name . '</a>';
			$tweet_html .= '<a target="_blank" class="tweet-time" href="http://twitter.com/'. $tweet_author->screen_name . '/status' . '/' . $v->id . '"><span class="ifonttwitter"></span> ' . $date . '</a>';
			$tweet_html .= '</header>';
				
			$tweet_html .= '<div class="tweet-content">';
			$tweet_html .= '<p class="tweet-text">' . $tweet_text . '</p>';
			$tweet_html .= '<p class="tweet-actions">';
			$tweet_html .= '<a href="https://twitter.com/intent/tweet?in_reply_to=' . $v->id . '"><span class="ifontreply"></span> Reply</a> |
						<a href="https://twitter.com/intent/retweet?tweet_id=' . $v->id . '"><span class="ifontloop"></span> Retweet</a> |
						<a href="https://twitter.com/intent/favorite?tweet_id=' . $v->id . '"><span class="ifontstar"></span> Favorite</a>
					</p>
				</div>
			</div>';
		endforeach;
		if ($echo === true) {
			echo $tweet_html;
		} else {
			return $tweet_html;
		}
	}
	
	public function getUserInfo ($return = 'array') {
		$data = array('include_entities'=>'true');
		if (!empty($this->user_data['user_id'])) {
			$data['user_id'] = $this->user_data['user_id'];
		} else if (!empty($this->user_data['screen_name'])) {
			$data['screen_name'] = $this->user_data['screen_name'];
		} else {
			return false;
		}
		$base_url = 'https://api.twitter.com/1/users/show.json?';
		$query = http_build_query($data);
		//get json about user from twitter
		$result = @file_get_contents($base_url . $query);
		if ($return === 'object') {
			return json_decode($result);
		} else {
			//it's either an array of the json or a specific field
			$result = (array) json_decode($result);
			//if it's not an array, get the specific field
			if ($return !== 'array') {
				$result = $result[$return];
			}
		}
		return $result;
	}

	public function getTweetURL ($arr = array()) {
		$arr = array_merge($this->_getTweetDefaults(), $arr);
		$url = 'https://twitter.com/share?' . http_build_query($arr);
		return $url;
	}
	
	public function getFollowURL ($arr = array('screen_name'=>'riotstudios')) {
		$url = 'https://twitter.com/intent/user?' . http_build_query($arr);
		return $url;
	}
	
	private function _getTweetDefaults () {
		if (!empty($this->share_defaults)) {
			return $this->share_defaults;
		}
		$this->share_defaults['url'] = get_permalink( $post->ID );
		$this->share_defaults['related'] = 'riotstudios';
		return $this->share_defaults;
	}
	
	function __construct ($id = false) {
		if ($id === false) {
			return false;
		}
		if (is_array($id)) {
			$this->user_data = $id;
		} else {
			if (is_numeric($id)) {
				//we're dealing with id number
				$this->user_data['user_id'] = $id;
			} else {
				//dealing with a screenname
				$this->user_data['screen_name'] = $id;
			}
		}
	}
}