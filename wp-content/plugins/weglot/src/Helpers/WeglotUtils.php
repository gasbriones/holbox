<?php

namespace Weglot\Helpers;

class WeglotUtils
{
    public static function getLangNameFromCode($code, $english)
    {
        switch ($code) {
            case "af":
                return $english ? "Afrikaans" : "Afrikaans";
            case "sq":
                return $english ? "Albanian" : "Shqip";
            case "am":
                return $english ? "Amharic" : "አማርኛ";
            case "ar":
                return $english ? "Arabic" : "‏العربية‏";
            case "hy":
                return $english ? "Armenian" : "հայերեն";
            case "az":
                return $english ? "Azerbaijani" : "Azərbaycan dili";
            case "ba":
                return $english ? "Bashkir" : "башҡорт теле";
            case "eu":
                return $english ? "Basque" : "Euskara";
            case "be":
                return $english ? "Belarusian" : "Беларуская";
            case "bn":
                return $english ? "Bengali" : "বাংলা";
            case "bs":
                return $english ? "Bosnian" : "Bosanski";
            case "bg":
                return $english ? "Bulgarian" : "български";
            case "my":
                return $english ? "Burmese" : "မြန်မာ";
            case "ca":
                return $english ? "Catalan" : "Català";
            case "km":
                return $english ? "Khmer" : "ភាសាខ្មែរ";
            case "ny":
                return $english ? "Chichewa" : "Chicheŵa";
            case "co":
                return $english ? "Corsican" : "Corsu";
            case "zh":
                return $english ? "Simplified Chinese" : "中文 (简体)";
            case "tw":
                return $english ? "Traditional Chinese" : "中文 (繁體)";
            case "hr":
                return $english ? "Croatian" : "Hrvatski";
            case "cs":
                return $english ? "Czech" : "Čeština";
            case "da":
                return $english ? "Danish" : "Dansk";
            case "nl":
                return $english ? "Dutch" : "Nederlands";
            case "en":
                return $english ? "English" : "English";
            case "eo":
                return $english ? "Esperanto" : "Esperanto";
            case "et":
                return $english ? "Estonian" : "Eesti";
            case "fj":
                return $english ? "Fijian" : "Fidžin";
            case "fl":
                return $english ? "Filipino" : "Filipino";
            case "fi":
                return $english ? "Finnish" : "Suomi";
            case "fr":
                return $english ? "French" : "Français";
            case "gl":
                return $english ? "Galician" : "Galego";
            case "ka":
                return $english ? "Georgian" : "ქართული";
            case "de":
                return $english ? "German" : "Deutsch";
            case "el":
                return $english ? "Greek" : "Ελληνικά";
            case "gu":
                return $english ? "Gujarati" : "ગુજરાતી";
            case "ht":
                return $english ? "Haitian" : "Kreyòl ayisyen";
            case "ha":
                return $english ? "Hausa" : "Hausa";
            case "hw":
                return $english ? "Hawaiann" : "Hawaiann";
            case "he":
                return $english ? "Hebrew" : "עברית";
            case "hi":
                return $english ? "Hindi" : "हिंदी";
            case "hu":
                return $english ? "Hungarian" : "Magyar";
            case "is":
                return $english ? "Icelandic" : "Íslenska";
            case "ig":
                return $english ? "Igbo" : "Igbo";
            case "id":
                return $english ? "Indonesian" : "Bahasa Indonesia";
            case "ga":
                return $english ? "Irish" : "Gaeilge";
            case "it":
                return $english ? "Italian" : "Italiano";
            case "ja":
                return $english ? "Japanese" : "日本語";
            case "jv":
                return $english ? "Javanese" : "baṣa Jawa";
            case "kn":
                return $english ? "Kannada" : "ಕನ್ನಡ";
            case "kk":
                return $english ? "Kazakh" : "Қазақша";
            case "ko":
                return $english ? "Korean" : "한국어";
            case "ku":
                return $english ? "Kurdish" : "Kurdí";
            case "ky":
                return $english ? "Kyrgyz" : "кыргызча";
            case "lo":
                return $english ? "Lao" : "ລາວ";
            case "la":
                return $english ? "Latin" : "Latine";
            case "lv":
                return $english ? "Latvian" : "Latviešu";
            case "lt":
                return $english ? "Lithuanian" : "Lietuvių";
            case "lb":
                return $english ? "Luxembourgish" : "Lëtzebuergesch";
            case "mk":
                return $english ? "Macedonian" : "Македонски";
            case "mg":
                return $english ? "Malagasy" : "Malagasy";
            case "ms":
                return $english ? "Malay" : "Bahasa Melayu";
            case "ml":
                return $english ? "Malayalam" : "മലയാളം";
            case "mt":
                return $english ? "Maltese" : "Malti";
            case "mi":
                return $english ? "Māori" : "Māori";
            case "mr":
                return $english ? "Marathi" : "मराठी";
            case "mn":
                return $english ? "Mongolian" : "Монгол";
            case "ne":
                return $english ? "Nepali" : "नेपाली";
            case "no":
                return $english ? "Norwegian" : "Norsk";
            case "ps":
                return $english ? "Pashto" : "پښتو";
            case "fa":
                return $english ? "Persian" : "فارسی";
            case "pl":
                return $english ? "Polish" : "Polski";
            case "pt":
                return $english ? "Portuguese" : "Português";
            case "pa":
                return $english ? "Punjabi" : "ਪੰਜਾਬੀ";
            case "ro":
                return $english ? "Romanian" : "Română";
            case "ru":
                return $english ? "Russian" : "Русский";
            case "sm":
                return $english ? "Samoan" : "Samoa";
            case "gd":
                return $english ? "Scottish Gaelic" : "Gàidhlig na h-Alba";
            case "sr":
                return $english ? "Serbian" : "Српски";
            case "sn":
                return $english ? "Shona" : "chiShona";
            case "sd":
                return $english ? "Sindhi" : "سنڌي";
            case "si":
                return $english ? "Sinhalese" : "සිංහල";
            case "sk":
                return $english ? "Slovak" : "Slovenčina";
            case "sl":
                return $english ? "Slovenian" : "Slovenščina";
            case "so":
                return $english ? "Somali" : "af Soomaali";
            case "st":
                return $english ? "Southern Sotho" : "seSotho";
            case "es":
                return $english ? "Spanish" : "Español";
            case "su":
                return $english ? "Sundanese" : "Sunda";
            case "sw":
                return $english ? "Swahili" : "Kiswahili";
            case "sv":
                return $english ? "Swedish" : "Svenska";
            case "tl":
                return $english ? "Tagalog" : "Tagalog";
            case "ty":
                return $english ? "Tahitian" : "Tahitian";
            case "tg":
                return $english ? "Tajik" : "Тоҷикӣ";
            case "ta":
                return $english ? "Tamil" : "தமிழ்";
            case "tt":
                return $english ? "Tatar" : "Tatar";
            case "te":
                return $english ? "Telugu" : "తెలుగు";
            case "th":
                return $english ? "Thai" : "ภาษาไทย";
            case "to":
                return $english ? "Tongan" : "Tonga";
            case "tr":
                return $english ? "Turkish" : "Türkçe";
            case "uk":
                return $english ? "Ukrainian" : "Українська";
            case "ur":
                return $english ? "Urdu" : "اردو";
            case "uz":
                return $english ? "Uzbek" : "O'zbek";
            case "vi":
                return $english ? "Vietnamese" : "Tiếng Việt";
            case "cy":
                return $english ? "Welsh" : "Cymraeg";
            case "fy":
                return $english ? "Western Frisian" : "Frysk";
            case "xh":
                return $english ? "Xhosa" : "isiXhosa";
            case "yi":
                return $english ? "Yiddish" : "ײִדיש";
            case "yo":
                return $english ? "Yoruba" : "Yorùbá";
            case "zu":
                return $english ? "Zulu" : "isiZulu";
        }
    }

