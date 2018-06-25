
<?php
use Weglot\WeglotContext;

$apacheModRewrite = ' <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
if (!function_exists('apache_get_modules')) {
    $apacheModRewrite = 'Unknown';
} elseif (!in_array('mod_rewrite', apache_get_modules())) {
    $apacheModRewrite = '<mark class="error"><span class="dashicons dashicons-warning"></span></mark>';
}

$phpMin56 = true;
if (version_compare(phpversion(), "5.6", "<")) {
    $phpMin56 = false;
}

?>

<script>
    jQuery(document).ready(function(){
        jQuery("#system-report").on("click", function(e){
            e.preventDefault();

            jQuery("#view-system-report").slideToggle();
        })
    })
</script>

<div class="wrap">
    <button class="button" id="system-report">
        <?php _e("Get system report", "weglot"); ?>
    </button>
    <div id="view-system-report" style="display:none";>
        <textarea readonly="readonly" style="width:100%; min-height:300px;">
            ## WordPress environment

            Home URL: <?php echo home_url() . "\n"; ?>
            Site URL: <?php echo site_url() . "\n"; ?>
            Weglot version: <?php echo WEGLOT_VERSION . "\n" ?>
            WP version: <?php echo get_bloginfo('version') . "\n"; ?>
            WP multisite: <?php  echo is_multisite() ? "Yes\n" :"-\n" ?>
            WP debug mode: <?php  echo (defined(WP_DEBUG)) ? "Yes\n" : "-\n"; ?>
            Permalink structure: <?php echo get_option('permalink_structure') . "\n"; ?>
            Language: <?php echo get_locale() . "\n"; ?>

            ## Server environment

            Server info: <?php echo (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] . "\n" : "Unknown\n" ?>
            PHP version: <?php echo phpversion() . "\n"; ?>
            Module mod_rewrite: <?php echo $apacheModRewrite . "\n"; ?>

            ## Weglot environment

            Original Language: <?php echo WeglotContext::getOriginalLanguage() . "\n"; ?>
            Destination Language: <?php echo WeglotContext::getDestinationLanguage() . "\n"; ?>
            Exclude URLs: <?php echo get_option('exclude_url') . "\n"; ?>
            Exclude blocks: <?php echo get_option('exclude_blocks') . "\n"; ?>
        </textarea>
    </div>
    <br />
    <br />
    <table class="widefat" cellspacing="0" id="status">
        <thead>
            <tr>
                <th colspan="3" data-export-label="WordPress Environment"><h2>WordPress environment</h2></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Home URL:</td>
                <td><?php echo home_url(); ?></td>
            </tr>
            <tr>
                <td>Site URL:</td>
                <td><?php echo site_url(); ?></td>
            </tr>
            <tr>
                <td>Weglot version:</td>
                <td><?php echo WEGLOT_VERSION ?></td>
            </tr>
            <tr>
                <td>WP version:</td>
                <td><?php echo get_bloginfo('version'); ?></td>
            </tr>
            <tr>
                <td>WP multisite:</td>
                <td>
                    <?php  echo is_multisite() ? "Yes" :"-" ?>

                </td>
            </tr>
            <tr>
                <td>WP debug mode:</td>
                <td>
                    <?php if (defined(WP_DEBUG) && WP_DEBUG) {
    ?>
                        <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
                        <?php
} ?>
                </td>
            </tr>
            <tr>
                <td>Permalink structure:</td>
                <td><?php echo get_option('permalink_structure'); ?></td>
            </tr>
            <tr>
                <td>Language:</td>
                <td><?php echo get_locale(); ?></td>
            </tr>
        </tbody>
    </table>
    <br />
    <table class="widefat" cellspacing="0">
        <thead>
            <tr>
                <th colspan="3" data-export-label="Server Environment"><h2>Server environment</h2></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Server info:</td>
                <td><?php echo (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] : "Unknown" ?></td>
            </tr>
            <tr>
                <td>PHP version:</td>
                <td>
                    <?php echo phpversion(); ?>
                    <?php if (!$phpMin56) {
        ?>
                        <mark class="error">
                            <span class="dashicons dashicons-warning"></span> - <?php echo __("We recommend a minimum PHP version of 5.6.", "weglot") ?>
                        </mark>
                    <?php
    } ?>
                </td>
            </tr>
            <tr>
                <td>Module mod_rewrite:</td>
                <td>
                    <?php echo $apacheModRewrite; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <br />
    <table class="widefat" cellspacing="0">
        <thead>
            <tr>
                <th colspan="3" data-export-label="Server Environment"><h2>Weglot environment</h2></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Original Language:</td>
                <td><?php echo WeglotContext::getOriginalLanguage(); ?></td>
            </tr>
            <tr>
                <td>Destination Language:</td>
                <td><?php echo WeglotContext::getDestinationLanguage(); ?></td>
            </tr>
            <tr>
                <td>Exclude URLs:</td>
                <td><?php echo get_option('exclude_url'); ?></td>
            </tr>
            <tr>
                <td>Exclude blocks:</td>
                <td><?php echo get_option('exclude_blocks'); ?></td>
            </tr>
        </tbody>
    </table>
</div>
