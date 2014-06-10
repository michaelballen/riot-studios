<?php
//register all our custom post types...
function create_post_types () {
	register_post_type( 'article',
		array(
			'labels' => array(
				'name' => __( 'Articles' ),
				'singular_name' => __( 'Article' ),
				'add_new' => __('Add Article'),
				'edit_item' => __('Edit Article'),
				'view_item' => __('View Article'),
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array(
				'title',
				'thumbnail',
				'editor'
			),
			'rewrite'=>array(
				'slug'=>'blog'
			)
		)
	);
	register_post_type( 'event_request',
		array(
			'public' => false,
			'has_archive' => false
		)
	);
	register_post_type( 'employment_opening',
		array(
			'labels' => array(
				'name' => __( 'Jobs' ),
				'singular_name' => __( 'Job' ),
				'add_new' => __('Add Job'),
				'edit_item' => __('Edit Job'),
				'view_item' => __('View Job'),
			),
			'public' => false,
			'has_archive' => false,
			'show_ui'=>true,
			'supports' => array(
				'title',
				'editor'
			)
		)
	);
	register_post_type( 'job_application',
		array(
			'public' => true,
			'has_archive' => true,
			'rewrite'=>array(
				'slug'=>'job-apps'
			)
		)
	);
	register_post_type( 'home_slide',
		array(
			'labels' => array(
				'name' => __( 'Home Slides' ),
				'singular_name' => __( 'Home Slide' ),
				'add_new' => __('Add Slide'),
				'edit_item' => __('Edit Slide'),
				'view_item' => __('View Slide'),
			),
			'public' => false,
			'has_archive' => false,
			'supports' => array(
				'title',
				'thumbnail'
			),
			'show_ui' => true,
			'register_meta_box_cb' => 'add_home_slide_meta'
		)
	);
	register_post_type( 'home_square',
		array(
			'labels' => array(
				'name' => __( 'Home Squares' ),
				'singular_name' => __( 'Home Square' ),
				'add_new' => __('Add Square'),
				'edit_item' => __('Edit Square'),
				'view_item' => __('View Square'),
			),
			'public' => false,
			'has_archive' => false,
			'supports' => array(
				'title',
				'thumbnail'
			),
			'show_ui' => true,
			'register_meta_box_cb' => 'add_home_square_meta'
		)
	);
	register_post_type( 'video',
		array(
			'labels' => array(
				'name' => __( 'Videos' ),
				'singular_name' => __( 'Video' ),
				'add_new' => __('Add Video'),
				'edit_item' => __('Edit Video'),
				'view_item' => __('View Video')
			),
			'public' => true,
			'has_archive' => false,
			'supports' => array(
				'title',
				'thumbnail',
				'editor'
			),
			'register_meta_box_cb' => 'add_video_meta'
		)
	);
	register_post_type( 'image',
		array(
			'labels' => array(
				'name' => __( 'Images' ),
				'singular_name' => __( 'Image' ),
				'add_new' => __('Add Image'),
				'edit_item' => __('Edit Image'),
				'view_item' => __('View Image')
			),
			'public' => true,
			'has_archive' => false,
			'supports' => array(
				'title',
				'thumbnail',
				'editor'
			)
		)
	);
	register_post_type( 'product',
		array(
			'labels' => array(
				'name' => __( 'Products' ),
				'singular_name' => __( 'Product' ),
				'add_new' => __('Add Product'),
				'edit_item' => __('Edit Product'),
				'view_item' => __('View Product')
			),
			'public' => true,
			'has_archive' => false,
			'supports' => array(
				'title',
				'thumbnail',
				'editor'
			),
			'register_meta_box_cb' => 'add_product_meta',
			'rewrite' => array(
				'slug'=>'store'
			),
		)
	);
	register_post_type( 'team_member',
		array(
			'labels' => array(
				'name' => __( 'Team Members' ),
				'singular_name' => __( 'Team Member' ),
				'add_new' => __('Add Team Member'),
				'edit_item' => __('Edit Team Member'),
				'view_item' => __('View Team Member')
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array(
				'title',
				'thumbnail',
				'editor',
				'page-attributes'
			),
			'rewrite' => array(
				'slug'=>'team'
			),
			'register_meta_box_cb' => 'add_team_meta'
		)
	);
	register_post_type( 'project',
		array(
			'labels' => array(
				'name' => __( 'Projects' ),
				'singular_name' => __( 'Project' ),
				'add_new' => __('Add Project'),
				'edit_item' => __('Edit Projects'),
				'view_item' => __('View Projects')
			),
			'show_ui'=> true,
			'public' => false,
			'register_meta_box_cb' => 'add_project_meta',
			'has_archive' => true,
			'supports' => array(
				'title',
				'thumbnail',
				'editor'
			),
		)
	);
	register_post_type( 'customer',
		array(
			'labels' => array(
				'name' => __( 'Customers' ),
				'singular_name' => __( 'Customer' ),
				'add_new' => __('Add Customer'),
				'edit_item' => __('Edit Customer'),
				'view_item' => __('View Customer')
			),
			'public' => false,
			'has_archive' => false,
			'supports' => array()
		)
	);
	register_post_type( 'invoice',
		array(
			'labels' => array(
				'name' => __( 'Invoices' ),
				'singular_name' => __( 'Invoice' ),
				'add_new' => __('Add Invoice'),
				'edit_item' => __('Edit Invoice'),
				'view_item' => __('View Invoice')
			),
			'public' => true,
			'show_ui' => true,
			'has_archive' => true,
			'rewrite' => array(
				'slug' => 'invoices',
				'with_front' => false,
				'pages' => false
			)
		)
	);
}
// Add the Events Meta Boxes
function add_product_meta () {
    add_meta_box('riot_product_info', 'Product Info', 'product_price_meta_html', 'product', 'side', 'default');
}
function add_home_slide_meta () {
    add_meta_box('riot_home_slide_info', 'Link Info', 'home_slide_meta_html', 'home_slide', 'normal', 'high');
}
function add_home_square_meta () {
    add_meta_box('riot_home_square_info', 'Link Info', 'home_square_meta_html', 'home_square', 'normal', 'high');
}
function add_project_meta () {
    add_meta_box('riot_project_info', 'Extra Links', 'project_meta_html', 'project', 'side', 'default');
}
function product_price_meta_html () {
	global $post;
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="product_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	// Get the pricing data if its already been entered
	$price = get_post_meta($post->ID, '_price', true);
	$min_price = get_post_meta($post->ID, '_min_price', true);
	$nyop = get_post_meta($post->ID, '_nyop', true);
	$product_type = get_post_meta($post->ID, '_product_type', true);
	$price_label = $nyop == '1' ? 'Default Price' : 'Price';
	$min_price_class = $nyop == '1' ? '' : ' hidden';
	//echo nyop label + input
	echo '<label>Name Your Price?</label>
	<br>
	<select name="_nyop">
		<option value="1"';
	if ($nyop == '1') {
		echo ' selected="1"';
	}
	echo '>Yes</option>
		<option value="0"';
	if ($nyop != '1') {
		echo ' selected="1"';
	}
	echo '>No</option>
	</select>
	<br>';
	
	//echo type of product label + input
	echo '<label>Product Type</label>
	<br>
	<select name="_product_type">
		<option value="dvd"';
	if ($product_type === 'dvd') {
		echo ' selected="1"';
	}
	echo '>DVD</option>
		<option value="stream"';
	if ($product_type === 'stream') {
		echo ' selected="1"';
	}
	echo '>Stream</option>
	<option value="other"';
if ($product_type === 'other' || !$product_type) {
	echo ' selected="1"';
}
echo '>Other</option>
	</select>
	<br>';
	
	// Echo out the price fields
	echo '<label for="_price">' . $price_label . '</label><input type="text" name="_price" value="' . $price . '" class="widefat" />';
	echo '<div class="min-price' . $min_price_class . '"><label for="_min_price">Minimum Price</label><input type="text" name="_min_price" value="' . $min_price . '" class="widefat" /></div>';
	
	// Get the pricing data if its already been entered
	$ship_price = get_post_meta($post->ID, '_ship_price', true);
	// Echo out the field
	echo '<label for="_ship_price">Shipping Price ($/per unit)</label><input data-default="' . $ship_price . '" type="text" name="_ship_price" value="' . $ship_price . '" class="widefat" placeholder="ex: 0.99" />';
	
	// Get the sku data if its already been entered
	$sku_code = get_post_meta($post->ID, '_sku_code', true);
	// Echo out the field
	echo '<label for="_sku_code">SKU Code (if shipping with Provident)</label><input type="text" name="_sku_code" value="' . $sku_code . '" class="widefat" />';
	
	// Get the trailer link if its already been entered
	$trailer_link = get_post_meta($post->ID, '_trailer_link', true);
	// Echo out the field
	echo '<label for="_trailer_link">Trailer (Youtube Link)</label><input type="text" name="_trailer_link" value="' . $trailer_link . '" class="widefat" />';
	// Get the website link if its already been entered
	$website_link = get_post_meta($post->ID, '_website_link', true);
	// Echo out the field
	echo '<label for="_website_link">Website</label><input type="text" name="_website_link" placeholder="http://" value="' . $website_link . '" class="widefat" />';
		
	// Get the trailer link if its already been entered
	$streaming_title = get_post_meta($post->ID, '_streaming_title', true);
	// Echo out the field
	echo '<div class="streaming-input-group';
	if ($product_type !== 'stream') {
		echo ' hidden';
	}
	echo '"><label for="_streaming_title">Streaming Video (without extension)</label><input type="text" name="_streaming_title" placeholder="ex: beware-of-christians" value="' . $streaming_title . '" class="widefat" /></div>';
	
	?>
	<script>
	(function () {
		jQuery(document).ready(function ($) {
			var $meta_box = $('#riot_product_info'),
				$nyop_select = $meta_box.find('select[name="_nyop"]'),
				$p_type_select = $meta_box.find('select[name="_product_type"]'),
				$price_label = $meta_box.find('label[for="_price"]'),
				$min_price_div =  $meta_box.find('div.min-price'),
				$ship_price_input = $meta_box.find('input[name="_ship_price"]');
				$streamingInput = $meta_box.find('.streaming-input-group');
				updateLabel = function () {
					var val = $(this).val();
					if (val === '1') {
						$min_price_div.removeClass('hidden');
						$price_label.text('Default Price');
					} else {
						$min_price_div.addClass('hidden');
						$price_label.text('Price');
					}
				},
				updateProductType = function () {
					var v = $(this).val();
					if (v === 'stream') {
						$ship_price_input.val('0');
						$streamingInput.removeClass('hidden');
					} else if (parseFloat($ship_price_input.val()) === 0) {
						$ship_price_input.val($ship_price_input.data('default'));
						$streamingInput.addClass('hidden');
					}
				};
			$nyop_select.on('change.updateLabel', updateLabel);
			$p_type_select.on('change.updateLabel', updateProductType);
		});
	}());
	</script>
	<?php
}
function project_meta_html () {
	global $post;
	
	echo '<input type="hidden" name="project_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	// Get the pricing data if its already been entered
	$buy_link = get_post_meta($post->ID, '_buy_link', true);
	// Echo out the field
	echo '<label for="_buy_link">Link to Buy</label><input type="text" name="_buy_link" value="' . $buy_link . '" class="widefat" placeholder="http://" />';
		
	$stream_link = get_post_meta($post->ID, '_stream_link', true);
	// Echo out the field
	echo '<label for="_stream_link">Link to Stream</label><input type="text" name="_stream_link" value="' . $stream_link . '" class="widefat" placeholder="http://" />';
	
	// Get the sku data if its already been entered
	$website_link = get_post_meta($post->ID, '_website_link', true);
	// Echo out the field
	echo '<label for="_website_link">Website Link</label><input type="text" name="_website_link" value="' . $website_link . '" placeholder="http://" class="widefat" />';
}
function home_slide_meta_html () {
	global $post;
	
	echo '<input type="hidden" name="home_slide_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	// Get the pricing data if its already been entered
	$hs_link = get_post_meta($post->ID, '_link', true);
	// Echo out the field
	echo '<label for="_link">Link</label><input type="text" name="_link" value="' . $hs_link . '" class="widefat" placeholder="http://" />';
}
function home_square_meta_html () {
	global $post;
	
	echo '<input type="hidden" name="home_square_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	// Get the pricing data if its already been entered
	$hs_link = get_post_meta($post->ID, '_link', true);
	// Echo out the field
	echo '<label for="_link">Link</label><input type="text" name="_link" value="' . $hs_link . '" class="widefat" placeholder="http://" />';
}

function add_video_meta () {
    add_meta_box('riot_video_url', 'Youtube URL', 'video_meta_html', 'video', 'normal', 'default');
}
function video_meta_html () {
	global $post;
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="vidurl_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	// Get the vid url data if its already been entered
	$vidurl = get_post_meta($post->ID, '_vidurl', true);
	$vidtype = get_post_meta($post->ID, '_vidtype', true);
	$vidid = get_post_meta($post->ID, '_vidid', true);
	$vidimage = get_post_meta($post->ID, '_vidimage', true);
	// Echo out the field
	echo '<a href="' . $vidurl . '" target="_blank"><img src="' . $vidimage . '" style="height:200px; width:auto;"/></a>';
	echo '<input type="hidden" name="_vidtype" value="' . $vidtype . '" />';
	echo '<input type="hidden" name="_vidid" value="' . $vidid . '" />';
	echo '<input type="text" name="_vidurl" value="' . $vidurl . '" class="widefat" />';
}
function add_team_meta () {
    add_meta_box('riot_team_meta', 'Position', 'team_meta_html', 'team_member', 'side', 'default');
}
function team_meta_html () {
	global $post;
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="team_member_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	// Get the vid url data if its already been entered
	$riot_position = get_post_meta($post->ID, 'riot_position', true);
	$bm_crew_position = get_post_meta($post->ID, 'bm_crew_position', true);
	$bm_cast_position = get_post_meta($post->ID, 'bm_cast_position', true);
	// Echo out the field
	echo '<label>Riot Position</label><input type="text" name="riot_position" value="' . $riot_position . '" class="widefat" /><br>';
	echo '<label>Believe Me Cast Role</label><input type="text" name="bm_cast_position" value="' . $bm_cast_position . '" class="widefat" /><br>';
	echo '<label>Believe Me Crew Position</label><input type="text" name="bm_crew_position" value="' . $bm_crew_position . '" class="widefat" /><br>';
	
}

// Save the Metabox Data
function save_product_meta ($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['product_noncename'], plugin_basename(__FILE__) )) {
		return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $product_meta['_price'] = $_POST['_price'];
	$product_meta['_min_price'] = $_POST['_min_price'];
	$product_meta['_nyop'] = $_POST['_nyop'];
	$product_meta['_product_type'] = $_POST['_product_type'];
	$product_meta['_ship_price'] = $_POST['_ship_price'];
	$product_meta['_sku_code'] = $_POST['_sku_code'];
	$product_meta['_trailer_link'] = $_POST['_trailer_link'];
	$product_meta['_website_link'] = $_POST['_website_link'];
	$product_meta['_streaming_title'] = $_POST['_streaming_title'];
	
	if (!empty($product_meta['_trailer_link'])) {
		$vidtype = getVideoType($product_meta['_trailer_link']);
		$product_meta['_trailer_id'] = getVideoId($vidtype, $product_meta['_trailer_link']);
	}
	
    // Add values of $events_meta as custom fields
    foreach ($product_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
    }
}
function save_video_meta ($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['vidurl_noncename'], plugin_basename(__FILE__) )) {
		return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $events_meta['_vidurl'] = $_POST['_vidurl'];
	$events_meta['_vidtype'] = getVideoType($events_meta['_vidurl']);
	$events_meta['_vidid'] = getVideoId($events_meta['_vidtype'], $events_meta['_vidurl']);
	$events_meta['_vidimage'] = getVideoImage($events_meta['_vidtype'], $events_meta['_vidid']);
    // Add values of $events_meta as custom fields
    foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}
