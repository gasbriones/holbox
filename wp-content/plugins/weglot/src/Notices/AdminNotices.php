<?php

namespace Weglot\Notices;

class AdminNotices
{
    public function wgExceededFreeLimit()
    {
        add_action('admin_notices', array($this, 'wgExceededFreeLimitNotice'), 0);
    }

    /**
     * @see wgExceededFreeLimit
     */
    public function wgExceededFreeLimitNotice()
    {
        ?>
        <div class="updated settings-error notice is-dismissible">
            <p><?php echo sprintf(esc_html__('Weglot Translate is not active because you have exceeded the free limit. Please %1$supgrade your plan%2$s if you want to keep the service running.', 'weglot'), '<a target="_blank" href="https://weglot.com/change-plan">', '</a>'); ?></p>
        </div>
        <?php
    }

    public function wgRequireRewriteModule()
    {
        add_action('admin_notices', array($this, 'wgRequireRewriteModuleNotice'), 0);
    }

    /**
     * @see wgRequireRewriteModule
     */
    public function wgRequireRewriteModuleNotice()
    {
        ?>
        <div class="error settings-error notice is-dismissible">
            <p><?php echo sprintf(esc_html__('Weglot Translate: You need to activate the mod_rewrite module. You can find more information here : %1$sUsing Permalinks%2$s. If you need help, just ask us directly at support@weglot.com.', 'weglot'), '<a target="_blank" href="https://codex.wordpress.org/Using_Permalinks">', '</a>'); ?></p>
        </div>
        <?php
    }

    public function wgNeedConfiguration()
    {
        add_action('admin_notices', array($this, 'wgNeedConfigurationNotice'), 0);
    }

    /**
     * @see wgNeedConfiguration
     */
    public function wgNeedConfigurationNotice()
    {
        ?>
        <div class="error settings-error notice is-dismissible">
            <p><?php echo sprintf(esc_html__('Weglot Translate is installed but not yet configured, you need to configure Weglot here : %1$sWeglot configuration page%2$s. The configuration takes only 1 minute! ', 'weglot'), '<a href="'.admin_url().'admin.php?page=weglot">', '</a>'); ?></p>
        </div>
        <?php
    }

    public function thirdNotices()
    {
        add_action('admin_notices', array($this, 'wgGtranslateNotice'), 0);
    }

    public function hasGTranslatePlugin() // Not single responsability
    {
        return is_plugin_active('gtranslate/gtranslate.php');
    }


    public function wgGtranslateNotice()
    {
        if (!$this->hasGTranslatePlugin()) {
            return;
        }


        $screen = get_current_screen();

        if ($screen->id === "toplevel_page_Weglot") {
            return;
        }

        $pluginFile = 'gtranslate/gtranslate.php';
        $deactivateLink =  wp_nonce_url('plugins.php?action=deactivate&amp;plugin='.urlencode($pluginFile).'&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $pluginFile)

        ?>
        <div class="error settings-error notice is-dismissible">
            <p><?php echo sprintf(esc_html__('Please %1$sdeactivate GTranslate%2$s. This plugin causes conflicts with Weglot.', 'weglot'), '<a href="'.$deactivateLink.'">', '</a>'); ?></p>
        </div>
        <?php
    }
}
