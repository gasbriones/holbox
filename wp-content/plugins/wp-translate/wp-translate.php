<?php
/*
Plugin Name: WP Translate
Plugin URI: http://plugingarden.com/google-translate-wordpress-plugin/
Description: Makes your website available to the world using the powerful Google Translate API to make your content multilingual.
Author: HahnCreativeGroup
Version: 5.0.2
Author URI: http://plugingarden.com
*/

register_activation_hook( __FILE__,  'wpTranslate_install' );

function wpTranslate_install() {
	wpTranslate_options_check();
}

function wpTranslate_options_check() {
	if(!get_option("wpTranslateOptions")) {
	$d = strtotime('+3 Days');
	$wpTranslateOptions = array(
								"default_language" => "auto",
								"tracking_enabled" => false,
								"tracking_id" => "", 
								"auto_display" => true,
								"exclude_mobile" => true,
								"upgrade_notice" => array(
									"count" => 0,
									"date" => date('Y-m-d', $d)
								)
								);
	
	add_option("wpTranslateOptions", $wpTranslateOptions);
	}
	else {
		$wpTranlsateOptions	= get_option("wpTranslateOptions");
		$keys = array_keys($wpTranlsateOptions);		
		
		if (!in_array('default_language', $keys)) {
			$wpTranlsateOptions['default_language'] = "auto";	
		}
		if (!in_array('auto_display', $keys)) {
			$wpTranlsateOptions['auto_display'] = true;	
		}
		if (!in_array('exclude_mobile', $keys)) {
			$wpTranlsateOptions['exclude_mobile'] = true;	
		}
		if (in_array('4-9-5_update_notice_seen', $keys)) {
			unset($wpTranlsateOptions['4-9-5_update_notice_seen']);
		}
		if (in_array('4-9-upgrade_notice', $keys)) {
			unset($wpTranlsateOptions['4-9-upgrade_notice']);	
		}
		if (!in_array('upgrade_notice', $keys)) {
			$d = strtotime('+5 Days');
			$wpTranlsateOptions['upgrade_notice'] = array(
									"count" => 0,
									"date" => date('Y-m-d', $d)
								);	
		}
		
		update_option("wpTranslateOptions", $wpTranlsateOptions);
	}
}
add_action('init', 'wpTranslate_options_check');

add_action( 'wp_ajax_wp_translate_settings', 'wp_translate_settings' );

function wp_translate_settings() {
	check_ajax_referer( 'wp_translate', 'security' );
	
	$wpTranslateOptions['default_language'] = sanitize_text_field($_POST["default_language"]);
	$wpTranslateOptions['exclude_mobile'] = filter_var($_POST["excludeMobile"], FILTER_VALIDATE_BOOLEAN);
	$wpTranslateOptions['auto_display'] = filter_var($_POST["autoDisplay"], FILTER_VALIDATE_BOOLEAN);
	$wpTranslateOptions['tracking_id'] = sanitize_text_field($_POST["trackingId"]);	
	$wpTranslateOptions['tracking_enabled'] = filter_var($_POST["trackingEnabled"], FILTER_VALIDATE_BOOLEAN); 
	
	update_option(WPTRANSLATEOPTIONS, $wpTranslateOptions);
	
	$message = "WP Translate settings have been saved.";
    
	echo $message;

	wp_die(); // this is required to terminate immediately and return a proper response
}
function wp_translate_notice() {
	check_ajax_referer( 'wp_translate', 'security' );
	
	$wpTranlsateOptions	= get_option("wpTranslateOptions");
	
	$upgradeObject = $wpTranlsateOptions['upgrade_notice'];
	$upgradeObject['count']++;
	$upgradeObject['date'] = date('Y-m-d', strtotime('+1 Month'));
	
	$wpTranlsateOptions['upgrade_notice'] = $upgradeObject;	
	
	update_option("wpTranslateOptions", $wpTranlsateOptions);

	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wp_translate_notice', 'wp_translate_notice' ); 

function wp_translate_notice_javascript() { 
	$ajax_nonce = wp_create_nonce( "wp_translate" );
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		jQuery('#wp-translate-notice-btn').on('click', function() {
			var data = {
				'action': 'wp_translate_notice',
				'security': '<?php echo $ajax_nonce; ?>'
			};
			
			jQuery('#wp-translate-notice').hide();
			
			jQuery.post(ajaxurl, data, function(response) {
				
			});
		});
		
	});
	</script> <?php
}
add_action( 'admin_footer', 'wp_translate_notice_javascript' );

function wp_translate_settings_javascript() { 
	$ajax_nonce = wp_create_nonce( "wp_translate" );
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery('#btn-wp-transalate-settings').on('click', function() {
			var default_language = jQuery('#defaultLanguage option:selected').val();
			var trackingId = jQuery('#trackingId').val();
			var tracking_enabled = jQuery('#trackingEnabled').is(':checked');
			var excludeMobile = jQuery('#excludeMobile').is(':checked');
			var autoDisplay = jQuery('#autoDisplay').is(':checked');
			
			var data = {
				'action': 'wp_translate_settings',
				'security': '<?php echo $ajax_nonce; ?>',
				'default_language' : default_language,
				'excludeMobile' : excludeMobile,
				'autoDisplay' : autoDisplay,
				'trackingId': trackingId,
				'trackingEnabled' : tracking_enabled
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#wp-translate-update-status').empty().css('top', '-150px').append(response).show();
				$( "#wp-translate-update-status" ).animate({			
					top: "+=300"
				}, 1000, function() {
					// Animation complete.
					$( "#wp-translate-update-status" ).fadeOut(1500);
				});				
			});
			
			return false;
		});
		
		jQuery('#wp-translate-notice-btn').on('click', function() {
			var data = {
				'action': 'wp_translate_notice',
				'security': '<?php echo $ajax_nonce; ?>'
			};
			
			jQuery('#wp-translate-notice').hide();
			
			jQuery.post(ajaxurl, data, function(response) {
				
			});
		});
		
	});
	</script> <?php
}