function save_team_meta ($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['team_member_noncename'], plugin_basename(__FILE__) )) {
		return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $events_meta['riot_position'] = $_POST['riot_position'];
	$events_meta['bm_crew_position'] = $_POST['bm_crew_position'];
	$events_meta['bm_cast_position'] = $_POST['bm_cast_position'];
    // Add values of $events_meta as custom fields
    foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}
function save_home_slide_meta ($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['home_slide_noncename'], plugin_basename(__FILE__) )) {
		return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $events_meta['_link'] = $_POST['_link'];
	
	//is this a post within riot?
	$riot_post_id = bwp_url_to_postid($_POST['_link']);
	if ($riot_post_id !== 0) {
		$events_meta['_riot_id'] = $riot_post_id;
		$events_meta['_link_post_type'] = get_post_type($riot_post_id);
	} else {
		$events_meta['_riot_id'] = false;
		$events_meta['_link_post_type'] = false;
	}
	
    // Add values of $events_meta as custom fields
    foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}
function save_home_square_meta ($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['home_square_noncename'], plugin_basename(__FILE__) )) {
		return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $meta['_link'] = $_POST['_link'];
	
	//is this a post within riot?
	$riot_post_id = url_to_postid($_POST['_link']);
	if ($riot_post_id !== 0) {
		$meta['_riot_id'] = $riot_post_id;
	} else {
		$meta['_riot_id'] = false;
	}
	
    // Add values of $events_meta as custom fields
    foreach ($meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}
function save_project_meta ($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if (!wp_verify_nonce($_POST['project_noncename'], plugin_basename(__FILE__))) {
		return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if (!current_user_can( 'edit_post', $post->ID)) {
		return $post->ID;
	}
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $project_meta['_buy_link'] = $_POST['_buy_link'];
	$project_meta['_website_link'] = $_POST['_website_link'];
	$project_meta['_stream_link'] = $_POST['_stream_link'];
    // Add values of $events_meta as custom fields
    foreach ($project_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type === 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}

function remove_menu_items () {
	global $menu;
	$restricted = array(
		__('Links'),
		__('Comments'),
		//__('Media'),
		__('Tools'),
		__('Posts')
	);
	end ($menu);
	while (prev($menu)) {
		$value = explode(' ',$menu[key($menu)][0]);
		if (in_array($value[0] != NULL ? $value[0] : "" , $restricted)) {
			unset($menu[key($menu)]);
		}
	}
}
function custom_icons () {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-product .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/css/img/cart-icon.png) no-repeat center top !important;
        }
		#menu-posts-product:hover .wp-menu-image, #menu-posts-product.wp-has-current-submenu .wp-menu-image {
            background-position:center bottom !important;
        }
		#menu-posts-team_member .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/css/img/user-icon.png) no-repeat center top !important;
        }
		#menu-posts-team_member:hover .wp-menu-image, #menu-posts-team_member.wp-has-current-submenu .wp-menu-image {
            background-position:center bottom !important;
        }
		#menu-posts-project .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/css/img/list-icon.png) no-repeat center top !important;
        }
		#menu-posts-project:hover .wp-menu-image, #menu-posts-project.wp-has-current-submenu .wp-menu-image {
            background-position:center bottom !important;
        }
		#menu-posts-article .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/css/img/article-icon.png) no-repeat center top !important;
        }
		#menu-posts-article:hover .wp-menu-image, #menu-posts-article.wp-has-current-submenu .wp-menu-image {
            background-position:center bottom !important;
        }
		#menu-posts-image .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/css/img/picture-icon.png) no-repeat center top !important;
        }
		#menu-posts-image:hover .wp-menu-image, #menu-posts-image.wp-has-current-submenu .wp-menu-image {
            background-position:center bottom !important;
        }
		#menu-posts-video .wp-menu-image {
            background: url(<?php bloginfo('template_url') ?>/css/img/video-icon.png) no-repeat center top !important;
        }
		#menu-posts-video:hover .wp-menu-image, #menu-posts-video.wp-has-current-submenu .wp-menu-image {
            background-position:center bottom !important;
        }
		#wp-admin-bar-wp-logo > .ab-item .ab-icon {
			background: transparent url(<?php bloginfo('template_url') ?>/css/img/logo-20px.png) no-repeat center;
		}
		#wpadminbar.nojs #wp-admin-bar-wp-logo:hover > .ab-item .ab-icon, #wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon {
			background: transparent url(<?php bloginfo('template_url') ?>/css/img/logo-20px-dark.png) no-repeat center;
		}
    </style>