    public static function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

    public static function is_HTML($string)
    {
        return ((preg_match('/<head/', $string, $m) != 0) && !(preg_match('/<xsl/', $string, $m) != 0));
    }

    public static function is_AJAX_HTML($string)
    {
        $r = preg_match_all('/<(a|div|span|p|i|aside|input|textarea|select|h1|h2|h3|h4|meta|button|form|li|strong|ul|option)/',
            $string, $m, PREG_PATTERN_ORDER);
        if (isset($string[0]) && $string[0] != '{' && $r && $r >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle,
                    $temp) !== false);
    }

    public static function hasLanguageRTL($arrayOfCode)
    {
        foreach ($arrayOfCode as $code) {
            if (self::isLanguageRTL($code)) {
                return true;
            }
        }
        return false;
    }

    public static function isLanguageRTL($code)
    {
        $rtls = array('ar', 'he', 'fa');
        if (in_array($code, $rtls)) {
            return true;
        }
        return false;
    }

    public static function hasLanguageLTR($arrayOfCode)
    {
        foreach ($arrayOfCode as $code) {
            if (!self::isLanguageRTL($code)) {
                return true;
            }
        }
        return false;
    }


    public static function is_bot()
    {
        $ua = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? sanitize_text_field(
            wp_unslash($_SERVER['HTTP_USER_AGENT'])
        ) : 'Unknown';
        if (isset($ua)) {
            if (preg_match('/bot|favicon|crawl|facebook|Face|slurp|spider/i', $ua)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
