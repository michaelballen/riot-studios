<?php
class RiotApi {
	public $params;
	public $stripe;
	private $job_application_arr = array(
		'user_first_name',
		'user_middle_name',
		'user_last_name',
		'user_email',
		'user_phone',
		'city',
		'state',
		'user_fb_profile',
		'user_twitter_profile',
		'resume',
		'job_application',
		'education_acquired',
		'job_interest',
		'college',
		'college_degree',
		'masters',
		'masters_degree',
		'references',
		'what_to_learn',
		'how_to_remember',
		'worst_movie',
		'status',
		'starred',
		'admin_viewed'
	);
	private $event_request_arr = array(
		'user_name',
		'user_email',
		'user_phone',
		'event_location_city',
		'event_location_state',
		'event_dates',
		'event_guys',
		'event_honorarium',
		'event_travel_exp',
		'event_merch',
		'event_description'
	);
	public function getProduct () {
		$p = $this->params;
		$post = get_post($p['id']);
		$thumb_id = get_post_thumbnail_id($p['id']);
		$thumb = wp_get_attachment_image_src($thumb_id, 'admin-list-thumb');
		$post->thumb = $thumb[0];
		$post->image = wp_get_attachment_url($thumb_id);
		$post->images = array();
		$post->id = $p['id'];
		$post->slug = $post->post_name;
		$post->price = get_post_meta($p['id'], '_price', true);
		$post->nyop = get_post_meta($p['id'], '_nyop', true);
		$post->shipping = get_post_meta($p['id'], '_ship_price', true);
		$post->post_content = wpautop($post->post_content);
		$post->trailer_id = get_post_meta($p['id'], '_trailer_id', true);
		$post->trailer_link = get_post_meta($p['id'], '_trailer_link', true);
		$post->website_link = get_post_meta($p['id'], '_website_link', true);
		$post->product_type = get_post_meta($p['id'], '_product_type', true);
		return $post;
	}
	public function getArticle () {
		$p = $this->params;
		if (empty($p['id'])) {
			return 'ID is required';
		}
		$post = get_post($p['id']);
		$author = get_userdata($post->post_author);
		$post->post_author_name = $author->display_name;
		$post->post_content = wpautop($post->post_content);
		$post->author_thumbnail = get_avatar($author->ID, 'thumbnail');
		$post->post_date = date('F j, Y', strtotime($post->post_date));
		
		if ($post->post_type === 'product') {
			$img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
			$post->image = $img[0];
			
			$post->nyop = get_post_meta($post->ID, '_nyop', true);
			$post->price = (float) get_post_meta($post->ID, '_price', true);
		}
		$post->permalink = get_permalink($post->ID);
		return $post;
	}
	public function getImageGallery ($id = false) {
		$p = $this->params;
		if (!$id) {
			if (empty($p['id'])) {
				throw new Exception('ID is required to lookup image gallery');
			}
			$id = $p['id'];
		}
		$post = get_post($id);
		$permalink = get_permalink($post->ID);
		$images = array();
		$attachments = new Attachments( 'riot_attachments', $id );
		if( $attachments->exist() ) :
		    while($attachments->get() ) :
				$images[$attachments->id()] = array(
					'full'=>$attachments->src('full'),
					'thumbnail'=>$attachments->src('thumbnail')
				);
			endwhile;
		endif;
		return array(
			'title'=>$post->post_title,
			'content'=>wpautop($post->post_content),
			'images'=>$images,
			'permalink'=>$permalink
		);
	}
	public function storeCheckout () {
		require_once('Stripe/Stripe.php');
		
		// set your secret key: remember to change this to your live secret key in production
		// see your keys here https://manage.stripe.com/account
		if (home_url() === 'http://riotstudios.com' || home_url() === 'https://riotstudios.com') {
			Stripe::setApiKey("sk_live_et97Q4DOC0sB3n7MUa2JSTca");
		} else {
			Stripe::setApiKey("sk_test_PtucQ9cVM9KM15lNU2RmZsbb");
		}
		

		// get the credit card details submitted by the form
		$token = $_POST['stripeToken'];
		$amount = $this->_getCartTotal(stripslashes($_POST['cart']), $_POST['shipping_country']) * 100;
		$shipping = $this->_getCartShipping(stripslashes($_POST['cart']), $_POST['shipping_country']) * 100;
		$cart = (array) json_decode(stripslashes($_POST['cart']));
		
		$has_stream = false;
		
		foreach ($cart['items'] as $k=>$v) {
			$p_type = get_post_meta($k, '_product_type', true);
			if ($p_type === 'stream') {
				$rand = $this->_generateRandomString(10);
				$v->watch_token = $rand;
				$v->streaming_video = get_post_meta($k, '_streaming_title', true);
				$cart['items']->$k = $v;
				$has_stream = true;
			}
		}
		
		$ship_address = array(
			'street'=>$_POST['shipping_address1'],
			'street2'=>$_POST['shipping_address2'],
			'city'=>$_POST['shipping_city'],
			'state'=>$_POST['shipping_state'],
			'zip'=>$_POST['shipping_zip'],
			'country'=>$_POST['shipping_country']
		);
		
		if ($_POST['same_address'] === 'on') {
			$bill_address = $ship_address;
		} else {
			$bill_address = array(
				'street'=>$_POST['billing_address1'],
				'street2'=>$_POST['billing_address2'],
				'city'=>$_POST['billing_city'],
				'state'=>$_POST['billing_state'],
				'zip'=>$_POST['billing_zip'],
				'country'=>$_POST['billing_country']
			);
		}
		//add or update a Wordpress db customer
		$wp_customer = $this->updateCustomer(array(
			'email'=>$_POST['user_email'],
			'name'=>$_POST['user_name'],
			'phone'=>$_POST['user_phone'],
			'shipping_address'=>$ship_address
		));
		$this->params['customer_id'] = $wp_customer;
		
		$full_name = empty($_POST['user_name']) ? explode(' ', $_POST['user_name']) : explode(' ', $_POST['billing_name']);
		$fname = $full_name[0];
		$lname = $full_name[count($full_name) - 1];
		
		//if the user asked to get on our email list, add him/her
		if ($_POST['email_subscribe'] === 'on') {
			$email_group_str = array();
			foreach ($cart['items'] as $k=>$v) {
				$email_group_str[] = $v->title;
			}
			$email_group_str = implode(',', $email_group_str);
			$this->_emailSubscribe(array(
				'email'=>$_POST['user_email'],
				'FNAME'=>$fname,
				'LNAME'=>$lname,
				'address'=>array(
					'addr1'=>$_POST['shipping_address1'],
					'addr2'=>$_POST['shipping_address2'],
					'city'=>$_POST['shipping_city'],
					'state'=>$_POST['shipping_state'],
					'zip'=>$_POST['shipping_zip'],
					'country'=>$_POST['shipping_country']
				),
				'GROUPINGS'=>array(
					array(
						'name'=>'Customers',
						'groups'=>$email_group_str
					)
				),
				'phone'=>$_POST['user_phone'],
				'double_opt_in'=>false,
				'send_welcome'=>false,
				'update_existing'=>true
			));
		}
		//make an order in the wp database...
		$wp_order_id = $this->createInvoice();
		
		try {
			//check if the wp customer has a stripe id set
			$stripe_id = get_post_meta($wp_customer, 'stripe', true);
			if ($stripe_id) {
				//if has stripe id, retrieve customer from stripe
				try {
					$customer = Stripe_Customer::retrieve($stripe_id);
					//update stripe customer
					$customer->card = $token;
					$customer->description = $_POST['user_name'];
					$customer->save();
				} catch (Exception $e) {
					//if the stripe id wasn't found, just make a new one...
					$customer = Stripe_Customer::create(array(
						"card" => $token,
						"description" => $_POST['user_name'],
						"email" => $_POST['user_email']
					));
					update_post_meta($wp_customer, 'stripe', $customer->id);
				}
			} else {
				// no stripe id set, create a Customer in stripe
				$customer = Stripe_Customer::create(array(
					"card" => $token,
					"description" => $_POST['user_name'],
					"email" => $_POST['user_email']
				));
				//save the stripe id to wp for next time
				add_post_meta($wp_customer, 'stripe', $customer->id);
			}
			// charge the Customer
			$charge = Stripe_Charge::create(array(
				"amount" => $amount, // amount in cents
				"currency" => "usd",
				"customer" => $customer->id
			));
			$this->updateInvoice(array(
				'id'=>$wp_order_id,
				'stripe'=>$charge->id,
				'shipping'=>$shipping,
				'total'=>$amount,
				'customer'=>$wp_customer,
				'status'=>'complete',
				'items'=> $cart['items'],
				'ship_address'=>$ship_address,
				'bill_address'=>$bill_address
			));
			
			$this->_sendInvoiceEmail($wp_order_id);
			if ($has_stream) {
				//send out an email with streaming instructions
				$this->_sendStreamingInfo($wp_order_id);
			} else {
				$this->_sendProvidentOrder($wp_order_id);
			}
			
		} catch (Exception $e) {
			$error = $e->getMessage();
			$this->updateInvoice(array(
				'id'=>$wp_order_id,
				'error'=>$error,
				'status'=>'error'
			));
			return $error;
		}
		return array(
			'success'=>true,
			'id'=>$wp_order_id,
			'cart'=>$cart['items']
		);
	}
	public function getCustomer ($p = false) {
		if (!$p) {
			$p = $this->params;
		}
		if (is_string($p)) {
			$email = $p;
		} else {
			$p = (array) $p;
			$email = $p['email'];
		}
		$qry = new WP_Query(array(
			'post_type'=>'customer',
			'meta_query'=>array(
				array(
					'key'=>'email',
					'value'=>$email,
					'compare'=>'LIKE'
				)
			)
		));
		if (!$qry->have_posts()) {
			return false;
		}
		while($qry->have_posts()) : $qry->the_post();
			$id = get_the_ID();
		endwhile;
		return $id;
	}
	public function updateCustomer ($p=false) {
		if (!$p) {
			$p = $this->params;
		}
		$p = (array) $p;
		$id = $this->getCustomer($p);
		if ($id) {
			//update the customer
			update_post_meta($id, 'name', $p['name']);
			update_post_meta($id, 'shipping_address', json_encode($p['shipping_address']));
			if (!empty($p['phone'])) {
				update_post_meta($id, 'phone', $p['phone']);
			}
		} else {
			//create new customer
			$id = wp_insert_post(array(
				'post_title'=>$p['name'],
				'post_type'=>'customer',
				'post_status'=>'publish'
			));
			//add meta to it
			add_post_meta($id, 'email', $p['email']);
			add_post_meta($id, 'name', $p['name']);
			add_post_meta($id, 'shipping_address', json_encode($p['shipping_address']));
			add_post_meta($id, 'phone', $p['phone']);
		}
		return $id;
	}
	public function createInvoice () {
		$p = $this->params;
		$id = wp_insert_post(array(
			'post_type'=>'invoice',
		));
		//save new title
		wp_insert_post(array(
			'ID'=>$id,
			'post_title'=>'Invoice #' . $id,
			'post_type'=>'invoice',
			'post_status'=>'publish'
		));
		//add meta
		add_post_meta($id, 'status', 'in_process');
		return $id;
	}
	public function updateInvoice ($p) {
		$id = $p['id'];
		unset($p['id']);
		foreach($p as $k=>$v){
			if (is_array($v) || is_object($v)) {
				$v = json_encode($v);
			}
			update_post_meta($id, $k, $v);
		}
	}
	public function registerUser ($p=false) {
		if ($p === false) {
			$p = $this->params;
		}
		$arr = array(
			'double_opt_in'=>true,
			'send_welcome'=>true
		);
		$email = $p['user_email'];
		if (empty($email)) {
			throw new Exception('Email is required');
		}
		$arr['email'] = $email;
		if (!empty($p['phone'])) {
			$arr['phone'] = $p['user_phone'];
		}
		if (!empty($p['user_name'])) {
			if (strpos(' ', $p['user_name']) !== FALSE) {
				$name_arr = explode(' ', $p['user_name']);
				$arr['FNAME'] = $name_arr[0];
				$arr['LNAME'] = $name_arr[count($name_arr) - 1];
			} else {
				$arr['FNAME'] = $p['user_name'];
			}
		}
		if (!empty($p['group_name'])) {
			$arr['GROUPINGS'] = array(array(
				'name'=>$p['group_name'],
				'groups'=>$p['group_value']
			));
		}
		$action = $this->_emailSubscribe($arr);
		if ($action === true) {
			return "We got your info, and we're excited to keep you in the loop with all things Riot.";
		} else {
			throw new Exception($action);
		}
	}
	public function logVideoStarted () {
		$p = $this->params;
		$order_id = $p['order_id'];
		$watch_token = $p['watch_token'];
		$items_str = get_post_meta($order_id, 'items', true);
		if (!$items_str) {
			throw new Exception('No watch token found for that order');
		}
		$items = (array) json_decode($items_str);
		$has_wt = false;
		foreach ($items as $k=>$v) {
			if ($v->watch_token === $watch_token) {
				$has_wt = $v;
				$has_wt_id = $k;
				break;
			}
		}
		if ($has_wt === false) {
			throw new Exception('No watch token match (' . $watch_token . ') found for that order');
		}
		if (empty($has_wt->date_started)) {
			date_default_timezone_set("US/Central");
			$has_wt->date_started = date('Y-m-d H:i:s');
			$exp_date = date("+3 days", strtotime($has_wt->date_started));
			$items[$has_wt_id] = $has_wt;
			
			update_post_meta($order_id, 'items', json_encode($items));
			
			//get the customer's email
			$customer_id = get_post_meta($order_id, 'customer', true);
			$customer_email = get_post_meta($customer_id, 'email', true);
			$customer_name = $this->_evalFullName(get_post_meta($customer_id, 'name', true));
			$email_content = "Hey " . $customer_name['first'] . ",

We couldn't help but notice that you started your video of " . $has_wt->title . ". Hope you're enjoying it! We just wanted to remind you that you can access the video for <b>3 more days</b> -or- <b>" . $exp_date . "</b>. After that, your link will expire. Make sure you get it watched before then.<br /><br />
If you need to access the movie again in the next 3 days, just use this link...<br />
" . site_url('theater') . '?wT=' . $watch_token . '&oI=' . $order_id . "
<br /><br />--
Cheers,<br /><br />Riot Studios";
			
			//send email to let them know
			$this->_sendEmail(array(
				'to'=>$customer_email,
				'subject'=>"You Started Your Rental of " . $has_wt->title,
				'message'=>$email_content
			));
			
			return $email_content;
		}
	}
	public function logVideoPlayPosition () {
		$p = $this->params;
		$order_id = $p['order_id'];
		$watch_token = $p['watch_token'];
		$items_str = get_post_meta($order_id, 'items', true);
		if (!$items_str) {
			throw new Exception('No watch token found for that order');
		}
		$items = (array) json_decode($items_str);
		$has_wt = false;
		foreach ($items as $k=>$v) {
			if ($v->watch_token === $watch_token) {
				$has_wt = $v;
				$has_wt_id = $k;
				break;
			}
		}
		if ($has_wt === false) {
			throw new Exception('No watch token match (' . $watch_token . ') found for that order');
		}
		$has_wt->playPosition = $p['play_position'];
		$items[$has_wt_id] = $has_wt;
		update_post_meta($order_id, 'items', json_encode($items));
		return $items;
	}
	public function getStreamingLinks () {
		$watch_token = $_REQUEST['watch_token'];
		$order_id = $_REQUEST['order_id'];
		$nonce = $_REQUEST['streaming_nonce'];
		$found_cart_item = false;
		if (empty($watch_token) || empty($order_id) || !wp_verify_nonce($nonce, 'streamRiotVideoNonce')) {
			throw new Exception('Invalid request: your video file cannot be provided at this time.');
		}
		//get the item from the order's cart
		$order_items = get_post_meta($order_id, 'items', true);
		$items = (array) json_decode($order_items);
		foreach($items as $k=>$v) {
			if (!empty($v->watch_token) && $v->watch_token === $watch_token) {
				$found_cart_item = $v;
				break;
			}
		}
		if ($found_cart_item === false) {
			throw new Exception('Video not found. Please purchase a product at the Riot Store to watch');
		}
		$playPosition = !empty($found_cart_item->playPosition) ? $found_cart_item->playPosition : 0;
		$video_title = get_post_meta($found_cart_item->id, '_streaming_title', true);
		$video_links = array(
			'mp4'=>'http://riotstudios.com/wp-content/streaming_videos/' . $video_title . '.mp4',
			'webm'=>'http://riotstudios.com/wp-content/streaming_videos/' . $video_title . '.webm',
			'poster'=>'http://riotstudios.com/wp-content/streaming_videos/' . $video_title . '.jpg',
			'playPosition'=>$playPosition
		);
		return $video_links;
	}
	public function contactForm () {
		$p = $this->params;
		$nonce = $p['contact_nonce'];
		//require a wp nonce field
		if (empty($nonce)) {
			throw new Exception('Missing wp nonce field, access denied yo!');
		}
		//check the nonce field
		if (!wp_verify_nonce($nonce, 'contactForm')) {
			throw new Exception('Incorrect nonce field, access denied yo!');
		}
		if (empty($p['user_email']) || !filter_var($p['user_email'], FILTER_VALIDATE_EMAIL)) {
			throw new Exception('Valid email is required');
		}
		if (empty($p['message'])) {
			throw new Exception('Message is required');
		}
		if (empty($p['subject'])) {
			$subject = 'Contact Form from ' . $p['user_email'];
		} else {
			$subject = 'Contact From - ' . $p['subject'];
		}
		//put together the message:
		
		$full_message = 'The following message was sent using a contact form on the website from ' . $p['user_email'] . '
		<br />
		============================================
		<br />
		<br />' . $p['message'] . '<br /><br />
		============================================';
		
		//send an email to info with the deets...
		$this->_sendEmail(array(
			'to'=>'info@riotstudios.com',
			'subject'=>$subject,
			'message'=>$full_message,
			'headers'=>array(
				'From: Riot Studios Website <noreply@riotstudios.com>',
				'Reply-To: ' . $p['user_email']
			)
		));
		
		//now, did they want to sign up for mailchimp?
		if (!empty($p['email_register']) && $p['email_register'] === '1') {
			$mc_arr = array(
				'email'=>$p['user_email']
			);
			if (!empty($p['user_name'])) {
				$user_name_arr = $this->_evalFullName($p['user_name']);
				$mc_arr['FNAME'] = $user_name_arr['first'];
				$mc_arr['LNAME'] = $user_name_arr['last'];
			}
			$this->_emailSubscribe($mc_arr);
		}
		return 'sent';
	}
	public function saveJobApplication () {
		$fields_to_save;
		$app_id;
		//security check
		if (!wp_verify_nonce($_REQUEST['job_app_nonce'], 'riotJobApplication')) {
			throw new Exception('missing/incorrect nonce field!');
		}
		if (empty($_REQUEST['application_id'])) {
			throw new Exception('Application ID number is required');
		}
		$app_id = $_REQUEST['application_id'];
		if (empty($_REQUEST['application_secret'])) {
			throw new Exception('Application secret is required');
		}
		$real_secret = get_post_meta($app_id, 'secret', true);
		if ($real_secret !== $_REQUEST['application_secret']) {
			throw new Exception('Application secret does not match');
		}
		if (!empty($_FILES['resume_upload']) && $_FILES['resume_upload']['size'] > 0) {
			$resume_file = $this->uploadResume();
			if ($resume_file !== false) {
				$_REQUEST['resume'] = $resume_file;
			}
		}
		foreach ($this->job_application_arr as $v) {
			if (isset($_REQUEST[$v])) {
				update_post_meta($app_id, $v, $_REQUEST[$v]);
			}
		}
		return "saved";
	}
	public function uploadResume () {
		if (!wp_verify_nonce($_REQUEST['job_app_nonce'], 'riotJobApplication')) {
			throw new Exception('missing/incorrect nonce field!');
		}
		if (empty($_FILES['resume_upload']) || $_FILES['resume_upload']['size'] === 0) {
			throw new Exception('resume not uploaded');
		}
		if ($_FILES['resume_upload']['size'] > 30000000) {
			throw new Exception('files must be under 30M');
		}
		if ($_FILES['resume_upload']['type'] !== 'application/pdf' && $_FILES['resume_upload']['type'] !== 'application/doc') {
			throw new Exception('incorrect file type');
		}
		$file_new_name = 'App-' . $_REQUEST['application_id'] . '-Resume-' . $_REQUEST['application_secret'];
		if ($_FILES['resume_upload']['type'] === 'application/pdf') {
			$file_new_name .= '.pdf';
		} else {
			$file_new_name .= '.doc';
		}
		$_FILES['resume_upload']['name'] = $file_new_name;
		$uploaded_resume = $_FILES['resume_upload'];
		$upload_overrides = array(
			'test_form' => false,
			'unique_filename_callback' => array($this, 'nonUniqueFilename')
		);
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}
		$movefile = wp_handle_upload( $uploaded_resume, $upload_overrides );
		return $movefile['url'];
	}
	public function nonUniqueFilename ($directory, $name, $extension) {
		$filename = $name . strtolower($extension);
		return $filename;
	}
	public function getJobApplicationByID ($id) {
		return $this->_formatApplication($id);
	}
	public function getJobApplication ($email=false, $sent_secret=false, $readonly=false) {
		if ($email === false) {
			$email = $_REQUEST['user_email'];
		}
		$email = strtolower($email);
		$application;
		$app_qry;
		//do we have an email?
		if (empty($email)) {
			throw new Exception('Email is required.');
		}
		//look up if there's already an application with that email...
		$app_qry = new WP_Query(array(
			'post_type'=>'job_application',
			'meta_query'=>array(
				array(
					'key'=>'user_email',
					'value'=>$email,
					'compare'=>'='
				)
			)
		));
		//if we do already have that email setup
		if ($app_qry->have_posts()) {
			$secret = get_post_meta($app_qry->post->ID, 'secret', true);
			//secret preset?
			//first check the url
			if (!$sent_secret && !empty($_REQUEST['secret'])) {
				$sent_secret = $_REQUEST['secret'];
			//then check the session
			} else if (!$sent_secret && !empty($_SESSION['job_app_secret'])) {
				$sent_secret = $_SESSION['job_app_secret'];
			}
			//it's not a new user, and they're trying to log in without a secret
			if ($sent_secret === false) {
				throw new Exception('App already started. Check link in email.');
			}
			//here they're trying to login with the wrong secret..
			if ($sent_secret !== $secret) {
				//if it's just to read, don't throw an error, but dont show this bc something's funky
				if ($readonly) {
					return false;
				}
				throw new Exception('Incorrect link for in progress app. Check link in email.');
			}
			/*if (empty($secret)) {
				update_post_meta($app_qry->post->ID, 'secret', $this->_generateRandomString(10));
			}*/
			$application = $this->_formatApplication ($app_qry->post->ID);
			return $application;
		}
		
		
		//new user!
		
		
		//if this is a readonly situation, and there's not nonce requesting a new app, bail
		if ($readonly !== false || !isset($_REQUEST['create_app_nonce'])) {
			return false;
		}
		
		$app_id = wp_insert_post(array(
			'post_type'=>'job_application',
		));
		//save new title
		wp_insert_post(array(
			'ID'=>$app_id,
			'post_title'=>'Application #' . $app_id,
			'post_type'=>'job_application',
			'post_status'=>'publish'
		));
		$secret = $this->_generateRandomString(10);
		add_post_meta($app_id, 'status', 'in_process');
		add_post_meta($app_id, 'user_email', $email);
		add_post_meta($app_id, 'secret', $secret);
		$application = $this->_formatApplication($app_id);
		$this->_sendJobAppLink($app_id);
		return $application;
	}
	public function getJobAppID ($email=false) {
		if ($email === false) {
			$email = $_REQUEST['user_email'];
		}
		$app_qry = new WP_Query(array(
			'post_type'=>'job_application',
			'meta_query'=>array(
				array(
					'key'=>'user_email',
					'value'=>$email,
					'compare'=>'='
				)
			)
		));
		if ($app_qry->have_posts()) {
			return $app_qry->post->ID;
		}
		return 0;
	}
	public function resendJobAppLink () {
		return $this->_sendJobAppLink($_REQUEST['app_id']);
	}
	public function requestEvent () {
		if (!wp_verify_nonce($_REQUEST['request_event_nonce'], 'requestEvent')) {
			throw new Exception('Nonce required. Please submit using http://riotstudios.com/request-an-event');
		}
		if (empty($_REQUEST['user_name'])) {
			throw new Exception('Name is required');
		}
		if (empty($_REQUEST['user_email'])) {
			throw new Exception('Email is required');
		}
		if ($_REQUEST['email_register'] == 'on') {
			$this->registerUser(array(
				'user_email'=>$_REQUEST['user_email'],
				'user_name'=>$_REQUEST['user_name'],
				'group_name'=>'Contact Source',
				'group_value'=>'Event Request'
			));
		}
		//create a new event request post type...
		$request_id = wp_insert_post(array(
			'post_type'=>'event_request'
		));
		//save new title
		wp_insert_post(array(
			'ID'=>$request_id,
			'post_title'=>'Event Request #' . $request_id,
			'post_type'=>'event_request',
			'post_status'=>'publish'
		));
		//add meta
		add_post_meta($request_id, 'status', 'no_contact');
		foreach($this->event_request_arr as $v) {
			if (isset($_REQUEST[$v])) {
				add_post_meta($request_id, $v, $_REQUEST[$v]);
			}
		}
		$email_msg = 'Alex,<br />';
		$email_msg .= 'We just got a new screening request from ' . $_REQUEST['user_name'] . ' (' . $_REQUEST['user_email'];
		if (!empty($_REQUEST['user_phone'])) {
			$email_msg .= ', ' . $_REQUEST['user_phone'];
		}
		$email_msg .= ').';
		$email_msg .= '<br /><br />';
		$email_msg .= 'Take a look at the details at: ' . site_url('request-an-event?request_id=' . $request_id . '&read_only=1') . '.';
		error_log($email_msg);
		$this->_sendEmail(array(
			'to'=>'alex@riotstudios.com',
			'subject'=>'Screening Request from ' . $_REQUEST['user_name'],
			'message'=> $email_msg
		));
		
		return $request_id;
	}
	public function loadMoreSquares () {
		global $post;
		$p = $this->params;
		$i = ($p['number'] * ($p['square_paged'] - 1));
		$post_array = array(
			'posts'=>'',
			'paged'=>($p['square_paged'] + 1)
		);
		$square_qry=new WP_Query(array(
			'post_type'=>getPostTypes($p['type']),
			'posts_per_page'=>$p['number'],
			'paged'=>$p['square_paged']
		));
		if ($square_qry->have_posts()) {
			while ($square_qry->have_posts()) {
				$square_qry->the_post();
				$post_array['posts'] .= do_home_square($i, false);
				$i += 1;
			}
			$post_array['maxPages'] = $square_qry->max_num_pages;
			return $post_array;
		} else {
			return false;
		}
	}
	public function nationBuilderPush () {
		$nb = new NationBuilder();
		return $nb->acessToken;
	}
	public function loadMoreTweets () {
		$page = $_REQUEST['tweet_page'];
		$twitter = new Twitter();
		$recent_tweets = $twitter->showRecentTweets($page, false);
		return $recent_tweets;
	}
	/*public function providentTest () {
		echo $this->_sendProvidentOrder(178);
		return true;
	}*/
	function __construct ($params=array()) {
		$this->params = $params;
	}
	private function _formatApplication ($app_id) {
		$application = get_post($app_id);
		foreach($this->job_application_arr as $v) {
			$application->$v = get_post_meta($app_id, $v, true);
		}
		$application->link = site_url('view-application?user_email=' . urlencode($application->user_email) . '&secret=' . urlencode($application->secret));
		$application->read_only_link = $application->link . '&readonly=1';
		$application->location = '';
		if (!empty($application->city)) {
			$application->location .= $application->city;
		}
		if (!empty($application->city) && !empty($application->state)) {
			$application->location .= ', ';
		}
		if (!empty($application->state)) {
			$application->location .= $application->state;
		}
		if (!empty($application->user_first_name)) {
			$application->display_name = $application->user_first_name;
			if (!empty($application->user_middle_name)) {
				$application->display_name .= ' ' . $application->user_middle_name;
			}
			if (!empty($application->user_last_name)) {
				$application->display_name .= ' ' . $application->user_last_name;
			}
		} else {
			$application->display_name = $application->user_email;
		}
		return $application;
	}
	private function _sendStreamingInfo($id){
		$order_lookup = get_post_meta($id, 'items', true);
		$customer_id = get_post_meta($id, 'customer', true);
		$customer_email = get_post_meta($customer_id, 'email', true);
		$items = (array) json_decode($order_lookup);
		$links = array();
		$message = '';
		foreach($items as $k=>$v) {
			if (!empty($v->watch_token)) {
				$links[$v->title] = site_url('theater') . '?wT=' . $v->watch_token . '&oI=' . $id;
			}
		}
		if (count($links) === 0) {
			return false;
		}
		if (count($links) === 1) {
			$subject = 'Riot Studios - Link to view Purchased Streaming Movie';
			$message = 'Here is your link to view the purchased video:<br /><br />';
		} else {
			$subject = 'Riot Studios - Links to view Purchased Streaming Movies';
			$message = 'Here are your links to view purchased videos:<br /><br />';
		}
		foreach($links as $k=>$v) {
			$message .= $kv . ' - ' . $v . '<br />';
		}
		$message .= '<br />You have up to 30 days to start watching your video. Once started, you\'ll have 3 days to finish it.<br /><br />We hope you enjoy. Post your review to <a href="http:/twitter.com/riotstudios">@riotstudios</a>!';
		return $this->_sendEmail(array(
			'to'=>$customer_email,
			'subject'=>$subject,
			'message'=>$message
		));
	}
	private function _getCartShipping ($cart, $ship = 'US') {
		$base_shipping = get_option('riot_shipping_domestic');
		$shipping = 0;
		if (is_string($cart)) {
			$cart = json_decode($cart);
		}
		$cart = (array) $cart;
		foreach($cart['items'] as $k=>$v) {
			if ($v->shipping) {
				$shipping += ($v->shipping * $v->qty);
				if ($ship !== 'US') {
					$shipping += ((float) get_option('riot_shipping_intl') * $v->qty);
				}
			}
		}
		if ($shipping > 0) {
			$shipping += $base_shipping;
		}
		return $shipping;
	}
	private function _getCartTotal ($cart = array(), $ship = 'US') {
		$total = 0;
		$base_shipping = get_option('riot_shipping_domestic');
		$shipping = 0;
		if (is_string($cart)) {
			$cart = json_decode($cart);
		}
		$cart = (array) $cart;
		foreach ($cart['items'] as $k=>$v) {
			$total += ($v->price * $v->qty);
		}
		$total += $this->_getCartShipping($cart, $ship);
		return $total;
	}
	private function _sendEmail ($arr, $html=true) {
		if (empty($arr['to'])) {
			throw new Exception('To: Address is required');
			return false;
		}
		if (empty($arr['subject'])) {
			throw new Exception('Subject is required to send email');
			return false;
		}
		if (empty($arr['message'])) {
			throw new Exception('Message is required to send email');
			return false;
		}
		if (empty($arr['headers']) && strpos($_SERVER['HTTP_HOST'], 'riotstudios.com') !== FALSE) {
			$headers[] = 'From: Riot Studios <info@riotstudios.com>';
			$headers[] = 'Reply-To: Riot Studios <info@riotstudios.com>';
			$arr['headers'] = $headers;
		}
		if ($html) {
			add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
		}
		$response = wp_mail($arr['to'], $arr['subject'], $arr['message'], $arr['headers']);
		return $response;
	}
	private function _sendInvoiceEmail ($id = false) {
		if (!$id) {
			throw new Exception('ID is required to send invoice email');
			return false;
		}
		$logo_image = get_bloginfo('template_directory') . '/img/logo-50.jpg';
		$border_image = get_bloginfo('template_directory') . '/img/border-image.jpg';
		$order = $this->_getInvoice($id);
		$html = '<html>';
		$html .= '<table border="0" cellpadding="0" cellspacing="0" align="center" width="500">
<tbody>
	<tr id="header">
		<td>
			<table>
				<tr>
					<td align="left">
						<img src="' . $logo_image . '" width="113" height="50" />
					</td>
					<td align="right">
						<font size="3" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">Receipt</font>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<img width="500" height="1" src="' . $border_image . '" />
					</td>
				</tr>
			</table>
			<br/>
			<br/>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tbody>
					<tr>
						<td>
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
								<b>Billed To:</b>
							</font>
						</td>
					</tr>
					<tr>
						<td width="320">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
								' . $order->customer->name . '<br/>
								<a href="mailto:' . $order->customer->email . '" >' . $order->customer->email . '</a><br/>';
								if (!empty($order->customer->phone)) {
									$html .= $order->customer->phone . '<br/>';
								}
								'<br/>' . $order->customer->name . '<br/>' . $order->bill_address->street . '<br/>';
								if (!empty($order->bill_address->street2)) {
									$html .= $order->bill_address->street2 . '<br/>';
								}
								$html .= $order->bill_address->city . ', ' . $order->bill_address->state . ' ' . $order->bill_address->zip . '<br/>' . $order->bill_address->country . '
           					</font>
						</td>
						<td valign="top">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
								<b>Invoice # : </b>' . $id . '<br/>
								<b>Receipt Date: </b>' . $order->date . ' <br/>
								<b>Order Total: </b>$ ' . $order->total . '<br/>
								<b>Billed To: </b>' . $order->stripe->card->type . ' ... ' . $order->stripe->card->last4 . '<br/>
							</font>
						</td>
					</tr>
				</tbody>
			</table>
			<br/>
			<br/>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tbody>
					<tr>
						<td colspan="4" height="12" valign="top">
							<img src="' . $border_image . '" width="500" height="1" />
						</td>
					</tr>
					<tr>
						<td>
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
								<b>Item</b>
							</font>
						</td>
						<td align="center">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
								<b>Qty</b>
							</font>
						</td>
						<td align="right">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
								<b>Unit Price</b>
							</font>
						</td>
						<td align="right">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
								<b>Total</b>
							</font>
						</td>
					</tr>
					<tr>
						<td colspan="4" height="12" valign="bottom">
							<img src="' . $border_image . '" width="500" height="1" />
						</td>
					</tr>
					<tr>
						<td colspan="4" height="8"></td>
					</tr>';
					
				foreach($order->items as $k=>$v):
					$html .= '<tr>
						<td><font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">' . $v->title . '</font></td>
						<td align="center"><font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">' . $v->qty . '</font></td>
						<td align="right"><font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">$' . money_format('%i', $v->price) . '</font></td>
						<td align="right"><font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">$' . money_format('%i', ($v->qty * $v->price)) . '</font></td>
					</tr>';
				endforeach;
					
					$html .= '<tr>
						<td colspan="4" height="24" valign="middle">
							<img src="' . $border_image . '" width="500" height="1" />
						</td>
					</tr>
					<tr>
						<td align="right" colspan="3">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">Shipping:</font>
						</td>
						<td align="right">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">$' . $order->shipping . '</font>
						</td>
					</tr>
					<tr>
						<td colspan="4" height="24" valign="middle">
							<img src="' . $border_image . '" width="500" height="1" />
						</td>
					</tr>
					<tr>
						<td align="right" colspan="3">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif"><b>Total:</b></font>
						</td>
						<td align="right">
							<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">$' . $order->total . '</font>
						</td>
					</tr>
					<tr>
						<td colspan="4" height="24" valign="middle">
							<img src="' . $border_image . '" width="500" height="1" />
						</td>
					</tr>
				</tbody>
			</table>
			<br/>
			<br/>
			<br/>
		</td>
	</tr>
	<tr>
		<td align="left">
			<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
				<b>Please Retain for your Records.</b>
			</font>
			<br/>
			<font size="2" color="#999999" face="Lucida Grande, Lucida Sans Unicode, sans-serif">Any questions about your order can be answered at <a href="mailto:info@riotstudios.com">info@riotstudios.com</a> or 214.686.0939.
			</font>
			<br/>
			<br/>
			<font size="2" color="#333333" face="Lucida Grande, Lucida Sans Unicode, sans-serif">
				<b>We appreciate your support.</b>
			</font>
			<br/>
			<font size="2" color="#999999" face="Lucida Grande, Lucida Sans Unicode, sans-serif">Domestic (United States) orders should arrive in the mail within 3-5 business days.  Please feel free to contact us if you have any questions or have not received it in a timely manner.  We hope our products are a blessing to you and that you\'ll continue to spread the word about our projects and their messages.  Have a wonderful day.</font>
			<br/>
			<br/>
			<br/>
			<br/>
			<center>
				<font size="1.5" color="#999999" face="Lucida Grande, Lucida Sans Unicode, sans-serif">Copyright &copy; 2013 Riot Studios LLC.  All Rights reserved.<br/><br/>This receipt has been sent to ' . $order->customer->name . ' at <a style="color:#666;" href="mailto:' . $order->customer->email . '">' . $order->customer->email . '</a>.  If you are not ' . $order->customer->name . ', please contact us at <a style="color:#666;" href="mailto:info@riotstudios.com">info@riotstudios.com</a> and let us know.
				</font>
			</center>
		</td>
	</tr>
</tbody>
</table>';
		return $this->_sendEmail(array(
			'to'=>$order->customer->email,
			'subject'=>'Riot Studios - Invoice #' . $id,
			'message'=>$html
		));
	}
	private function _getCustomer ($id) {
		$customer = get_post($id);
		if (empty($customer) || $customer->post_type !== 'customer') {
			throw new Exception('No customer with that ID');
		}
		$meta = (array) get_post_meta($id);
		foreach ($meta as $k=>$v) {
			$customer->$k = $v[0];
		}
		return $customer;
	}
	private function _getInvoice ($id) {
		$order = get_post($id);
		if (empty($order) || ($order->post_type !== 'invoice' && $order->post_type !== 'order')) {
			throw new Exception('No order with that ID');
		}
		//get Stripe
		require_once('Stripe/Stripe.php');
		
		if (home_url() === 'http://riotstudios.com' || home_url() === 'https://riotstudios.com') {
			Stripe::setApiKey("sk_live_et97Q4DOC0sB3n7MUa2JSTca");
		} else {
			Stripe::setApiKey("sk_test_PtucQ9cVM9KM15lNU2RmZsbb");
		}
		
		//include meta details
		$order_meta = (array) get_post_meta($id);
		foreach ($order_meta as $k=>$v) {
			$order->$k = $v[0];
		}
		$order->ship_address = json_decode($order->ship_address);
		if (empty($order->bill_address)) {
			$order->bill_address = $order->ship_address;
		} else {
			$order->bill_address = json_decode($order->bill_address);
		}
		$order->items = (array) json_decode($order->items);
		$order->customer = $this->_getCustomer($order->customer);
		$order->date = date('Y-m-d', strtotime($order->post_date));
		$order->total = $order->total / 100;
		$order->shipping = $order->shipping / 100;
		
		//retrieve stripe data
		if ($order->stripe) {
			$order->stripe = Stripe_Charge::retrieve($order->stripe);
		}
		
		return $order;
	}
	private function _emailSubscribe ($arr = array()) {
		//separate email from rest of vars
		if (is_string($arr)) {
			$email = $arr;
			$arr = array();
			/*
			OPTIONS FOR ARR DATA:
			 - FNAME, LNAME
			 - ADDRESS (array with addr1, addr2, city, state, zip, country
			 - date YYYY-MM-DD
			 - phone
			 - zip
			 - GROUPINGS
			*/
		} else {
			$email = $arr['email'];
			unset($arr['email']);
		}
		$arr = array_merge(array(
			'update_existing'=>true,
			'send_welcome'=>false,
			'double_opt_in'=>false
		), $arr);
		$update_existing = $arr['update_existing'];
		$send_welcome = $arr['send_welcome'];
		$double_opt_in = $arr['double_opt_in'];
		unset($arr['update_existing']);
		unset($arr['send_welcome']);
		unset($arr['double_opt_in']);
				
		//create a mailchimp API instance
		require ('MailChimp/MCAPI.class.php');
		$MC = new MCAPI('0748ef55588e35ed4333e57e1edffdc0-us5');
		
		//first get the list ID
		//$lists = $MC->lists();
		$list_id = '5b55587b69';
				
		$subscribe = $MC->listSubscribe(
			$list_id,
			$email,
			$arr,//MERGE VARS
			'html',//html or plain text email?
			$double_opt_in,//don't send a double optin message,
			$update_existing,//update existing user?
			false,//replace interests?
			$send_welcome//send a welcome email?
		);		
		if ($MC->errorCode) {
			return $MC->errorMessage;
		} else {
			return true;
		}
	}
	private function _generateRandomString ($length = 10) {
		$original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
		$original_string = implode("", $original_string);
		return substr(str_shuffle($original_string), 0, $length);
	}
	private function _evalFullName ($full_name) {
		$full_name_arr = explode(' ', $full_name);
		$fname = $full_name_arr[0];
		$lname = $full_name_arr[count($full_name_arr) - 1];
		return array(
			'first'=>$fname,
			'last'=>$lname
		);
	}
	private function _sendJobAppLink ($id) {
		$app = $this->_formatApplication($id);
		$message = 'Hey There,<br /><br />';
		$message .= 'Looks like you started a job application to join the team at Riot Studios. In case you need it, here\'s a link to pick up where you left off:<br /><br />';
		$message .= $app->link . '<br /><br />';
		$message .= 'Give us a call at 214.686.0939 if you have any questions or problems finishing your app.<br /><br />--<br />The Riot Team';
		return $this->_sendEmail(array(
			'to'=>$app->user_email,
			'subject'=>'Link to Your Job Application for Riot Studios',
			'message'=>$message
		));
	}
	//provident API functions
	private function _sendProvidentOrder ($id) {
		$obString = $this->_createOrderBundleString($id);
		$request = $this->_sendSoapToProvident('createOrder', $obString);
		if ($request) {
			$xml = @simplexml_load_string($request);
			if ($xml) {
				$result = (string)$xml->children('soap', true)
					->Body
					->children()
					->createOrderResponse
					->createOrderResult;
			} else {
				$result = false;
			}
		} else {
			$result = false;
		}
		
		
		if (!$request || $request == 'BAD') {
			//need to email admin to notify Provident didn't get the order...
			$this->_sendEmail(array(
				'to'=>'michael@riotstudios.com',
				'subject'=>'Failed SOAP request to Provident - Order ' . $id,
				'message'=>"Provident didn't receive the deets for Order " . $id . " the xml response was:<br/><br/>" . $request
			));
			return false;
		} else {
			return $result;
		}
	}
	private function _sendSoapToProvident($action, $xml){	
		$url= 'http://dc.artistservices.com/RIOTv1/dcservice.asmx';
		$soap_request  = '<?xml version="1.0" encoding="utf-8"?>';
		$soap_request .= '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">';
		$soap_request .= '<soap:Body>' . $xml . '</soap:Body></soap:Envelope>';
		$header = 'POST ' . $url . ' HTTP/1.1
VsDebuggerCausalityData: uIDPo+waSxDsV+1DtbrsHagQ6koAAAAAVQx/C5kTN0SSuRoJtokkh/2ll6nphTBOqnN6snOhgHIACQAA
Content-Type: text/xml; charset=utf-8
SOAPAction: "http://dc.artistservices.com/v1/' . $action . '"
Host: dc.artistservices.com
Content-Length: ' . strlen($soap_request) . '
Expect: 100-continue
Connection: Keep-Alive

' . $soap_request;
		$soap_do = curl_init();
		curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($soap_do, CURLOPT_URL, $url);
		curl_setopt($soap_do, CURLOPT_CUSTOMREQUEST, $header);
		curl_setopt($soap_do, CURLOPT_POST, TRUE);
		curl_setopt($soap_do, CURLOPT_HEADER, 0);
		curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, TRUE);
		$httpResp = curl_exec($soap_do);
		return $httpResp;
	}
	private function _createOrderBundleString ($id) {
		
		//general riot options
		$riot_flat_shipping_fee = get_option('riot_shipping_domestic');
		
		//lookup the order in our db
		$order = $this->_getInvoice($id);
		
		$ship_method = 'FCM';
		
		$num_intl_lines=count($order->items);
		
		//generated by sender in accordance with Provident rules
		$orderbundle = '<OrderBundle xmlns="https://741Productions.com/dsOrderBundle.xsd"><Order>';
		$orderbundle .= '<OrderNumber>' . $id . '</OrderNumber>';
		
		//generated by sender in accordance with Provident rules
		$orderbundle .= '<CustomerNumber>' . $order->customer->ID . '</CustomerNumber>';
		
		//date is really all we need in yyyy-mm-dd format
		$orderbundle .= '<DateCreated>' . $order->date . '</DateCreated>';

		if (home_url() === 'http://riotstudios.com' || home_url() === 'https://riotstudios.com') {
			$orderbundle .= '<SiteID>RIOT</SiteID>';
		} else {
			$orderbundle .= '<SiteID>RIOT1</SiteID>';
		}

		$orderbundle .= '<TaxRate>0.0</TaxRate>';
		
		// $orderbundle .= '<ShippingTax>N</ShippingTax>'; //should tax be charged on Shipping (Y/N flag)
		$orderbundle .= '<BillingName>' . $order->customer->name . '</BillingName>';
		$orderbundle .= '<BillingPhone>' . $order->customer->phone . '</BillingPhone>';
		$orderbundle .= '<BillingEmail>' . $order->customer->email . '</BillingEmail>';
		$orderbundle .= '<BillingAddress1>' . $order->bill_address->street . '</BillingAddress1>';
		$orderbundle .= '<BillingAddress2>' . $order->bill_address->street2 . '</BillingAddress2>';
		$orderbundle .= '<BillingCity>' . $order->bill_address->city . '</BillingCity>';
		$orderbundle .= '<BillingState>' . $order->bill_address->state . '</BillingState>';
		$orderbundle .= '<BillingPostalCode>' . $order->bill_address->zip . '</BillingPostalCode>';
		$orderbundle .= '<BillingCountry>' . $order->bill_address->country . '</BillingCountry>';
		
		//Gift Y or N – controls if prices and a gift message print on the order summary
		$orderbundle .= '<Gift>N</Gift>';
		
		//gift message if one exists
		$orderbundle .= '<GiftMessage></GiftMessage>';
		
		$orderbundle .= '<ShipName>' . $order->customer->name . '</ShipName>';
		$orderbundle .= '<ShippingPhone>' . $order->customer->phone . '</ShippingPhone>';
		$orderbundle .= '<ShippingEmail>' . $order->customer->email . '</ShippingEmail>';
		$orderbundle .= '<ShipAddress1>' . $order->ship_address->street . '</ShipAddress1>';
		$orderbundle .= '<ShipAddress2>' . $order->ship_address->street2 . '</ShipAddress2>';
		$orderbundle .= '<ShipCity>' . $order->ship_address->city . '</ShipCity>';
		$orderbundle .= '<ShipState>' . $order->ship_address->state . '</ShipState>';
		$orderbundle .= '<ShipPostalCode>' . $order->ship_address->zip . '</ShipPostalCode>';
		$orderbundle .= '<ShipCountry>' . $order->ship_address->country . '</ShipCountry>';
		
		//Provident will supply the codes to use – this one is for USPS parcel post
		$orderbundle .= '<ShipVia>' . $ship_method . '</ShipVia>';
		
		$orderbundle .= '<FreeShipping>N</FreeShipping>';
		//$orderbundle .= '<OrderShippingFee>25.00</OrderShippingFee><ShipComplete>Y</ShipComplete>'; //Ship Complete Y or N
		
		//number of lines on the order
		$orderbundle .= '<TotalLines>' . count($order->items) . '</TotalLines>';

		foreach ($order->items as $k=>$v) :
			//get the product sku number
			$sku = get_post_meta($k, '_sku_code', true);
			
			$orderbundle .= '<OrderLine>';
			$orderbundle .= '<OrderNumber>' . $id . '</OrderNumber>';
			
			//unique line id in sender's database – used for order status updates by line
			$orderbundle .= '<LineID>' . $id . '--l--' . $k . '</LineID>';
			
			//SKU number for the product: UPC
			$orderbundle .= '<ProductID>' . $sku . '</ProductID>';
			
			$orderbundle .= '<Quantity>' . $v->qty . '</Quantity>';
			$orderbundle .= '<LineTotalBT>' . money_format('%i', ($v->price * $v->qty)) . '</LineTotalBT>';
			$orderbundle .= '<LineTotalAT>'.money_format('%i', ($v->price * $v->qty)) . '</LineTotalAT>';
			$orderbundle .= '<LineStatus>InStock</LineStatus>';
			$orderbundle .= '<ShippingFlatFee>' . $riot_flat_shipping_fee . '</ShippingFlatFee>';
			$orderbundle .= '<ShippingPerItemFee>0.99</ShippingPerItemFee>';
			
			//number of separate intl shipping line descriptors for this product
			$orderbundle .= '<IShippingLines>' . $num_intl_lines . '</IShippingLines>';
			
			/*
			if ($num_intl_lines > 0) :
				$orderbundle .= '<IShippingDetails>'//Loop containing one set of intl shipping details
				$orderbundle .= '<HarmonizedCode>123</HarmonizedCode>';
				$orderbundle .= '<Quantity>' . $itemqtys[$k] . '</Quantity>';
				$orderbundle .= '<Description>DVD</Description>';
				$orderbundle .= '<CountryOrigin>US</CountryOrigin>';
				$orderbundle .= '<PerUnitAmount>' . $itemprices[$k] . '</PerUnitAmount>';
				$orderbundle .= '</IShippingDetails>';
			endif;
			*/
			//$orderbundle .= '<CustomsValue>' . ($itemprices[$k] * $itemqtys[$k]) . '</CustomsValue>';
			$orderbundle .= '</OrderLine>';
		endforeach;

		$orderbundle .= '</Order></OrderBundle>';
		$orderbundle = '<createOrder xmlns="http://dc.artistservices.com/v1/"><OrderBundle>' . htmlentities($orderbundle, ENT_NOQUOTES) . '</OrderBundle></createOrder>';
		return $orderbundle;
	}
}