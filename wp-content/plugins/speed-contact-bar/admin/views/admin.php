<?php
/**
 * Options Page
 *
 * @package   Speed_Contact_Bar
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      https://wordpress.org/plugins/speed-contact-bar/
 * @copyright 2014 
 */

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div class="th_wrapper">
		<div id="th_main">
			<div class="th_content">

				<form method="post" action="options.php">
<?php 
settings_fields( $this->settings_fields_slug );
do_settings_sections( $this->main_options_page_slug );
submit_button();
?>
				</form>

			</div><!-- .th_content -->
		</div><!-- #th_main -->
		<div id="th_footer">
			<div class="th_content">
				<h2><?php esc_html_e( 'Credits and informations', 'speed-contact-bar' ); ?></h2>
				<dl>
					<dt><?php esc_html_e( 'Do you like the plugin?', 'speed-contact-bar' ); ?></dt><dd><a href="https://wordpress.org/support/view/plugin-reviews/speed-contact-bar"><?php esc_html_e( 'Rate it at wordpress.org!', 'speed-contact-bar' ); ?></a></dd>
					<dt><?php esc_html_e( 'The plugin is for free. But the plugin author would be delighted to your small contribution.', 'speed-contact-bar' ); ?></dt><dd><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=TPCX6FVZ5NSJ6"><img src="https://www.paypalobjects.com/<?php echo $this->get_paypal_locale(); ?>/i/btn/btn_donateCC_LG.gif" alt="(<?php esc_attr_e( 'Donation Button', 'speed-contact-bar' ); ?>)" id="paypal_button" /><br /><?php esc_html_e( 'Donate with PayPal', 'speed-contact-bar' ); ?></a><img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1" /></dd>
					<dt><?php esc_html_e( 'Do you need support or have an idea for the plugin?', 'speed-contact-bar' ); ?></dt><dd><a href="https://wordpress.org/support/plugin/speed-contact-bar"><?php esc_html_e( 'Post your questions and ideas in the forum at wordpress.org!', 'speed-contact-bar' ); ?></a></dd>
					<dt><?php esc_html_e( 'Plugin development by', 'speed-contact-bar' ); ?></dt><dd><a href="https://stehle-internet.de/?ref=speed-contact-bar"> <?php echo get_avatar( 'm.stehle@gmx.de', 44 ); ?>Stehle Internet</a></dd>
				</dl>
			</div><!-- .th_content -->
		</div><!-- #th_footer -->
	</div><!-- .th_wrapper -->
</div><!-- .wrap -->
