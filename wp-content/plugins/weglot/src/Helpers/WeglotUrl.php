<?php

namespace Weglot\Helpers;

use Weglot\WeglotContext;

abstract class WeglotUrl
{
    public static function isEligibleURL($url) // Change this in another helper and remove static
    {
        $url = urldecode(self::URLToRelative($url));
        //Format exclude URL
        $excludeURL = get_option('exclude_url'); // Too dependecy WP

        if (!empty($excludeURL)) {
            $excludeURL    = preg_replace('#\s+#', ',', trim($excludeURL));

            $excludedUrls  = explode(',', $excludeURL);
            foreach ($excludedUrls as &$ex_url) {
                $ex_url = self::URLToRelative($ex_url);
            }
            $excludeURL = implode(',', $excludedUrls);
        }

        $exclusions = preg_replace('#\s+#', ',', $excludeURL);

        $listRegex      = explode(',', $exclusions);

        $excludeAmp = apply_filters("weglot_exclude_amp", true); // Too dependecy WP

        if ($excludeAmp) {
            $listRegex[] = apply_filters('weglot_regex_amp', '([&\?/])amp(/)?$');
        }

        if (empty($exclusions)) {
            return true;
        }

        foreach ($listRegex as $regex) {
            $str          = self::escapeSlash($regex);
            $prepareRegex = sprintf('/%s/', $str);
            if (preg_match($prepareRegex, $url) === 1) {
                return false;
            }
        }

        return true;
    }

    public static function escapeSlash($str) // Change this in another helper and remove static
    {
        return str_replace('/', '\/', $str);
    }

    public static function URLToRelative($url) // Change this in another helper and remove static
    {
        if ((substr($url, 0, 7) == 'http://') || (substr($url, 0, 8) == 'https://')) {
            // the current link is an "absolute" URL - parse it to get just the path
            $parsed   = parse_url($url);
            $path     = isset($parsed['path']) ? $parsed['path'] : '';
            $query    = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

            if (self::getHomeDirectory()) {
                $relative = str_replace(self::getHomeDirectory(), '', $path);

                return ($relative == '') ? '/' : $relative;
            } else {
                return $path . $query . $fragment;
            }
        }
        return $url;
    }

    /** Returns the subdirectories where WP is installed
    *
    * returns /directories if there is one
    * return empty string otherwise
    *
    */
    public static function getHomeDirectory()
    {
        $homeDir = WeglotContext::getHomeDirectory();
        if (isset($homeDir) ) {
            return $homeDir;
        }

        $opt_siteurl = trim(get_option('siteurl'), '/');
        $opt_home    = trim(get_option('home'), '/');
        if ($opt_siteurl != '' && $opt_home != '') {
            if ((substr($opt_home, 0, 7) == 'http://' && strpos(substr($opt_home, 7), '/') !== false) || (substr($opt_home, 0, 8) == 'https://' && strpos(substr($opt_home, 8), '/') !== false)) {
                $parsed_url = parse_url($opt_home);
                $path       = isset($parsed_url['path']) ? $parsed_url['path'] : '/';
                WeglotContext::setHomeDirectory($path);
                return $path;
            }
        }
        return null;
    }
}
