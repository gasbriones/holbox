<?php

use Weglot\Helpers\WeglotUtils;
use Weglot\Helpers\WeglotLang;
use Weglot\WeglotContext;

$showRTL = false;
$showLTR = false;

if (WeglotUtils::isLanguageRTL(WeglotContext::getOriginalLanguage())) { // Right lo left language
    if (WeglotUtils::hasLanguageLTR(explode(',', WeglotContext::getDestinationLanguage()))) {
        $showLTR = true;
    }
} else { // Left to right language
    if (WeglotUtils::hasLanguageRTL(explode(',', WeglotContext::getDestinationLanguage()))) {
        $showRTL = true;
    }
} ?>
<div class="wrap">
    <?php if ($this->allowed == 0) {
    ?>
        <div class="wg-status-box">
            <h3><?php echo sprintf(esc_html__('Weglot Translate service is not active because you have reached the end of the trial period.', 'weglot'), esc_html__($this->userInfo['limit'])); ?></h3>
            <p><?php echo sprintf(esc_html__('To reactivate the service, please %1$supgrade your plan%2$s.', 'weglot'), '<a target="_blank" href="https://weglot.com/change-plan">', '</a>'); ?></p>
        </div>
    <?php
} ?>

    <?php if (esc_attr(get_option('show_box')) == 'on') {
        ?>
        <div class="wgbox-blur">
            <div class="wgbox">
                <div class="wgclose-btn"><?php esc_html_e('Close', 'weglot'); ?></div>
                <h3 class="wgbox-title"><?php esc_html_e('Well done! Your website is now multilingual.', 'weglot'); ?></h3>
                <p class="wgbox-text"><?php esc_html_e('Go on your website, there is a language switcher. Try it :)', 'weglot'); ?></p>
                <a class="wgbox-button button button-primary" href="
				<?php
                echo esc_html__($this->home_dir); ?>
				/" target="_blank">
                    <?php
                    esc_html_e('Go on my front page.', 'weglot'); ?>
                </a>
                <p class="wgbox-subtext"><?php esc_html_e('Next step, edit your translations directly in your Weglot account.', 'weglot'); ?></p>
            </div>
        </div>
        <?php
        list($wgfirstlang) = explode(',', get_option('destination_l'));
        if (strlen($wgfirstlang) == 2) {
            ?>
            <iframe style="visibility:hidden;" src="<?php
            echo esc_html__($this->home_dir).'/'.esc_html__($wgfirstlang); ?>/" width=1
                    height=1
            ></iframe>
        <?php
        } ?>
        <?php update_option('show_box', 'off');
    } ?>
    <form class="wg-widget-option-form" method="post" action="options.php">
        <?php settings_fields('my-plugin-settings-group'); ?>
        <?php do_settings_sections('my-plugin-settings-group'); ?>
        <h3 style="border-bottom:1px solid #c0c0c0;padding-bottom:10px;max-width:800px;margin-top:40px;"><?php esc_html_e('Main configuration', 'weglot'); ?></h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e('API Key', 'weglot'); ?><p
                            style="font-weight:normal;margin-top:2px;"><?php echo sprintf(esc_html__('Log in to %1$sWeglot%2$s to get your API key.', 'weglot'), '<a target="_blank" href="https://weglot.com/register-wordpress">', '</a>'); ?></p>
                </th>
                <td><input type="text" class="wg-input-text" name="project_key"
                           value="<?php echo esc_attr(get_option('project_key')); ?>"
                           placeholder="wg_XXXXXXXX" required/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Original Language', 'weglot'); ?><p
                            style="font-weight:normal;margin-top:2px;"><?php esc_html_e('What is the original (current) language of your website?', 'weglot'); ?></p>
                </th>
                <td>
                    <select class="wg-input-select" name="original_l" style="width :200px;">
                        <?php
                        $optionL = get_option('original_l', 'en');
                        foreach (WeglotLang::getCodeLangs() as $code): ?>
                             <option <?php selected($optionL, $code); ?> value="<?php echo $code ?>"><?php echo esc_html(WeglotLang::getStrLangByCode($code)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Destination Languages', 'weglot'); ?>
                    <p style="font-weight:normal;margin-top:2px;"><?php echo sprintf(esc_html__('Choose languages you want to translate into. Supported languages can be found %1$shere%2$s.', 'weglot'), '<a target="_blank" href="https://weglot.com/translation-api#languages_code">', '</a>'); ?></p>
                </th>
                <td>
                    <div style="display:inline-block;width:300px;    margin-top: 35px;">
                        <select id="select-lto" multiple class="demo-default"
                                style=""
                                placeholder="French, German, Italian, Portuguese, …"
                                name="destination_l">
                            <?php foreach (WeglotLang::getCodeLangs() as $code): ?>
                                <option <?php if (strpos(esc_attr(get_option('destination_l')), $code) !== false) {
                            echo 'selected';
                        } ?> value="<?php echo $code ?>"><?php echo esc_html(WeglotLang::getStrLangByCode($code)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input id="destination_input_hidden" type="text"
                           class="wg-input-text" name="destination_l"
                           value="<?php echo esc_attr(get_option('destination_l')); ?>"
                           placeholder="en,es" required style="display:none;"/>
                    <?php
                    if ($this->userInfo['plan'] <= 0) {
                        ?>
                        <p class="wg-fsubtext"><?php echo sprintf(esc_html__('On the free plan, you can only choose one language and a maximum of 2000 words. If you want to use more than 1 language and 2000 words, please %1$supgrade your plan%2$s.', 'weglot'), '<a target="_blank" href="https://weglot.com/change-plan">', '</a>'); ?></p><?php
                    } ?>            <?php if ($this->userInfo['plan'] >= 18 && $this->userInfo['plan'] <= 19) {
                        ?>
                        <p class="wg-fsubtext"><?php echo sprintf(esc_html__('On the Starter plan, you can only choose one language. If you want to use more than 1 language, please %1$supgrade your plan%2$s.', 'weglot'), '<a target="_blank" href="https://weglot.com/change-plan">', '</a>'); ?></p><?php
                    } ?>
                </td>
            </tr>
        </table>
        <h3 style="border-bottom:1px solid #c0c0c0;padding-bottom:10px;max-width:800px;margin-top:40px;"><?php echo esc_html__('Language button appearance', 'weglot') . ' ' . esc_html__('(Optional)', 'weglot'); ?></h3>
        <p class="preview-text"><?php esc_html_e('Preview:', 'weglot'); ?></p>
        <div class="wg-widget-preview"></div>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Dropdown?', 'weglot'); ?></th>
                <td><input id="id_is_dropdown" type="checkbox" name="is_dropdown"
                        <?php
                        if (esc_attr(get_option('is_dropdown')) == 'on') {
                            echo 'checked';
                        } ?>
                    /><label for="id_is_dropdown"
                             style="font-weight: normal;margin-left: 20px;font-style: italic;display: inline-block;"><?php esc_html_e('Check if you want the button to be a dropdown box.', 'weglot'); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('With flags?', 'weglot'); ?></th>
                <td><input id="id_with_flags" type="checkbox" name="with_flags"
                        <?php
                        if (esc_attr(get_option('with_flags')) == 'on') {
                            echo 'checked';
                        } ?>
                    /><label for="id_with_flags"
                             style="font-weight: normal;margin-left: 20px;font-style: italic;display: inline-block;"><?php esc_html_e('Check if you want flags in the language button.', 'weglot'); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Type of flags', 'weglot'); ?></th>
                <td>
                    <select class="wg-input-select" name="type_flags"
                            style="width :200px;">
                        <option <?php if (esc_attr(get_option('type_flags')) == '0') {
                            echo 'selected';
                        } ?> value="0"><?php esc_html_e('Rectangle mat', 'weglot'); ?></option>
                        <option <?php if (esc_attr(get_option('type_flags')) == '1') {
                            echo 'selected';
                        } ?> value="1"><?php esc_html_e('Rectangle shiny', 'weglot'); ?></option>
                        <option <?php if (esc_attr(get_option('type_flags')) == '2') {
                            echo 'selected';
                        } ?> value="2"><?php esc_html_e('Square', 'weglot'); ?></option>
                        <option <?php if (esc_attr(get_option('type_flags')) == '3') {
                            echo 'selected';
                        } ?> value="3"><?php esc_html_e('Circle', 'weglot'); ?></option>
                    </select>
                    <div class="flag-style-openclose"><?php esc_html_e('Change country flags', 'weglot'); ?></div>
                    <div class="flag-style-wrapper" style="display:none;">
                        <select class="flag-en-type wg-input-select">
                            <option value=0><?php esc_html_e('Choose English flag:', 'weglot'); ?></option>
                            <option value=0><?php esc_html_e('United Kingdom (default)', 'weglot'); ?></option>
                            <option value=1><?php esc_html_e('United States', 'weglot'); ?></option>
                            <option value=2><?php esc_html_e('Australia', 'weglot'); ?></option>
                            <option value=3><?php esc_html_e('Canada', 'weglot'); ?></option>
                            <option value=4><?php esc_html_e('New Zealand', 'weglot'); ?></option>
                            <option value=5><?php esc_html_e('Jamaica', 'weglot'); ?></option>
                            <option value=6><?php esc_html_e('Ireland', 'weglot'); ?></option>
                        </select>
                        <select class="flag-es-type wg-input-select">
                            <option value=0><?php esc_html_e('Choose Spanish flag:', 'weglot'); ?></option>
                            <option value=0><?php esc_html_e('Spain (default)', 'weglot'); ?></option>
                            <option value=1><?php esc_html_e('Mexico', 'weglot'); ?></option>
                            <option value=2><?php esc_html_e('Argentina', 'weglot'); ?></option>
                            <option value=3><?php esc_html_e('Colombia', 'weglot'); ?></option>
                            <option value=4><?php esc_html_e('Peru', 'weglot'); ?></option>
                            <option value=5><?php esc_html_e('Bolivia', 'weglot'); ?></option>
                            <option value=6><?php esc_html_e('Uruguay', 'weglot'); ?></option>
                            <option value=7><?php esc_html_e('Venezuela', 'weglot'); ?></option>
                            <option value=8><?php esc_html_e('Chile', 'weglot'); ?></option>
                            <option value=9><?php esc_html_e('Ecuador', 'weglot'); ?></option>
                            <option value=10><?php esc_html_e('Guatemala', 'weglot'); ?></option>
                            <option value=11><?php esc_html_e('Cuba', 'weglot'); ?></option>
                            <option value=12><?php esc_html_e('Dominican Republic', 'weglot'); ?></option>
                            <option value=13><?php esc_html_e('Honduras', 'weglot'); ?></option>
                            <option value=14><?php esc_html_e('Paraguay', 'weglot'); ?></option>
                            <option value=15><?php esc_html_e('El Salvador', 'weglot'); ?></option>
                            <option value=16><?php esc_html_e('Nicaragua', 'weglot'); ?></option>
                            <option value=17><?php esc_html_e('Costa Rica', 'weglot'); ?></option>
                            <option value=18><?php esc_html_e('Puerto Rico', 'weglot'); ?></option>
                            <option value=19><?php esc_html_e('Panama', 'weglot'); ?></option>
                        </select>
                        <select class="flag-pt-type wg-input-select">
                            <option value=0><?php esc_html_e('Choose Portuguese flag:', 'weglot'); ?></option>
                            <option value=0><?php esc_html_e('Brazil (default)', 'weglot'); ?></option>
                            <option value=1><?php esc_html_e('Portugal', 'weglot'); ?></option>
                        </select>
                        <select class="flag-fr-type wg-input-select">
                            <option value=0><?php esc_html_e('Choose French flag:', 'weglot'); ?></option>
                            <option value=0><?php esc_html_e('France (default)', 'weglot'); ?></option>
                            <option value=1><?php esc_html_e('Belgium', 'weglot'); ?></option>
                            <option value=2><?php esc_html_e('Canada', 'weglot'); ?></option>
                            <option value=3><?php esc_html_e('Switzerland', 'weglot'); ?></option>
                            <option value=4><?php esc_html_e('Luxemburg', 'weglot'); ?></option>
                        </select>
                        <select class="flag-ar-type wg-input-select">
                            <option value=0><?php esc_html_e('Choose Arabic flag:', 'weglot'); ?></option>
                            <option value=0><?php esc_html_e('Saudi Arabia (default)', 'weglot'); ?></option>
                            <option value=1><?php esc_html_e('Algeria', 'weglot'); ?></option>
                            <option value=2><?php esc_html_e('Egypt', 'weglot'); ?></option>
                            <option value=3><?php esc_html_e('Iraq', 'weglot'); ?></option>
                            <option value=4><?php esc_html_e('Jordan', 'weglot'); ?></option>
                            <option value=5><?php esc_html_e('Kuwait', 'weglot'); ?></option>
                            <option value=6><?php esc_html_e('Lebanon', 'weglot'); ?></option>
                            <option value=7><?php esc_html_e('Libya', 'weglot'); ?></option>
                            <option value=8><?php esc_html_e('Morocco', 'weglot'); ?></option>
                            <option value=14><?php esc_html_e('Oman', 'weglot'); ?></option>
                            <option value=9><?php esc_html_e('Qatar', 'weglot'); ?></option>
                            <option value=10><?php esc_html_e('Syria', 'weglot'); ?></option>
                            <option value=11><?php esc_html_e('Tunisia', 'weglot'); ?></option>
                            <option value=12><?php esc_html_e('United Arab Emirates', 'weglot'); ?></option>
                            <option value=13><?php esc_html_e('Yemen', 'weglot'); ?></option>
                        </select>
                        <p><?php esc_html_e('If you want to use a different flag, just ask us.', 'weglot'); ?></p>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('With name?', 'weglot'); ?></th>
                <td><input id="id_with_name" type="checkbox" name="with_name"
                        <?php
                        if (esc_attr(get_option('with_name')) == 'on') {
                            echo 'checked';
                        } ?>
                    /><label for="id_with_name"
                             style="font-weight: normal;margin-left: 20px;font-style: italic;display: inline-block;"><?php esc_html_e('Check if you want to display the name of languages.', 'weglot'); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Full name?', 'weglot'); ?></th>
                <td><input id="id_is_fullname" type="checkbox" name="is_fullname"
                        <?php
                        if (esc_attr(get_option('is_fullname')) == 'on') {
                            echo 'checked';
                        } ?>
                    /><label for="id_is_fullname"
                             style="font-weight: normal;margin-left: 20px;font-style: italic;display: inline-block;"><?php esc_html_e('Check if you want the name of the languge. Don\'t check if you want the language code.', 'weglot'); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Override CSS', 'weglot'); ?><p
                            style="font-weight:normal;margin-top:2px;"><?php esc_html_e('Don\'t change it unless you want a specific style for your button.', 'weglot'); ?></p>
                </th>
                <td><textarea class="wg-input-textarea" type="text" rows=10 cols=30
                              name="override_css" placeholder=".country-selector {
margin-bottom: 20px;
background-color: green!important;
}
.country-selector a {
color: blue!important;
}"><?php echo esc_attr(get_option('override_css')); ?></textarea><textarea
                            class="wg-input-textarea" type="text" name="flag_css"
                            style="display:none;"><?php echo esc_attr(get_option('flag_css')); ?></textarea>
                </td>
            </tr>
        </table>
        <h3 style="border-bottom:1px solid #c0c0c0;padding-bottom:10px;max-width:800px;margin-top:40px;"><?php echo esc_html__('Language button position', 'weglot') . ' ' . esc_html__('(Optional)', 'weglot'); ?></h3>
        <h4 style="font-size:14px;line-height: 1.3;font-weight: 600;"><?php esc_html_e('Where will the language button be on my website? By default, bottom right.', 'weglot'); ?></h4>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e('In menu?', 'weglot'); ?></th>
                <td><input id="id_is_menu" type="checkbox" name="is_menu"
                        <?php
                        if (esc_attr(get_option('is_menu')) == 'on') {
                            echo 'checked';
                        } ?>
                    /><label for="id_is_menu"
                             style="font-weight: normal;margin-left: 20px;font-style: italic;display: inline-block;"><?php esc_html_e('Check if you want to display the button in the navigation menu.', 'weglot'); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('As a widget?', 'weglot'); ?></th>
                <td>
                    <p style="font-weight: normal;font-style: italic;display: inline-block;"><?php esc_html_e('You can place the button in a widget area. Go to Appearance -> Widgets and drag and drop the Weglot Translate widget where you want.', 'weglot'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('With a shortcode?', 'weglot'); ?></th>
                <td>
                    <p style="font-weight: normal;font-style: italic;display: inline-block;"><?php esc_html_e('You can use the Weglot shortcode [weglot_switcher] wherever you want to place the button.', 'weglot'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('In the source code?', 'weglot'); ?></th>
                <td>
                    <p style="font-weight: normal;font-style: italic;display: inline-block;"><?php esc_html_e('You can add the code &lt;div id=&quot;weglot_here&quot;&gt;&lt;/div&gt; wherever you want in the source code of your HTML page. The button will appear at this place.', 'weglot'); ?></p>
                </td>
            </tr>
        </table>
        <h3 style="border-bottom:1px solid #c0c0c0;padding-bottom:10px;max-width:800px;margin-top:40px;">
            <?php
            echo esc_html__('Translation Exclusion', 'weglot') . ' ' . esc_html__('(Optional)', 'weglot');
            ?>
        </h3>
        <p><?php esc_html_e('By default, every page is translated. You can exclude parts of a page or a full page here.', 'weglot'); ?></p>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Exclude URL here', 'weglot'); ?><p
                            style="font-weight:normal;margin-top:2px;"><?php esc_html_e('You can write regex.', 'weglot'); ?>
                    <p></th>
                <td><textarea class="wg-input-textarea" type="text" rows=3 cols=30
                              name="exclude_url"
                              placeholder=""><?php echo esc_attr(get_option('exclude_url')); ?></textarea>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Exclude blocks', 'weglot'); ?><p
                            style="font-weight:normal;margin-top:2px;"><?php esc_html_e('Enter CSS selectors, separated by commas.', 'weglot'); ?>
                    <p></th>
                <td><textarea class="wg-input-textarea" type="text" rows=3 cols=30
                              name="exclude_blocks"
                              placeholder="#top-menu,footer a,.title-3"><?php echo esc_attr(get_option('exclude_blocks')); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
        $classHideOption = "hidden-option";
        if ($this->userInfo['plan'] > 0) {
            $classHideOption = "";
        }
        ?>
        <h3 class="<?php echo $classHideOption; ?>" style="border-bottom:1px solid #c0c0c0;padding-bottom:10px;max-width:800px;margin-top:40px;">
            <?php
            echo esc_html__('Other options', 'weglot') . ' ' . esc_html__('(Optional)', 'weglot'); ?>
        </h3>
        <table class="form-table <?php echo $classHideOption; ?>">
            <tr valign="top" class="hidden-option">
                <th scope="row"><?php esc_html_e('Exclude AMP pages ?', 'weglot'); ?></th>
                <td><input id="id_exclude_amp" type="checkbox"
                            name="wg_exclude_amp"
                        <?php
                        if (esc_attr(get_option('wg_exclude_amp', 'on')) == 'on') {
                            echo 'checked';
                        } ?>
                    /><label for="id_exclude_amp"
                                style="font-weight: normal;margin-left: 20px;font-style: italic;display: inline-block;"><?php esc_html_e('Exclude translation for Accelerated Mobile Pages. Default : On', 'weglot'); ?></label>
                </td>
            </tr>

        <?php if ($this->userInfo['plan'] > 0) {
            ?>


            <tr valign="top">
                <th scope="row"><?php esc_html_e('Auto redirect?', 'weglot'); ?></th>
                <td><input id="id_auto_switch" type="checkbox"
                            name="wg_auto_switch"
                        <?php
                        if (esc_attr(get_option('wg_auto_switch')) == 'on') {
                            echo 'checked';
                        } ?>
                    /><label for="id_auto_switch"
                                style="font-weight: normal;margin-left: 20px;font-style: italic;display: inline-block;"><?php esc_html_e('Check if you want to redirect users based on their browser language.', 'weglot'); ?></label>
                </td>
            </tr>

        <?php
            } ?>
        </table>
        <?php
        if ($showLTR || $showRTL) {
            $ltrOrRtl = $showLTR ? esc_html__('Left to Right languages', 'weglot') : esc_html__('Right to Left languages', 'weglot'); ?>
            <h3 style="border-bottom:1px solid #c0c0c0;padding-bottom:10px;max-width:800px;margin-top:40px;">
                <?php
                echo esc_html__('Customize style for ', 'weglot') . esc_html__($ltrOrRtl) . ' ' . esc_html__('(Optional)', 'weglot'); ?>
            </h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo sprintf(esc_html__('Write CSS rules to apply on %s page.', 'weglot'), esc_html__($ltrOrRtl)); ?>
                        <p style="font-weight:normal;margin-top:2px;">
                        <p></th>
                    <td><textarea class="wg-input-textarea" type="text" rows=5
                                  cols=30 name="rtl_ltr_style" placeholder="body {
text-align: right;
}"><?php echo esc_attr(get_option('rtl_ltr_style')); ?></textarea></td>
                </tr>
            </table>
        <?php
        } ?>
        <?php submit_button(); ?>
    </form>
    <?php
    if (esc_attr(get_option('show_box')) == 'off') {
        ?>
        <div class="wginfobox">
        <h3><?php esc_html_e('Where are my translations?', 'weglot'); ?></h3>
        <div>
            <p><?php esc_html_e('You can find all your translations in your Weglot account:', 'weglot'); ?></p>
            <a href="<?php esc_html_e('https://weglot.com/dashboard', 'weglot'); ?>"
               target="_blank"
               class="wg-editbtn"><?php esc_html_e('Edit my translations', 'weglot'); ?></a>
        </div>
        </div><?php
    } ?>
    <br>
    <a target="_blank"
       href="http://wordpress.org/support/view/plugin-reviews/weglot?rate=5#postform">
        <?php esc_html_e('Love Weglot? Give us 5 stars on WordPress.org :)', 'weglot'); ?>
    </a>
    <br><br>
    <i class="fa fa-question-circle" aria-hidden="true"
       style="font-size : 17px;"></i>
    <p style="display:inline-block; margin-left:5px;"><?php echo sprintf(esc_html__('If you need any help, you can contact us via our live chat at %1$sweglot.com%2$s or email us at support@weglot.com.', 'weglot'), '<a href="https://weglot.com/" target="_blank">', '</a>') . '<br>' . sprintf(esc_html__('You can also check our %1$sFAQ%2$s', 'weglot'), '<a href="http://support.weglot.com/" target="_blank">', '</a>'); ?></p>
    <br><br><br>

</div>
