<?php
/**
 * Speed Contact Bar.
 *
 * @package   Speed_Contact_Bar_Admin
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/speed-contact-bar/
 * @copyright 2014 
 */

/**
 * @package Speed_Contact_Bar_Admin
 * @author    Martin Stehle <m.stehle@gmx.de>
 */
class Speed_Contact_Bar_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $plugin_screen_hook_suffix = null;

	/**
	 * version of this plugin.
	 *
	 * @since    1.5
	 *
	 * @var      string
	 */
	private $plugin_version = null;

	/**
	 * Name of this plugin.
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $plugin_name = null;

	/**
	 * Unique identifier for this plugin.
	 *
	 * It is the same as in class Speed_Contact_Bar
	 * Has to be set here to be used in non-object context, e.g. callback functions
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $plugin_slug = null;

	/**
	 * Unique identifier in the WP options table
	 *
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	private $settings_db_slug = null;

	/**
	 * Slug of the menu page on which to display the form sections
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	private $main_options_page_slug = 'scb_options_page';

	/**
	 * Group name of options
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	private $settings_fields_slug = 'scb_options_group';
	
	/**
	 * Structure of the form sections with headline, description and options
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	private $form_structure = null;

	/**
	 * Stored settings in an array
	 *
	 *
	 * @since    1.0
	 *
	 * @var      array
	 */
	private $stored_settings = array();

	/**
	 * Social networks
	 *
	 *
	 * @since    1.5
	 *
	 * @var      array
	 */
	private $social_networks = array();
	
	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0
	 */
	private function __construct() {

		// Call variables from public plugin class.
		$plugin = Speed_Contact_Bar::get_instance();
		$this->plugin_name = $plugin->get_plugin_name();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->settings_db_slug = $plugin->get_settings_db_slug();
		$this->social_networks = $plugin->get_social_networks();
		$this->plugin_version = $plugin->get_plugin_version();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_head',			 array( $this, 'print_admin_css' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'admin_init', array( $this, 'register_options' ) );

		// get current or default settings
		$this->stored_settings = $plugin->get_stored_settings();

	}

	/**
	 * Get PayPal locale code
	 *
	 * @since     4.2.1
	 *
	 * @return    string    Returns xx_XX country code
	 */
	private function get_paypal_locale () {
		// source: https://developer.paypal.com/docs/classic/archive/buttons/
		// source: http://wpcentral.io/internationalization/
		$paypal_locale = get_locale();
		// if locale is not in registered locale code try to find the nearest match
		if ( ! in_array( $paypal_locale, array( 'en_US', 'en_AU', 'es_ES', 'fr_FR', 'de_DE', 'ja_JP', 'it_IT', 'pt_PT', 'pt_BR', 'pl_PL', 'ru_RU', 'sv_SE', 'tr_TR', 'nl_NL', 'zh_CN', 'zh_HK', 'he_IL' ) ) ) {
			if ( 'ja' == $paypal_locale ) { // japanese language
				$paypal_locale = 'ja_JP';
			} else {
				$language_codes = explode( '_', $paypal_locale );
				// test the language
				switch ( $language_codes[ 0 ] ) {
					case 'en':
						$paypal_locale = 'en_US';
						break;
					case 'nl':
						$paypal_locale = 'nl_NL';
						break;
					case 'es':
						$paypal_locale = 'es_ES';
						break;
					case 'de':
						$paypal_locale = 'de_DE';
						break;
					default:
						$paypal_locale = 'en_US';
				} // switch()
			} // if ('ja')
		} // if !in_array()
	
		return $paypal_locale;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array( ), $this->plugin_version );
		}

		/* collect css for the color picker */
		#wp_enqueue_style( 'farbtastic' );
		wp_enqueue_style( 'wp-color-picker' );
 	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		/* collect js for the color picker */
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), $this->plugin_version );
		}
		#wp_enqueue_script( 'farbtastic' );
		wp_enqueue_script( 'wp-color-picker' );
	}

	/**
	 * Print dynamic CSS in the HTML Head section
	 *
	 * @since     1.4
	 *
	 */
	public function print_admin_css() {
	
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		
		// print CSS only on this plugin's page
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			$root_url = plugin_dir_url( dirname( __FILE__ ) );
			$pngs = array( 'imdb', 'yelp', 'soundcloud', 'snap' ); // PNG image file namens
			echo '<style type="text/css">';
			print "\n";
			$background_size = '40px 40px';
			$background_position = '2.77em';
			foreach ( array( 'address', 'phone', 'fax', 'cellphone', 'email' ) as $name ) {
				printf(
					".form-table th label[for='%s'] { display: block; height: 85px; background: url('%spublic/assets/images/%s_dark.svg') no-repeat scroll 0 %s transparent; background-size: %s; }",
					$name,
					$root_url,
					$name,
					$background_position,
					$background_size
				);
				print "\n";
			}
			foreach ( $this->social_networks as $name ) {
				if ( in_array( $name, $pngs ) ) {
					switch ( $name ) {
						case 'imdb':
							$logo_size = '85px 40px';
							break;
						case 'yelp':
							$logo_size = '76px 40px';
							break;
						default:
							$logo_size = $background_size;
					}
					printf(
						".form-table th label[for='%s'] { display: block; height: 85px; background: url('%spublic/assets/images/%s.png') no-repeat scroll 0 %s transparent; background-size: %s; }",
						$name,
						$root_url,
						$name,
						$background_position,
						$logo_size
					);
				} else {
					printf(
						".form-table th label[for='%s'] { display: block; height: 85px; background: url('%spublic/assets/images/%s.svg') no-repeat scroll 0 %s transparent; background-size: %s; }",
						$name,
						$root_url,
						$name,
						$background_position,
						$background_size
					);
				}
				print "\n";
			}
			echo '</style>';
			print "\n";
		}
	}
	
	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0
	 */
	public function add_plugin_admin_menu() {

		$text = 'Settings';
		// Add a settings page for this plugin to the Settings menu.
		$this->plugin_screen_hook_suffix = add_options_page(
			sprintf( '%s %s', $this->plugin_name, __( $text ) ),
			$this->plugin_name,
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0
	 */
	public function add_action_links( $links ) {

		$text = 'Settings';
		return array_merge(
			$links,
			array(
				'settings' => '<a href="' . esc_url( admin_url( 'options-general.php?page=' . $this->plugin_slug ) ) . '">' . esc_html__( $text ) . '</a>'
			)
		);

	}

	/**
	* Define and register the options
	* Run on admin_init()
	*
	* @since   1.0
	*/
	public function register_options () {

		$title = null;
		$html = null;
		
		$font_sizes = array();
		foreach( range( 4, 48 ) as $value ) {
			$font_sizes[ $value ] = sprintf( '%dpx', $value );
		}
		
		$icon_sizes = array();
		foreach( range( 10, 48, 2 ) as $value ) {
			$icon_sizes[ $value ] = sprintf( '%dpx', $value );
		}
		
		$readjustments = array();
		foreach( range( 0, 75 ) as $value ) {
			$readjustments[ $value ] = sprintf( '%dpx', $value );
		}
		
		$padding_sizes = array();
		foreach( range( 0, 32 ) as $value ) {
			$padding_sizes[ $value ] = sprintf( '%dpx', $value );
		}
		

		// translate recurring strings once
		$label_example	= esc_html__( 'Example', 'speed-contact-bar' );
		$label_enter	= esc_html__( 'Enter a valid URL. If the URL is invalid it will not be used.', 'speed-contact-bar' );
		$label_url		= esc_html__( 'Your URL on %s', 'speed-contact-bar' );
		// define the form sections, order by appereance, with headlines, and options
		$this->form_structure = array(
			'1st_section' => array(
				'headline' => esc_html__( 'Your Contact Data', 'speed-contact-bar' ),
				'description' => esc_html__( 'Set the contact informations. To supress displaying a field leave it empty.', 'speed-contact-bar' ),
				'options' => array(
					'headline' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Headline', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a short headline for the contact bar', 'speed-contact-bar' ),
					),
					'headline_url' => array(
						'type'    => 'url',
						'title'   => esc_html__( 'URL of the headline', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a web address and the headline becomes a link. The address must start with http:// or https://', 'speed-contact-bar' ) ,
					),
					'address' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Postal address', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a short postal address for the contact bar', 'speed-contact-bar' ),
					),
					'address_url' => array(
						'type'    => 'url',
						'title'   => esc_html__( 'URL of the postal address', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a web address and the postal address becomes a link. The web address must start with http:// or https://', 'speed-contact-bar' ) ,
					),
					'email' => array(
						'type'    => 'email',
						'title'   => esc_html__( 'E-Mail Address', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a valid email address. If the email address is invalid it will not be used.', 'speed-contact-bar' ),
					),
					'email_text' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'E-Mail Link Text', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a text for the email link, otherwise the email address will be displayed.', 'speed-contact-bar' ),
					),
					'phone' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Phone Number', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter your official contact phone number. Since web pages can be accessed worldwide the phone number should include the international dialing prefix of your country like +49 for Germany, making the number useable from any location.', 'speed-contact-bar' ),
					),
					'phone_text' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Phone Text', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a text for the phone link, otherwise the phone number will be displayed.', 'speed-contact-bar' ),
					),
					'fax' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Fax Number', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter your official contact fax number, including the international dialing prefix of your country.', 'speed-contact-bar' ),
					),
					'fax_text' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Fax Text', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a text for the fax link, otherwise the fax number will be displayed.', 'speed-contact-bar' ),
					),
					'cellphone' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Cell Phone Number', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter your official contact cell phone number, including the international dialing prefix of your country.', 'speed-contact-bar' ),
					),
					'cellphone_text' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Cell Phone Text', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a text for the cell phone link, otherwise the cell phone number will be displayed.', 'speed-contact-bar' ),
					),
					'facebook' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Facebook' ),
						'desc'    => $label_example . ': http://www.facebook.com/name<br />'. $label_enter,
					),
					'flickr' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Flickr' ),
						'desc'    => $label_example . ': https://www.flickr.com/people/user-id/<br />'. $label_enter,
					),
					'googleplus' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Google Plus' ),
						'desc'    => $label_example . ': https://plus.google.com/name<br />'. $label_enter,
					),
					'imdb' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'IMDb' ),
						'desc'    => $label_example . ': http://www.imdb.com/user/name<br />'. $label_enter,
					),
					'instagram' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Instagram' ),
						'desc'    => $label_example . ': http://instagram.com/name<br />'. $label_enter,
					),
					'linkedin' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'LinkedIn' ),
						'desc'    => $label_example . ': http://www.linkedin.com/in/username<br />'. $label_enter,
					),
					'pinterest' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Pinterest' ),
						'desc'    => $label_example . ': http://www.pinterest.com/username<br />'. $label_enter,
					),
					'skype' => array(
						'type'    => 'textfield',
						'title'   => esc_html__( 'Your Skype name', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter your Skype username, not your email address or phone number.', 'speed-contact-bar' ),
					),
					'slideshare' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'SlideShare' ),
						'desc'    => $label_example . ': http://www.slideshare.net/channelname<br />'. $label_enter,
					),
					'snap' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Snapchat' ),
						'desc'    => $label_example . ': https://www.snapchat.com/add/profilname<br />'. $label_enter,
					),
					'soundcloud' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'SoundCloud' ),
						'desc'    => $label_example . ': http://www.soundcloud.com/name<br />'. $label_enter,
					),
					'tumblr' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'tumblr' ),
						'desc'    => $label_example . ': http://blogname.tumblr.com/<br />'. $label_enter,
					),
					'twitter' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Twitter' ),
						'desc'    => $label_example . ': http://www.twitter.com/username<br />'. $label_enter,
					),
					'vimeo' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Vimeo' ),
						'desc'    => $label_example . ': http://vimeo.com/name<br />'. $label_enter,
					),
					'yelp' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Yelp' ),
						'desc'    => $label_example . ': http://www.yelp.com/biz/name<br />'. $label_enter,
					),
					'youtube' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'YouTube' ),
						'desc'    => $label_example . ': http://www.youtube.com/username<br />'. $label_enter,
					),
					'xing' => array(
						'type'    => 'url',
						'title'   => sprintf( $label_url, 'Xing' ),
						'desc'    => $label_example . ': http://www.xing.com/profile/username<br />'. $label_enter,
					),
				),
			),
			'2nd_section' => array(
				'headline' => esc_html__( 'Appeareance of the contact bar', 'speed-contact-bar' ),
				'description' => esc_html__( 'Set the graphical properties of the contact bar.', 'speed-contact-bar' ),
				'options' => array(
					'max_viewport_width' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Maximal viewport width to hide the bar', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the maximal viewport width for hiding the bar. 480px and below = hide in smartphones; 1024px and below = probably tablets.', 'speed-contact-bar' ),
						'values'  => array( 'never' => esc_html__( 'never hide', 'speed-contact-bar' ), '320px' => esc_html__( '320px', 'speed-contact-bar' ), '480px' => esc_html__( '480px', 'speed-contact-bar' ), '640px' => esc_html__( '640px', 'speed-contact-bar' ), '768px' => esc_html__( '768px', 'speed-contact-bar' ), '1024px' => esc_html__( '1024px', 'speed-contact-bar' ) ),
						'default' => 'top',
					),
					'position' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Position of the bar', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the position of the bar on every page', 'speed-contact-bar' ),
						'values'  => array( 'top' => esc_html__( 'at the top', 'speed-contact-bar' ), 'bottom' => esc_html__( 'at the bottom', 'speed-contact-bar' ) ),
						'default' => 'top',
					),
					'fixed' => array(
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Enable fixed position', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Display bar always visible (not available in mobile design)', 'speed-contact-bar' ),
					),
					'readjustment' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Height readjustment for fixed position', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Readjust the space between the bar and the page content (not in mobile design)', 'speed-contact-bar' ),
						'values'  => $readjustments,
						'default' => 30,
					),
					'vertical_padding' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Vertical Padding', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the space between content and upper and lower border of the bar', 'speed-contact-bar' ),
						'values'  => $padding_sizes,
						'default' => 15,
					),
					'horizontal_padding' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Horizontal Padding', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the space between content and left and right border of the bar', 'speed-contact-bar' ),
						'values'  => $padding_sizes,
						'default' => 15,
					),
					'bg_transparent' => array(
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Transparent background', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Activate to ignore the background color and to show a transparent bar', 'speed-contact-bar' ),
					),
					'bg_color' => array(
						'type'    => 'colorpicker',
						'title'   => esc_html__( 'Background Color', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the background color', 'speed-contact-bar' ),
					),
					'bg_color_opacity' => array(
						'type'    => 'zero2one',
						'title'   => esc_html__( 'Background Color Opacity', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Enter a number between 0.0 and 1.0 for 0.0 as no opacity (i.e. highest transparency) and 1.0 for highest opacity (i.e. no transparency).', 'speed-contact-bar' ),
					),
					'show_shadow' => array(
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Show shadow', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Activate to show a slight shadow under or above the bar depending on the position of the bar', 'speed-contact-bar' ),
					),
				),
			),
			'3rd_section' => array(
				'headline' => esc_html__( 'Appeareance of the headline', 'speed-contact-bar' ),
				'description' => esc_html__( 'Set the graphical properties of the headline.', 'speed-contact-bar' ),
				'options' => array(
					'show_headline' => array(
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Show headline', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Activate to show the headline', 'speed-contact-bar' ),
					),
					'keep_headline' => array(
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Keep headline in mobile devices', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Activate to keep displaying the headline in tablets and smartphones, else it will be hidden', 'speed-contact-bar' ),
					),
					'headline_tag' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Headline HTML Tag', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the HTML element for the headline without changing the headline style', 'speed-contact-bar' ),
						'values'  => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'div' => 'DIV', 'p' => 'P' ),
						'default' => 'h2',
					),
				),
			),
			'4th_section' => array(
				'headline' => esc_html__( 'Appeareance of texts and links', 'speed-contact-bar' ),
				'description' => esc_html__( 'Set the graphical properties of the texts and links in the contact bar.', 'speed-contact-bar' ),
				'options' => array(
					'show_texts' => array(
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Show texts on small displays', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Activate to keep the postal address, phone numbers and mail address displayed in small displays', 'speed-contact-bar' ),
					),
					'content_alignment' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Text Alignment', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the alignment of the content within the bar', 'speed-contact-bar' ),
						'values'  => array( 'left' => esc_html__( 'left-aligned', 'speed-contact-bar' ), 'center' => esc_html__( 'centered', 'speed-contact-bar' ), 'right' => esc_html__( 'right-aligned', 'speed-contact-bar' ) ),
						'default' => 'center',
					),
					'font_size' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Font Size', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the font size of the texts and links', 'speed-contact-bar' ),
						'values'  => $font_sizes,
						'default' => 15,
					),
					'text_color' => array(
						'type'    => 'colorpicker',
						'title'   => esc_html__( 'Text Color', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the text color', 'speed-contact-bar' ),
					),
					'link_color' => array(
						'type'    => 'colorpicker',
						'title'   => esc_html__( 'Link Color', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the link color', 'speed-contact-bar' ),
					),
					'open_new_window' => array(
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Open links in new windows', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Activate to let the links load the target site in new windows or tabs', 'speed-contact-bar' ),
					),
					'open_new_window_social_only' => array(
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Do not open the links of the postal address and the headline in new windows', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Activate to exclude the links of the postal address and the headline from the previous option to open them in the same window', 'speed-contact-bar' ),
					),
				),
			),
			'5th_section' => array(
				'headline' => esc_html__( 'Appeareance of the icons', 'speed-contact-bar' ),
				'description' => esc_html__( 'Set the graphical properties of the icons in the contact bar.', 'speed-contact-bar' ),
				'options' => array(
					'icon_size' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Icon Size', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the size of the icons', 'speed-contact-bar' ),
						'values'  => $icon_sizes,
						'default' => 30,
					),
					'icon_type' => array(
						'type'    => 'selection',
						'title'   => esc_html__( 'Icon Brightness', 'speed-contact-bar' ),
						'desc'    => esc_html__( 'Select the brightness of the icons', 'speed-contact-bar' ),
						'values'  => array( 'bright' => esc_html__( 'bright', 'speed-contact-bar' ), 'dark' => esc_html__( 'dark', 'speed-contact-bar' ) ),
						'default' => 'dark',
					),
				),
			),
		);
		// build form with sections and options
		foreach ( $this->form_structure as $section_key => $section_values ) {
		
			// assign callback functions to form sections (options groups)
			add_settings_section(
				// 'id' attribute of tags
				$section_key, 
				// title of the section.
				$this->form_structure[ $section_key ][ 'headline' ],
				// callback function that fills the section with the desired content
				array( $this, 'print_section_' . $section_key ),
				// menu page on which to display this section
				$this->main_options_page_slug
			); // end add_settings_section()
			
			// set labels and callback function names per option name
			foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
				// set default description
				$desc = '';
				if ( isset( $option_values[ 'desc' ] ) and '' != $option_values[ 'desc' ] ) {
					if ( 'checkbox' == $option_values[ 'type' ] ) {
						$desc =  $option_values[ 'desc' ];
					} else {
						$desc =  sprintf( '<p class="description">%s</p>', $option_values[ 'desc' ] );
					}
				}
				// build the form elements values
				switch ( $option_values[ 'type' ] ) {
					case 'radiobuttons':
						$title = $option_values[ 'title' ];
						$stored_value = isset( $this->stored_settings[ $option_name ] ) ? esc_attr( $this->stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<fieldset><legend class="screen-reader-text"><span>%s</span></legend>', $title );
						foreach ( $option_values[ 'values' ] as $value => $label ) {
							$checked = $stored_value ? checked( $stored_value, $value, false ) : '';
							$html .= sprintf( '<label><input type="radio" name="%s[%s]" value="%s"%s /> <span>%s</span></label><br />', $this->settings_db_slug, $option_name, $value, $checked, $label );
						}
						$html .= '</fieldset>';
						$html .= $desc;
						break;
					case 'checkboxes':
						$title = $option_values[ 'title' ];
						$html = sprintf( '<fieldset><legend class="screen-reader-text"><span>%s</span></legend>', $title );
						foreach ( $option_values[ 'values' ] as $value => $label ) {
							$stored_value = isset( $this->stored_settings[ $value ] ) ? esc_attr( $this->stored_settings[ $value ] ) : '0';
							$checked = $stored_value ? checked( '1', $stored_value, false ) : '0';
							$html .= sprintf( '<label for="%s"><input name="%s[%s]" type="checkbox" id="%s" value="1"%s /> %s</label><br />' , $value, $this->settings_db_slug, $value, $value, $checked, $label );
						}
						$html .= '</fieldset>';
						$html .= $desc;
						break;
					case 'selection':
						$title = $option_values[ 'title' ];
						$stored_value = isset( $this->stored_settings[ $option_name ] ) ? esc_attr( $this->stored_settings[ $option_name ] ) : $option_values[ 'default' ];
						$html = sprintf( '<select id="%s" name="%s[%s]">', $option_name, $this->settings_db_slug, $option_name );
						foreach ( $option_values[ 'values' ] as $value => $label ) {
							$selected = $stored_value ? selected( $stored_value, $value, false ) : '';
							$html .= sprintf( '<option value="%s"%s>%s</option>', $value, $selected, $label );
						}
						$html .= '</select>';
						$html .= $desc;
						break;
					case 'checkbox':
						$title = $option_values[ 'title' ];
						$value = isset( $this->stored_settings[ $option_name ] ) ? esc_attr( $this->stored_settings[ $option_name ] ) : '0';
						$checked = $value ? checked( '1', $value, false ) : '';
						$html = sprintf( '<label for="%s"><input name="%s[%s]" type="checkbox" id="%s" value="1"%s /> %s</label>' , $option_name, $this->settings_db_slug, $option_name, $option_name, $checked, $desc );
						break;
					case 'url':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( $this->stored_settings[ $option_name ] ) ? esc_url( $this->stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<input type="text" id="%s" name="%s[%s]" value="%s">', $option_name, $this->settings_db_slug, $option_name, $value );
						$html .= $desc;
						break;
					case 'textarea':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( $this->stored_settings[ $option_name ] ) ? esc_textarea( $this->stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<textarea id="%s" name="%s[%s]" cols="30" rows="5">%s</textarea>', $option_name, $this->settings_db_slug, $option_name, $value );
						$html .= $desc;
						break;
					case 'farbtastic':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( $this->stored_settings[ $option_name ] ) ? esc_attr( $this->stored_settings[ $option_name ] ) : '#cccccc';
						$html = '<div class="farbtastic-container" style="position: relative;">';
						$html .= sprintf( '<input type="text" id="%s" name="%s[%s]" value="%s">', $option_name, $this->settings_db_slug, $option_name, $value );
						$html .= sprintf( '<div id="farbtastic-%s"></div></div>', $option_name );
						$html .= $desc;
						break;
					case 'colorpicker':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( $this->stored_settings[ $option_name ] ) ? esc_attr( $this->stored_settings[ $option_name ] ) : '#cccccc';
						$html = sprintf( '<input type="text" id="%s" class="wp-color-picker" name="%s[%s]" value="%s">', $option_name, $this->settings_db_slug, $option_name, $value );
						$html .= $desc;
						break;
					case 'zero2one':
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( $this->stored_settings[ $option_name ] ) ? esc_attr( $this->stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<input type="text" id="%s" name="%s[%s]" value="%s" class="zero2one">', $option_name, $this->settings_db_slug, $option_name, $value );
						$html .= $desc;
						break;
					// else text field
					default:
						$title = sprintf( '<label for="%s">%s</label>', $option_name, $option_values[ 'title' ] );
						$value = isset( $this->stored_settings[ $option_name ] ) ? esc_attr( $this->stored_settings[ $option_name ] ) : '';
						$html = sprintf( '<input type="text" id="%s" name="%s[%s]" value="%s">', $option_name, $this->settings_db_slug, $option_name, $value );
						$html .= $desc;
				} // end switch()

				// register the option
				add_settings_field(
					// form field name for use in the 'id' attribute of tags
					$option_name,
					// title of the form field
					$title,
					// callback function to print the form field
					array( $this, 'print_option' ),
					// menu page on which to display this field for do_settings_section()
					$this->main_options_page_slug,
					// section where the form field appears
					$section_key,
					// arguments passed to the callback function 
					array(
						'html' => $html,
					)
				); // end add_settings_field()

			} // end foreach( section_values )

		} // end foreach( section )

		// finally register all options. They will be stored in the database in the wp_options table under the options name $this->settings_db_slug.
		register_setting( 
			// group name in settings_fields()
			$this->settings_fields_slug,
			// name of the option to sanitize and save in the db
			$this->settings_db_slug,
			// callback function that sanitizes the option's value.
			array( $this, 'sanitize_options' )
		); // end register_setting()
		
	} // end register_options()

	/**
	* Check and return correct values for the settings
	*
	* @since   1.0
	*
	* @param   array    $input    Options and their values after submitting the form
	* 
	* @return  array              Options and their sanatized values
	*/
	public function sanitize_options ( $input ) {
		foreach ( $this->form_structure as $section_name => $section_values ) {
			foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
				switch ( $option_values[ 'type' ] ) {
					// if checkbox is set assign '1', else '0'
					case 'checkbox':
						$input[ $option_name ] = isset( $input[ $option_name ] ) ? 1 : 0 ;
						break;
					// clean email value
					case 'email':
						$email = sanitize_email( $input[ $option_name ] );
						$input[ $option_name ] = is_email( $email ) ? $email : '';
						break;
					// clean url values
					case 'url':
						$input[ $option_name ] = esc_url_raw( $input[ $option_name ] );
						break;
					// clean float values between 0 and 1
					case 'zero2one':
						// consider possible local orthography: change comma to point
						$input[ $option_name ] = str_replace( ',', '.', $input[ $option_name ] );
						// cast string to float number in the range from 0 to 1
						if ( $input[ $option_name ] < 0 ) {
							$input[ $option_name ] = 0.0;
						} elseif ( 1 < $input[ $option_name ] ) {
							$input[ $option_name ] = 1.0;
						} else {
							// note: strings are converted to 0.0
							$input[ $option_name ] = floatval( $input[ $option_name ] );
						}
						break;
					// clean all other form elements values
					default:
						$input[ $option_name ] = sanitize_text_field( $input[ $option_name ] );
				} // end switch()
			} // foreach( options )
		} // foreach( sections )
		return $input;
	} // end sanitize_options()

	/**
	* Print the option
	*
	* @since   1.0
	*
	*/
	public function print_option ( $args ) {
		echo $args[ 'html' ];
	}

	/**
	* Print the explanation for section 1
	*
	* @since   1.0
	*/
	public function print_section_1st_section () {
		printf( "<p>%s</p>\n", $this->form_structure[ '1st_section' ][ 'description' ] );
	}

	/**
	* Print the explanation for section 2
	*
	* @since   1.0
	*/
	public function print_section_2nd_section () {
		printf( "<p>%s</p>\n", $this->form_structure[ '2nd_section' ][ 'description' ] );
	}

	/**
	* Print the explanation for section 3
	*
	* @since   3.0
	*/
	public function print_section_3rd_section () {
		printf( "<p>%s</p>\n", $this->form_structure[ '3rd_section' ][ 'description' ] );
	}
	
	/**
	* Print the explanation for section 4
	*
	* @since   3.0
	*/
	public function print_section_4th_section () {
		printf( "<p>%s</p>\n", $this->form_structure[ '4th_section' ][ 'description' ] );
	}
	
	/**
	* Print the explanation for section 5
	*
	* @since   3.0
	*/
	public function print_section_5th_section () {
		printf( "<p>%s</p>\n", $this->form_structure[ '5th_section' ][ 'description' ] );
	}
	
}
