<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

	if (isset($_POST["editOptions"])) {
		if(check_admin_referer('wp_translate','wp_translate')) {
			$wpTranslateOptions['default_language'] = sanitize_text_field($_POST["defaultLanguage"]);
			if (isset($_POST["trackingEnabled"]))
				$wpTranslateOptions['tracking_enabled'] = true;
			else
				$wpTranslateOptions['tracking_enabled'] = false;
			
			$wpTranslateOptions['tracking_id'] = sanitize_text_field($_POST["trackingId"]);
			
			if (isset($_POST["autoDisplay"]))
				$wpTranslateOptions['auto_display'] = true;
			else
				$wpTranslateOptions['auto_display'] = false;
				
			if (isset($_POST["excludeMobile"]))
				$wpTranslateOptions['exclude_mobile'] = true;
			else
				$wpTranslateOptions['exclude_mobile'] = false;
				
			update_option("wpTranslateOptions", $wpTranslateOptions);
			?>  
			<div class="updated"><p><strong><?php _e('WP Translate settings have been saved.', 'wp-translate' ); ?></strong></p></div>  
			<?php
		}
	}
	$wpTranslateOptions = get_option("wpTranslateOptions");
?>
<div id='wrap'>
	<div class="wpt-divider first">
		<h1 class="wp-heading-inline"><?php _e('WP Translate', 'wptranslate'); ?></h1>
	</div>
	<div id="wpt-left-column">	
	<div class="wp-translate-settings-wrap">	
	<p>You can also use the <a href="<?php echo get_site_url().'/wp-admin/widgets.php'; ?>">WP Translate Widget</a> to insert the drop down list of languages globally instead of the default tool bar.</p>
	<p><strong><a href="http://plugingarden.com/google-translate-wordpress-plugin/" target="_blank"><?php _e('Upgrade to WP Translate Pro', 'wp-translate'); ?></a></strong><br /><em><?php _e('Pro features include: Improved look and feel, enhanced widget positioning, and more', 'wp-translate'); ?>...</em></p>	
	</div>
	<div class="wp-translate-settings-wrap">
	<h2><?php _e('Settings', 'wptranslate-pro'); ?></h2>	
    <form name="wp_translate_settings_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="editOptions" vale="true" />
	<?php wp_nonce_field('wp_translate','wp_translate'); ?>
    <div class="wpt-setting-label-wrap">
		<?php _e('Default Language', 'wptranslate-pro'); ?>
	</div>
	<div class="wpt-setting-value-wrap">
		<select id="defaultLanguage" name="defaultLanguage">
			<option value="auto" <?php if($wpTranslateOptions['default_language'] == 'auto') echo esc_attr('selected'); ?>>Detect language</option>
			<option value="af" <?php if($wpTranslateOptions['default_language'] == 'af') echo esc_attr('selected'); ?>>Afrikaans</option>
			<option value="sq" <?php if($wpTranslateOptions['default_language'] == 'sq') echo esc_attr('selected'); ?>>Albanian</option>
			<option value="ar" <?php if($wpTranslateOptions['default_language'] == 'ar') echo esc_attr('selected'); ?>>Arabic</option>
			<option value="hy" <?php if($wpTranslateOptions['default_language'] == 'hy') echo esc_attr('selected'); ?>>Armenian</option>
			<option value="az" <?php if($wpTranslateOptions['default_language'] == 'az') echo esc_attr('selected'); ?>>Azerbaijani</option>
			<option value="eu" <?php if($wpTranslateOptions['default_language'] == 'eu') echo esc_attr('selected'); ?>>Basque</option>
			<option value="be" <?php if($wpTranslateOptions['default_language'] == 'be') echo esc_attr('selected'); ?>>Belarusian</option>
			<option value="bn" <?php if($wpTranslateOptions['default_language'] == 'bn') echo esc_attr('selected'); ?>>Bengali</option>
			<option value="bg" <?php if($wpTranslateOptions['default_language'] == 'bg') echo esc_attr('selected'); ?>>Bulgarian</option>
			<option value="ca" <?php if($wpTranslateOptions['default_language'] == 'ca') echo esc_attr('selected'); ?>>Catalan</option>
			<option value="ceb" <?php if($wpTranslateOptions['default_language'] == 'ceb') echo esc_attr('selected'); ?>>Cebuano</option>
			<option value="ny" <?php if($wpTranslateOptions['default_language'] == 'ny') echo esc_attr('selected'); ?>>Chichewa</option>
			<option value="zh-CN" <?php if($wpTranslateOptions['default_language'] == 'zh-CN') echo esc_attr('selected'); ?>>Chinese (Simplified)</option>
			<option value="zh-TW" <?php if($wpTranslateOptions['default_language'] == 'zh-TW') echo esc_attr('selected'); ?>>Chinese (Traditional)</option>
			<option value="co" <?php if($wpTranslateOptions['default_language'] == 'co') echo esc_attr('selected'); ?>>Corsican</option>
			<option value="hr" <?php if($wpTranslateOptions['default_language'] == 'hr') echo esc_attr('selected'); ?>>Croatian</option>
			<option value="cs" <?php if($wpTranslateOptions['default_language'] == 'cs') echo esc_attr('selected'); ?>>Czech</option>
			<option value="da" <?php if($wpTranslateOptions['default_language'] == 'da') echo esc_attr('selected'); ?>>Danish</option>
			<option value="nl" <?php if($wpTranslateOptions['default_language'] == 'nl') echo esc_attr('selected'); ?>>Dutch</option>
			<option value="en" <?php if($wpTranslateOptions['default_language'] == 'en') echo esc_attr('selected'); ?>>English</option>
			<option value="eo" <?php if($wpTranslateOptions['default_language'] == 'eo') echo esc_attr('selected'); ?>>Esperanto</option>
			<option value="et" <?php if($wpTranslateOptions['default_language'] == 'et') echo esc_attr('selected'); ?>>Estonian</option>
			<option value="tl" <?php if($wpTranslateOptions['default_language'] == 'tl') echo esc_attr('selected'); ?>>Filipino</option>
			<option value="fi" <?php if($wpTranslateOptions['default_language'] == 'fi') echo esc_attr('selected'); ?>>Finnish</option>
			<option value="fr" <?php if($wpTranslateOptions['default_language'] == 'fr') echo esc_attr('selected'); ?>>French</option>
			<option value="gl" <?php if($wpTranslateOptions['default_language'] == 'gl') echo esc_attr('selected'); ?>>Galician</option>
			<option value="ka" <?php if($wpTranslateOptions['default_language'] == 'ka') echo esc_attr('selected'); ?>>Georgian</option>
			<option value="de" <?php if($wpTranslateOptions['default_language'] == 'de') echo esc_attr('selected'); ?>>German</option>
			<option value="el" <?php if($wpTranslateOptions['default_language'] == 'el') echo esc_attr('selected'); ?>>Greek</option>
			<option value="gu" <?php if($wpTranslateOptions['default_language'] == 'gu') echo esc_attr('selected'); ?>>Gujarati</option>
			<option value="ht" <?php if($wpTranslateOptions['default_language'] == 'ht') echo esc_attr('selected'); ?>>Haitian Creole</option>
			<option value="ha" <?php if($wpTranslateOptions['default_language'] == 'ha') echo esc_attr('selected'); ?>>Hausa</option>
			<option value="haw" <?php if($wpTranslateOptions['default_language'] == 'haw') echo esc_attr('selected'); ?>>Hawaii</option>
			<option value="iw" <?php if($wpTranslateOptions['default_language'] == 'iw') echo esc_attr('selected'); ?>>Hebrew</option>
			<option value="hi" <?php if($wpTranslateOptions['default_language'] == 'hi') echo esc_attr('selected'); ?>>Hindi</option>
			<option value="hmn" <?php if($wpTranslateOptions['default_language'] == 'hmn') echo esc_attr('selected'); ?>>Hmong</option>
			<option value="hu" <?php if($wpTranslateOptions['default_language'] == 'hu') echo esc_attr('selected'); ?>>Hungarian</option>
			<option value="is" <?php if($wpTranslateOptions['default_language'] == 'is') echo esc_attr('selected'); ?>>Icelandic</option>
			<option value="id" <?php if($wpTranslateOptions['default_language'] == 'id') echo esc_attr('selected'); ?>>Indonesian</option>
			<option value="ga" <?php if($wpTranslateOptions['default_language'] == 'ga') echo esc_attr('selected'); ?>>Irish</option>
			<option value="it" <?php if($wpTranslateOptions['default_language'] == 'it') echo esc_attr('selected'); ?>>Italian</option>
			<option value="ja" <?php if($wpTranslateOptions['default_language'] == 'ja') echo esc_attr('selected'); ?>>Japanese</option>
			<option value="jw" <?php if($wpTranslateOptions['default_language'] == 'jw') echo esc_attr('selected'); ?>>Javanese</option>
			<option value="kn" <?php if($wpTranslateOptions['default_language'] == 'kn') echo esc_attr('selected'); ?>>Kannada</option>
			<option value="kk" <?php if($wpTranslateOptions['default_language'] == 'kk') echo esc_attr('selected'); ?>>Kazakh</option>
			<option value="km" <?php if($wpTranslateOptions['default_language'] == 'km') echo esc_attr('selected'); ?>>Khmer</option>
			<option value="ko" <?php if($wpTranslateOptions['default_language'] == 'ko') echo esc_attr('selected'); ?>>Korean</option>
			<option value="ku" <?php if($wpTranslateOptions['default_language'] == 'ku') echo esc_attr('selected'); ?>>Kurdish (Kurmanji)</option>
			<option value="ky" <?php if($wpTranslateOptions['default_language'] == 'ky') echo esc_attr('selected'); ?>>Kyrgyz</option>
			<option value="lo" <?php if($wpTranslateOptions['default_language'] == 'lo') echo esc_attr('selected'); ?>>Lao</option>
			<option value="la" <?php if($wpTranslateOptions['default_language'] == 'la') echo esc_attr('selected'); ?>>Latin
			<option value="lv" <?php if($wpTranslateOptions['default_language'] == 'lv') echo esc_attr('selected'); ?>>Latvian</option>
			<option value="lt" <?php if($wpTranslateOptions['default_language'] == 'lt') echo esc_attr('selected'); ?>>Lithuanian</option>
			<option value="lb" <?php if($wpTranslateOptions['default_language'] == 'lb') echo esc_attr('selected'); ?>>Luxembourgish</option>
			<option value="mk" <?php if($wpTranslateOptions['default_language'] == 'mk') echo esc_attr('selected'); ?>>Macedonian</option>
			<option value="mg" <?php if($wpTranslateOptions['default_language'] == 'mg') echo esc_attr('selected'); ?>>Malagasy</option>
			<option value="ms" <?php if($wpTranslateOptions['default_language'] == 'ms') echo esc_attr('selected'); ?>>Malay</option>
			<option value="ml" <?php if($wpTranslateOptions['default_language'] == 'ml') echo esc_attr('selected'); ?>>Malayalam</option>
			<option value="mt" <?php if($wpTranslateOptions['default_language'] == 'mt') echo esc_attr('selected'); ?>>Maltese</option>
			<option value="mi" <?php if($wpTranslateOptions['default_language'] == 'mi') echo esc_attr('selected'); ?>>Maori</option>
			<option value="mr" <?php if($wpTranslateOptions['default_language'] == 'mr') echo esc_attr('selected'); ?>>Marathi</option>
			<option value="mn" <?php if($wpTranslateOptions['default_language'] == 'mn') echo esc_attr('selected'); ?>>Mongolian</option>
			<option value="my" <?php if($wpTranslateOptions['default_language'] == 'my') echo esc_attr('selected'); ?>>Myanmar (Burmese)</option>
			<option value="ne" <?php if($wpTranslateOptions['default_language'] == 'ne') echo esc_attr('selected'); ?>>Nepali</option>
			<option value="no" <?php if($wpTranslateOptions['default_language'] == 'no') echo esc_attr('selected'); ?>>Norwegian</option>
			<option value="ps" <?php if($wpTranslateOptions['default_language'] == 'ps') echo esc_attr('selected'); ?>>Pashto</option>
			<option value="fa" <?php if($wpTranslateOptions['default_language'] == 'fa') echo esc_attr('selected'); ?>>Persian</option>
			<option value="pl" <?php if($wpTranslateOptions['default_language'] == 'pl') echo esc_attr('selected'); ?>>Polish</option>
			<option value="pt" <?php if($wpTranslateOptions['default_language'] == 'pt') echo esc_attr('selected'); ?>>Portuguese</option>
			<option value="ro" <?php if($wpTranslateOptions['default_language'] == 'ro') echo esc_attr('selected'); ?>>Romanian</option>
			<option value="ru" <?php if($wpTranslateOptions['default_language'] == 'ru') echo esc_attr('selected'); ?>>Russian</option>
			<option value="sm" <?php if($wpTranslateOptions['default_language'] == 'sm') echo esc_attr('selected'); ?>>Samoan</option>
			<option value="gd" <?php if($wpTranslateOptions['default_language'] == 'gd') echo esc_attr('selected'); ?>>Scots Gaelic</option>
			<option value="sr" <?php if($wpTranslateOptions['default_language'] == 'sr') echo esc_attr('selected'); ?>>Serbian</option>
			<option value="st" <?php if($wpTranslateOptions['default_language'] == 'st') echo esc_attr('selected'); ?>>Sesotho</option>
			<option value="sn" <?php if($wpTranslateOptions['default_language'] == 'sn') echo esc_attr('selected'); ?>>Shona</option>
			<option value="sd" <?php if($wpTranslateOptions['default_language'] == 'sd') echo esc_attr('selected'); ?>>Sindhi</option>
			<option value="si" <?php if($wpTranslateOptions['default_language'] == 'si') echo esc_attr('selected'); ?>>Sinhala</option>
			<option value="sk" <?php if($wpTranslateOptions['default_language'] == 'sk') echo esc_attr('selected'); ?>>Slovak</option>
			<option value="sl" <?php if($wpTranslateOptions['default_language'] == 'sl') echo esc_attr('selected'); ?>>Slovenian</option>
			<option value="so" <?php if($wpTranslateOptions['default_language'] == 'so') echo esc_attr('selected'); ?>>Somali</option>
			<option value="es" <?php if($wpTranslateOptions['default_language'] == 'es') echo esc_attr('selected'); ?>>Spanish</option>
			<option value="su" <?php if($wpTranslateOptions['default_language'] == 'su') echo esc_attr('selected'); ?>>Sudanese</option>
			<option value="sw" <?php if($wpTranslateOptions['default_language'] == 'sw') echo esc_attr('selected'); ?>>Swahili</option>
			<option value="sv" <?php if($wpTranslateOptions['default_language'] == 'sv') echo esc_attr('selected'); ?>>Swedish</option>
			<option value="tg" <?php if($wpTranslateOptions['default_language'] == 'tg') echo esc_attr('selected'); ?>>Tajik</option>
			<option value="tl" <?php if($wpTranslateOptions['default_language'] == 'tl') echo esc_attr('selected'); ?>>Tamil</option>
			<option value="te" <?php if($wpTranslateOptions['default_language'] == 'te') echo esc_attr('selected'); ?>>Telugu</option>
			<option value="th" <?php if($wpTranslateOptions['default_language'] == 'th') echo esc_attr('selected'); ?>>Thai</option>
			<option value="tr" <?php if($wpTranslateOptions['default_language'] == 'tr') echo esc_attr('selected'); ?>>Turkish</option>
			<option value="uk" <?php if($wpTranslateOptions['default_language'] == 'uk') echo esc_attr('selected'); ?>>Ukrainian</option>
			<option value="ur" <?php if($wpTranslateOptions['default_language'] == 'ur') echo esc_attr('selected'); ?>>Urdu</option>
			<option value="uz" <?php if($wpTranslateOptions['default_language'] == 'uz') echo esc_attr('selected'); ?>>Uzbek</option>
			<option value="vi" <?php if($wpTranslateOptions['default_language'] == 'vi') echo esc_attr('selected'); ?>>Vietnamese</option>
			<option value="cy" <?php if($wpTranslateOptions['default_language'] == 'cy') echo esc_attr('selected'); ?>>Welsh</option>
			<option value="xh" <?php if($wpTranslateOptions['default_language'] == 'xh') echo esc_attr('selected'); ?>>Xhosa</option>
			<option value="yi" <?php if($wpTranslateOptions['default_language'] == 'yi') echo esc_attr('selected'); ?>>Yiddish</option>
			<option value="yo" <?php if($wpTranslateOptions['default_language'] == 'yo') echo esc_attr('selected'); ?>>Yoruba</option>
			<option value="zu" <?php if($wpTranslateOptions['default_language'] == 'zu') echo esc_attr('selected'); ?>>Zulu</option>
		</select>
	</div>    	
	<div class="wpt-setting-label-wrap"><?php _e('Exclude from Mobile Browsers', 'wptranslate-pro'); ?></div>
	<div class="wpt-setting-value-wrap"><input type="checkbox" id="excludeMobile" name="excludeMobile" value="true"<?php echo ($wpTranslateOptions['exclude_mobile']) ? " checked='yes'" : ""; ?> /></div>	
	<div class="wpt-setting-label-wrap"><?php _e('Toolbar Auto Display', 'wptranslate-pro'); ?></div>
	<div class="wpt-setting-value-wrap"><input type="checkbox" id="autoDisplay" name="autoDisplay" value="true"<?php echo ($wpTranslateOptions['auto_display']) ? " checked='yes'" : ""; ?> /></div>    
	<div class="wpt-divider"><h3><?php _e('Translation Tracking - Google Analytics', 'wptranslate-pro'); ?></h3></div>
    <div class="wpt-setting-label-wrap"><?php _e('Tracking enabled', 'wptranslate-pro'); ?></div> 
	<div class="wpt-setting-value-wrap"><input type="checkbox" id="trackingEnabled" name="trackingEnabled" value="true"<?php echo ($wpTranslateOptions['tracking_enabled']) ? " checked='yes'" : ""; ?> /></div>    
    <div class="wpt-setting-label-wrap"><?php _e('Tracking ID (UA#)', 'wptranslate-pro'); ?></div>
	<div class="wpt-setting-value-wrap"><input type="text" id="trackingId" name="trackingId" value="<?php echo esc_attr($wpTranslateOptions['tracking_id']); ?>" /></div>
    <div class="wpt-divider"><p class="major-publishing-actions"><input type="submit" name="Submit" id="btn-wp-transalate-settings" class="button-primary" value="<?php _e('Save Settings', 'wptranslate-pro'); ?>" /></p></div>
    </form>
	</div>
	</div>
	<div id="wpt-right-column">
		<!--<div id="rss" class="wp-translate-settings-wrap">
		</div>-->
        <div class="wp-translate-settings-wrap">                
			<p><strong>WP Translate Pro</strong><br/><em>Show country flag icons next to languages and remove Google branding. Also has short code for pages that don't display sidebar widgets.</em> <a href="http://plugingarden.com/google-translate-wordpress-plugin/?src=wpt" target="_blank">See it in action</a>!</p>
			<p><strong><?php _e('Try WP Easy Gallery Pro', 'wp-translate'); ?></strong><br /><em><?php _e('Pro Features include: Multi-image uploader, Enhanced admin section for easier navigation, Image preview pop-up, and more', 'wp-translate'); ?>...</em></p>
			<p><a href="http://plugingarden.com/wordpress-gallery-plugin/?src=wpt" target="_blank"><img src="http://plugingarden.com/wp-content/uploads/2017/08/WP-Easy-Gallery-Pro_200x160.gif" width="200" height="160" border="0" alt="WP Easy Gallery Pro"></a></p>
			<!--<p><strong><?php _e('Try Custom Post Donations Premium', 'wp-translate'); ?></strong><br /><em><?php _e('This WordPress plugin will allow you to create unique customized PayPal donation widgets to insert into your WordPress posts or pages and accept donations. Features include: Multiple Currencies, Multiple PayPal accounts, Custom donation form display titles, and more.', 'wp-translate'); ?></em></p>
			<p><a href="http://plugingarden.com/wordpress-paypal-plugin/?src=wpt" target="_blank"><img src="http://plugingarden.com/wp-content/uploads/2017/08/CustomPostDonationsPro-200x400v4.png" width="200" height="400" alt="Custom Post Donations Pro" border="0"></a></p>
			<p><strong><?php _e('Try Email Obfuscate', 'wp-translate'); ?></strong><br /><em><?php _e('Email Obfuscate is a Lightweight WordPress/jQuery plugin that prevents spam-bots from harvesting your email addresses by dynamically obfuscating email addresses on your site.', 'wp-translate'); ?></em><br /><a href="http://codecanyon.net/item/wordpressjquery-email-obfuscate-plugin/721738?ref=HahnCreativeGroup" target="_blank">Email Obfuscate Plugin</a></p>
			<p><a href="http://codecanyon.net/item/wordpressjquery-email-obfuscate-plugin/721738?ref=HahnCreativeGroup" target="_blank"><img alt="WordPress/jQuery Email Obfuscate Plugin - CodeCanyon Item for Sale" border="0" class="landscape-image-magnifier preload no_preview" height="80" src="<?php echo plugins_url( '/images/WordPress-Email-Obfuscate_thumb_80x80.jpg', __FILE__ ); ?>" title="" width="80"></a></p>-->
			<p><em><?php _e('Please consider making a donation for the continued development of this plugin. Thank you.', 'wp-translate'); ?></em></p>
			<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EJVXJP3V8GE2J" target="_blank"><img src="<?php echo plugins_url( '/images/btn_donateCC_LG.gif', __FILE__ ); ?>" border="0" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></a></p>
		</div>
</div>
<div id="wp-translate-update-status"></div>
<script type="text/javascript">
jQuery(document).ready(function(){			
	jQuery.ajax({url: "<?php echo plugins_url( '/rss.php', __FILE__ ); ?>",success:function(result){
		//jQuery("#rss").html(result);
  }});
});
</script>
</div>