<?php }

//be able to find wp post id from url
/* Post URLs to IDs function, supports custom post types - borrowed and modified from url_to_postid() in wp-includes/rewrite.php */
function bwp_url_to_postid ($url) {
    global $wp_rewrite;
 
    $url = apply_filters('url_to_postid', $url);
 
    // First, check to see if there is a 'p=N' or 'page_id=N' to match against
    if ( preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values) )   {
        $id = absint($values[2]);
        if ( $id )
            return $id;
    }
 
    // Check to see if we are using rewrite rules
    $rewrite = $wp_rewrite->wp_rewrite_rules();
 
    // Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
    if ( empty($rewrite) )
        return 0;
 
    // Get rid of the #anchor
    $url_split = explode('#', $url);
    $url = $url_split[0];
 
    // Get rid of URL ?query=string
    $url_split = explode('?', $url);
    $url = $url_split[0];
 
    // Add 'www.' if it is absent and should be there
    if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
        $url = str_replace('://', '://www.', $url);
 
    // Strip 'www.' if it is present and shouldn't be
    if ( false === strpos(home_url(), '://www.') )
        $url = str_replace('://www.', '://', $url);
 
    // Strip 'index.php/' if we're not using path info permalinks
    if ( !$wp_rewrite->using_index_permalinks() )
        $url = str_replace('index.php/', '', $url);
 
    if ( false !== strpos($url, home_url()) ) {
        // Chop off http://domain.com
        $url = str_replace(home_url(), '', $url);
    } else {
        // Chop off /path/to/blog
        $home_path = parse_url(home_url());
        $home_path = isset( $home_path['path'] ) ? $home_path['path'] : '' ;
        $url = str_replace($home_path, '', $url);
    }
 
    // Trim leading and lagging slashes
    $url = trim($url, '/');
 
    $request = $url;
    // Look for matches.
    $request_match = $request;
    foreach ( (array)$rewrite as $match => $query) {
        // If the requesting file is the anchor of the match, prepend it
        // to the path info.
        if ( !empty($url) && ($url != $request) && (strpos($match, $url) === 0) )
            $request_match = $url . '/' . $request;
 
        if ( preg_match("!^$match!", $request_match, $matches) ) {
            // Got a match.
            // Trim the query of everything up to the '?'.
            $query = preg_replace("!^.+\?!", '', $query);
 
            // Substitute the substring matches into the query.
            $query = addslashes(WP_MatchesMapRegex::apply($query, $matches));
 
            // Filter out non-public query vars
            global $wp;
            parse_str($query, $query_vars);
            $query = array();
            foreach ( (array) $query_vars as $key => $value ) {
                if ( in_array($key, $wp->public_query_vars) )
                    $query[$key] = $value;
            }
 
        // Taken from class-wp.php
        foreach ( $GLOBALS['wp_post_types'] as $post_type => $t )
            if ( $t->query_var )
                $post_type_query_vars[$t->query_var] = $post_type;
 
        foreach ( $wp->public_query_vars as $wpvar ) {
            if ( isset( $wp->extra_query_vars[$wpvar] ) )
                $query[$wpvar] = $wp->extra_query_vars[$wpvar];
            elseif ( isset( $_POST[$wpvar] ) )
                $query[$wpvar] = $_POST[$wpvar];
            elseif ( isset( $_GET[$wpvar] ) )
                $query[$wpvar] = $_GET[$wpvar];
            elseif ( isset( $query_vars[$wpvar] ) )
                $query[$wpvar] = $query_vars[$wpvar];
 
            if ( !empty( $query[$wpvar] ) ) {
                if ( ! is_array( $query[$wpvar] ) ) {
                    $query[$wpvar] = (string) $query[$wpvar];
                } else {
                    foreach ( $query[$wpvar] as $vkey => $v ) {
                        if ( !is_object( $v ) ) {
                            $query[$wpvar][$vkey] = (string) $v;
                        }
                    }
                }
 
                if ( isset($post_type_query_vars[$wpvar] ) ) {
                    $query['post_type'] = $post_type_query_vars[$wpvar];
                    $query['name'] = $query[$wpvar];
                }
            }
        }
 
            // Do the query
            $query = new WP_Query($query);
            if ( !empty($query->posts) && $query->is_singular )
                return $query->post->ID;
            else
                return 0;
        }
    }
    return 0;
}

//hook it up baby!
add_action('init', 'create_post_types' );
add_action('admin_menu', 'remove_menu_items');
add_action('save_post', 'save_product_meta', 1, 2); // save the custom fields
add_action('save_post', 'save_video_meta', 1, 2); // save the custom fields
add_action('save_post', 'save_team_meta', 1, 2); // save the custom fields
add_action('save_post', 'save_project_meta', 1, 2); // save the custom fields
add_action('save_post', 'save_home_slide_meta', 1, 2); // save the custom fields
add_action('save_post', 'save_home_square_meta', 1, 2); // save the custom fields
//add_action('admin_head', 'custom_icons' );//add custom icon support