//translator
function translate_Init() {
	$wpTranslateOptions = get_option("wpTranslateOptions");
	$doTranslate = true;
	if ($wpTranslateOptions["exclude_mobile"]) {		
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/iPhone|Android|Blackberry|Windows Phone/i', $agent)){
			$doTranslate = false;
		}
	}
	$agent = $_SERVER['HTTP_USER_AGENT'];  
	if($doTranslate){
	?>
	<!-- WP Translate - Google Translate: http://plugingarden.com/google-translate-wordpress-plugin/ -->
    <script type='text/javascript'>
function googleTranslateElementInit2() {
  new google.translate.TranslateElement({
    pageLanguage: '<?php echo esc_js($wpTranslateOptions["default_language"]); ?>',
    <?php if ($wpTranslateOptions["tracking_enabled"]) { ?>
	gaTrack: true,
    gaId: '<?php echo esc_js($wpTranslateOptions["tracking_id"]); ?>',
	<?php } ?>
    floatPosition: google.translate.TranslateElement.FloatPosition.TOP_RIGHT,
	autoDisplay: <?php echo ($wpTranslateOptions["auto_display"]) ? "true" : "false"; ?>
  }<?php if (true) {echo(", 'wp_translate'");} ?>);
}
</script><script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
<style type="text/css">
body {top:0 !important;}
</style>
    <?php
	}	
}
add_action('wp_footer', 'translate_Init');

//admin
function admin_positioning() {
	if (current_user_can('manage_options')) {
		_e('<style>.goog-te-ftab-float {right: 250px !important;}</style>');	
	}
}
add_action('wp_head', 'admin_positioning');

function create_translate_plugin_links($links, $file) {			
	if ( $file == plugin_basename(__FILE__) ) {			
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EJVXJP3V8GE2J">' . __('Donate', 'wp-translate') . '</a>';
	}
	return $links;
}
add_filter('plugin_row_meta', 'create_translate_plugin_links', 10, 2);

function add_wp_translate_menu() {
	add_menu_page(__('WP Translate','wp-translate'), __('WP Translate','wp-translate'), 'manage_options', 'wptranslate-admin', 'show_translate_menu' );
	
	wp_register_style( 'wp_translate_admin_stylesheet', WP_PLUGIN_URL.'/wp-translate/admin/wp-translate-style.css');
	wp_enqueue_style('wp_translate_admin_stylesheet');
}
add_action( 'admin_menu', 'add_wp_translate_menu' );

function show_translate_menu()
{
	include("admin/overview.php");
	
	add_action( 'admin_footer', 'wp_translate_settings_javascript' );
}

function wpt_upgrade_notice() {
	$wpTranlsateOptions	= get_option("wpTranslateOptions");
	$upgradeObject = $wpTranlsateOptions['upgrade_notice'];
	$today = strtotime(date('Y-m-d'));
	$noticeDate = strtotime($upgradeObject['date']);
	$showNotice = false;
	
	if ($today >= $noticeDate) {
		$showNotice = true;
	}
	
	if ($showNotice) {
	?>
	<div id="wp-translate-notice" class="update-nag" style="clear: both;">
		<div class="wp-translate-logo" id="wp-translate-notice-logo"></div>
		<div id="wp-translate-notice-content">
			<h2>Want to give your readers a better translation experience?</h2>
			<h4>Check out <a href="http://plugingarden.com/google-translate-wordpress-plugin/?src=wpt" target="_blank">WP Tranlate Pro</a>.</h4>
			<p>Show country flag icons next to languages and remove Google branding. WP Translate Pro also has shortcode for pages that don't display widgets.</p>
		</div>
		<button id="wp-translate-notice-btn" class="notice-dismiss" style="position: relative; float: right;"></button>
		<div style="clear: both;"></div>
	</div>
	<?php
	}
}
add_action( 'admin_notices', 'wpt_upgrade_notice' );

//widget
class WP_Translate_Widget extends WP_Widget {
	//register widget
	function __construct() {
		parent::__construct(
			'wp_translation_widget',
			__('WP Translate Widget', 'wp-translate'),
			array('description' => __('Creates a simple drop down list of languages to translate content to and hides tool bar', 'wp-translate'), )
		);
	}
	
	//front-end
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
	
		echo '<div id="wp_translate"></div>';		
		
		echo $args['after_widget'];
	}
	
	//back-end
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		else {
			$title = __( 'Translate', 'wp-translate' );
		}
		?>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">		
		<?php
	}
	
	//sanitize form values when updated
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		
		return $instance;
	}
}

function register_wp_translation_widget() {
	register_widget( 'WP_Translate_Widget' );
}
add_action( 'widgets_init', 'register_wp_translation_widget' );





?>