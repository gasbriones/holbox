<?php
namespace Weglot\Client;

use Weglot\Exceptions\WeglotException;
use Weglot\Third\WooCommerce\WeglotTranslateWoo;
use WeglotSDP;

class WeglotClient
{
    protected $api_key;

    const API_BASE     = 'https://api.weglot.com';
    const API_BASE_OLD = 'https://weglot.com/api/';
    const NUMBER_MAX_CHARS = 500;

    protected static $NODES_TO_IGNORE = null;

    public function __construct($key)
    {
        self::$NODES_TO_IGNORE = array("strong","em","abbr","acronym","b","bdo","big","cite","kbd","q","small","sub","sup");

        $this->api_key = $key;

        if ($this->api_key == null || mb_strlen($this->api_key) == 0) {
            return null;
        }

        $this->initThird();
    }

    public function initThird()     // TODO : Dependency injection
    {
        $this->thirds = array();

        if (class_exists('WooCommerce')) {
            $this->thirds["woocommerce"] = new WeglotTranslateWoo();
        }
    }

    public function hasAncestorAttribute($node, $attribute)
    {
        $currentNode = $node;

        if (isset($currentNode->$attribute)) {
            return true;
        }

        while ($currentNode->parent() && $currentNode->parent()->tag != 'html') {
            if (isset($currentNode->parent()->$attribute)) {
                return true;
            } else {
                $currentNode = $currentNode->parent();
            }
        }
        return false;
    }

    public function checkTitle($row)
    {
        return true;
    }

    public function checkText($row)
    {
        return ($row->parent()->tag != 'script'
            && $row->parent()->tag != 'style'
            && $row->parent()->tag != 'noscript'
            && $row->parent()->tag != 'title'
            && $row->parent()->tag != 'code'
            && ! is_numeric($this->full_trim($row->outertext))
            && ! preg_match('/^\d+%$/', $this->full_trim($row->outertext))
            && strpos($row->outertext, '[vc_') === false);
    }

    public function checkButton($row)
    {
        return (! is_numeric($this->full_trim($row->value))
             && ! preg_match('/^\d+%$/', $this->full_trim($row->value)));
    }

    public function checkTd_dt($row)
    {
        return true;
    }

    public function checkInput_dv($row)
    {
        return true;
    }

    public function checkInput_dobt($row)
    {
        return true;
    }

    public function checkRad_obt($row)
    {
        return true;
    }


    public function checkPlaceholder($row)
    {
        return (! is_numeric($this->full_trim($row->placeholder))
            && ! preg_match('/^\d+%$/', $this->full_trim($row->placeholder)));
    }

    public function checkMeta_desc($row)
    {
        return (! is_numeric($this->full_trim($row->placeholder))
            && ! preg_match('/^\d+%$/', $this->full_trim($row->placeholder)));
    }

    public function checkIframe_src($row)
    {
        return (strpos($this->full_trim($row->src), 'youtube.') !== false);
    }

    public function checkSource_src($row)
    {
        return true;
    }

    public function checkImg_src($row)
    {
        return true;
    }

    public function checkImg_alt($row)
    {
        return true;
    }

    public function checkA_pdf($row)
    {
        return (
            strtolower(substr($this->full_trim($row->href), -4)) == '.pdf'
            || strtolower(substr($this->full_trim($row->href), -4)) == '.rar'
            || strtolower(substr($this->full_trim($row->href), -4)) == 'docx'
        );
    }

    public function checkA_title($row)
    {
        return true;
    }

    public function checkA_dv($row)
    {
        return true;
    }

    public function checkA_dt($row)
    {
        return true;
    }

    public function checkA_dto($row)
    {
        return true;
    }

    public function checkA_dho($row)
    {
        return true;
    }

    public function checkA_dco($row)
    {
        return true;
    }

    public function checkA_dte($row)
    {
        return true;
    }

    public function searchForId($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['uid'] === $id) {
                return $key;
            }
        }
        return null;
    }

    function ignoreNodes($dom)
    {
        foreach (self::$NODES_TO_IGNORE as $n) {

            $openRegex = "#<$n( [^>]*)?>#";
            $closeRegex = "</$n>";

            $dom = preg_replace($openRegex, "&lt;$n$1&gt;", $dom);
            $dom = str_replace($closeRegex, "&lt;/$n&gt;", $dom);
        }

        return $dom;
    }

    function decodeNode($dom) {
        return str_replace('&gt;','>',str_replace('&lt;','<',$dom));
    }


    public function translateDomFromTo($dom, $l_from, $l_to)
    {
        if (strlen($this->api_key) == 36) {
            $dom = $this->ignoreNodes($dom);
        }

        $html = WeglotSDP\str_get_html($dom, true, true, WG_DEFAULT_TARGET_CHARSET, false, WG_DEFAULT_BR_TEXT, WG_DEFAULT_SPAN_TEXT);

        $exceptions = explode(',', get_option('exclude_blocks'));
        array_push($exceptions, '#wpadminbar');
        foreach ($exceptions as $exception) {
            foreach ($html->find($exception) as $k => $row) {
                $attribute       = 'data-wg-notranslate';
                $row->$attribute = '';
            }
        }

        $words = array();
        $nodes = array();
        $prewords = array();
        $endwords = array();

        $elements_to_check = array(

            'title'
            => array(
                array(
                    'property' => 'innertext',
                    't' => 4,
                    'type' => 'text',
                ),
            ),


            'meta[name="description"],meta[property="og:title"],meta[property="og:description"],meta[property="og:site_name"],meta[name="twitter:title"],meta[name="twitter:description"]'
            => array(
                array(
                    'property' => 'content',
                    't' => 4,
                    'type' => 'meta_desc',
                ),
            ),

            'meta[name="twitter:image"],meta[name="og:image"]'
            => array(
                array(
                    'property' => 'content',
                    't' => 6,
                    'type' => 'meta_desc',
                ),
            ),

            'text'
            => array(
                array(
                    'property' => 'outertext',
                    't' => 1,
                    'type' => 'text',
                ),
            ),


            "input[type='submit'],input[type='button'],button"
                => array(
                    array(
                        'property' => 'value',
                        't' => 2,
                        'type' => 'button',
                    ),
                    array(
                        'property' => 'data-value',
                        't' => 1,
                        'type' => 'input_dv',
                    ),
                    array(
                        'property' => 'data-order_button_text',
                        't' => 1,
                        'type' => 'input_dobt',
                    ),
                ),

            "input[type='radio']"
            => array(
                array(
                    'property' => 'data-order_button_text',
                    't' => 2,
                    'type' => 'rad_obt',
                ),
            ),


            "td"
            => array(
                array(
                    'property' => 'data-title',
                    't' => 2,
                    'type' => 'td_dt',
                ),
            ),

            "input[type=\'text\'],input[type=\'password\'],input[type=\'search\'],input[type=\'email\'],input:not([type]),textarea"
                => array(
                    array(
                        'property' => 'placeholder',
                        't' => 3,
                        'type' => 'placeholder',
                    ),
                ),

            'iframe'
                => array(
                    array(
                        'property' => 'src',
                        't' => 5,
                        'type' => 'iframe_src',
                    ),
                ),

            'img'
            => array(
                array(
                    'property' => 'src',
                    't' => 6,
                    'type' => 'img_src',
                ),
                array(
                    'property' => 'alt',
                    't' => 7,
                    'type' => 'img_alt',
                ),
            ),

            'source'
            => array(
                array(
                    'property' => 'src',
                    't' => 5,
                    'type' => 'source_src',
                ),
            ),

            'a'
            => array(
                array(
                    'property' => 'href',
                    't' => 8,
                    'type' => 'a_pdf',
                ),
                array(
                    'property' => 'title',
                    't' => 1,
                    'type' => 'a_title',
                ),
                array(
                    'property' => 'data-value',
                    't' => 1,
                    'type' => 'a_dv',
                ),
                array(
                    'property' => 'data-title',
                    't' => 1,
                    'type' => 'a_dt',
                ),
                array(
                    'property' => 'data-tooltip',
                    't' => 1,
                    'type' => 'a_dto',
                ),
                array(
                    'property' => 'data-hover',
                    't' => 1,
                    'type' => 'a_dho',
                ),
                array(
                    'property' => 'data-content',
                    't' => 1,
                    'type' => 'a_dco',
                ),
                array(
                    'property' => 'data-text',
                    't' => 1,
                    'type' => 'a_dte',
                ),
                array(
                    'property' => 'data-avia-tooltip',
                    't' => 1,
                    'type' => 'a_dat'
                )
            ),
        );

        $elements_to_check = apply_filters("weglot_elements_to_check", $elements_to_check);

        foreach ($elements_to_check as $key => $elem) {
            foreach ($html->find($key) as $k => $row) {
                foreach ($elem as $element) {
                    $property     = $element['property'];
                    $t            = $element['t'];
                    $type         = $element['type'];
                    $functionName = apply_filters('weglot_check_type_element', 'check' . ucfirst($type), $type);

                    $checkType = true;
                    if (method_exists($this, $functionName)) {
                        $checkType = $this->$functionName($row);
                    } elseif (function_exists($functionName)) {
                        $checkType = $functionName($row);
                    }

                    if (
                        $this->full_trim($row->$property) != '' &&
                        ! $this->hasAncestorAttribute($row, 'data-wg-notranslate') &&
                        $checkType &&
                        (strlen($row->$property) <= self::NUMBER_MAX_CHARS || preg_match('/\s|。|｡|︒/u', $row->$property) )
                    ) {
                        array_push(
                            $words,
                            array(
                                't' => $t,
                                'w' => $this->trim_inline($row->$property,$prewords,$endwords),
                            )
                        );
                        array_push(
                            $nodes,
                            array(
                                'node' => $row,
                                'type' => $type,
                                'property' => $property,
                            )
                        );
                    } else {
                        if(strpos($row->$property, '&gt;') !== false || strpos($row->$property, '&lt;') !== false) {
                            $row->$property = $this->decodeNode($row->$property);
                        }
                    }
                }
            }
        }


        $microData     = array("description");
        $jsons         =  array();
        $nbJsonStrings = 0;
        foreach ($html->find('script[type="application/ld+json"]') as $k => $row) {
            $mustAddjson = false;
            $json        = json_decode($row->innertext, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                foreach ($microData as $key) {
                    $path  = explode(">", $key);
                    $value = $this->getValue($json, $path);

                    if (isset($value)) {
                        $mustAddjson = true;
                        $this->addValues($value, $words, $nbJsonStrings);
                    }
                }

                if ($mustAddjson) {
                    array_push($jsons, array('node' => $row, 'json' => $json));
                }
            }
        }

        $countWC18n = 0;
        if (isset($this->thirds["woocommerce"])) {
            $countWC18n = $this->thirds["woocommerce"]->translate($dom, $words); // TODO : Improve countWC18n
        }

        $title = 'Empty title';
        foreach ($html->find('title') as $k => $row) {
            if ($row->innertext != '') {
                $title = $row->innertext;
            }
        }


        $absolute_url = $this->full_url($_SERVER);
        if (strpos($absolute_url, 'admin-ajax.php') !== false) {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $absolute_url = sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER']));
            }
            $title = 'Ajax data';
        }

        $otherWords = apply_filters("weglot_words_translate", array());

        if (!empty($otherWords) && is_array($otherWords)) {
            foreach ($otherWords as $otherWord) {

                if (strlen($otherWord) > self::NUMBER_MAX_CHARS && preg_match('/\s|。|｡|︒/u', $otherWord) == false) {
                    continue;
                }

                $words[] = array(
                    "t" => 1,
                    "w" => $otherWord
                );
            }
        }

        $bot = $this->bot_detected();
        $parameters = array(
            'l_from' => $l_from,
            'l_to' => $l_to,
            'title' => $title,
            'request_url' => $absolute_url,
            'bot' => $bot,
            'words' => $words,
        );
        $results = $this->doRequest(self::API_BASE . '/translate?api_key=' . $this->api_key, $parameters);

        $json = json_decode($results, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new WeglotException('Error with Weglot Api (0001) : ' . json_last_error() . ' Error is: ' . serialize($results));
        }

        $answer = $json;
        if (!isset($answer['to_words'])) {
            throw new WeglotException('Unknown error with Weglot Api (0005) Error is: ' . serialize($results));
        }

        $translated_words = $answer['to_words'];
        $from_words       = $answer['from_words'];

        $totalWordsToTranslate = count($nodes) + $nbJsonStrings + $countWC18n + count($otherWords);
        if ($totalWordsToTranslate !== count($translated_words)) {
            throw new WeglotException('Unknown error with Weglot Api (0006)');
        }

        for ($i = 0;$i < count($nodes);$i++) {
            $currentNode = $nodes[$i];
            $property = $currentNode['property'];
            $type     = $currentNode['type'];

            if ($type == "meta_desc") {
                $currentNode['node']->$property = htmlspecialchars($translated_words[$i]);
            } else {
                $currentNode['node']->$property = $prewords[$i].$translated_words[$i].$endwords[$i];
            }


            if ($currentNode['type'] == 'img_src') {
                $currentNode['node']->src = $translated_words[$i];
                if ($currentNode['node']->hasAttribute('srcset') && $currentNode['node']->srcset != '' && htmlspecialchars_decode($translated_words[$i]) != htmlspecialchars_decode($words[$i]['w'])) {
                    $currentNode['node']->srcset = '';
                }
            }
        }

        $index = count($nodes);
        for ($i = 0;$i < count($jsons);$i++) {
            $currentJson = $jsons[$i];
            $jsonArray = $currentJson['json'];
            $node      = $currentJson['node'];
            foreach ($microData as $key) {
                $path = explode(">", $key);
                $hasV = $this->getValue($jsonArray, $path);

                if (isset($hasV)) {
                    $this->setValues($jsonArray, $path, $translated_words, $index);
                }
            }
            $node->innertext = json_encode($jsonArray, JSON_PRETTY_PRINT);
        }

        $dom = $html->save();

        for ($i = 0; $i < $countWC18n; $i++) {
            $dom = str_replace('\"'.$this->unformatFromApi($from_words[$i + $index + $nbJsonStrings]).'\"', '\"'.$this->unformatFromApi($translated_words[$i + $index + $nbJsonStrings]).'\"', $dom);
        }


        if (empty($otherWords)) {
            return $dom;
        }

        $fromWords = array_slice($from_words, $index+ $nbJsonStrings + $countWC18n);
        $otherWordsTranslated = array_slice($translated_words, $index+ $nbJsonStrings + $countWC18n);

        foreach ($fromWords as $key => $fromWord) {
            if(!preg_match("#<[^>\"']*".$fromWord."[^>\"']*>#",$dom)) {
                $dom = preg_replace("#\b$fromWord\b#", $otherWordsTranslated[$key], $dom);
            }
        }

        return $dom;
    }

    public function getUserInfo()
    {
        $results = $this->doRequest(self::API_BASE_OLD . 'user-info?api_key=' . $this->api_key, null);
        $json    = json_decode($results, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            if (isset($json['succeeded']) && ($json['succeeded'] == 0 || $json['succeeded'] == 1)) {
                if ($json['succeeded'] == 1) {
                    if (isset($json['answer'])) {
                        $answer = $json['answer'];
                        return $answer;
                    } else {
                        throw new WeglotException('Unknown error with Weglot Api (0004)');
                    }
                } else {
                    $error = isset($json['error']) ? $json['error'] : 'Unknown error with Weglot Api (0003)';
                    throw new WeglotException($error);
                }
            } else {
                throw new WeglotException('Unknown error with Weglot Api (0002) : ' . $json);
            }
        } else {
            throw new WeglotException('Unknown error with Weglot Api (0001) : ' . json_last_error());
        }
    }

    public function doRequest($url, $parameters)
    {
        if ($parameters) {
            $payload = json_encode($parameters);
            if (json_last_error() == JSON_ERROR_NONE) {
                $response = wp_remote_post(
                    $url,
                    array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'blocking' => true,
                        'headers' => array(
                            'Content-type' => 'application/json',
                        ),
                        'body' => $payload,
                        'cookies' => array(),
                        'sslverify' => false,
                    )
                );
            } else {
                throw new WeglotException('Cannot json encode parameters: ' . json_last_error());
            }
        } else {
            $response = wp_remote_get(
                $url,
                array(
                    'method' => 'GET',
                    'timeout' => 45,
                    'redirection' => 5,
                    'blocking' => true,
                    'headers' => array(
                        'Content-type' => 'application/json',
                    ),
                    'body' => null,
                    'cookies' => array(),
                    'sslverify' => false,
                )
            );
        }

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            throw new WeglotException('Error doing the external request to ' . $url . ': ' . $error_message);
        } else {
            return $response['body'];
        }
    }



    public function bot_detected()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT']));
        }
        if (isset($ua)) {
            if (preg_match('/bot|favicon|crawl|facebook|slurp|spider/i', $ua)) {
                if (strpos($ua, 'Google') !== false || strpos($ua, 'facebook') !== false || strpos($ua, 'wprocketbot') !== false || strpos($ua, 'SemrushBot') !== false) {
                    return 2;
                } elseif (strpos($ua, 'bing') !== false) {
                    return 3;
                } elseif (strpos($ua, 'yahoo') !== false) {
                    return 4;
                } elseif (strpos($ua, 'Baidu') !== false) {
                    return 5;
                } elseif (strpos($ua, 'Yandex') !== false) {
                    return 6;
                } else {
                    return 1;
                }
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }


    public function addValues($value, &$words, &$nbJsonStrings)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $this->addValues($val, $words, $nbJsonStrings);
            }
        } else {
            array_push(
                $words,
                array(
                    't' => 1,
                    'w' => $value,
                )
            );
            $nbJsonStrings++;
        }
    }

    public function setValues(&$data, $path, $translatedwords, &$index)
    {
        $temp = &$data;
        foreach ($path as $key) {
            if (array_key_exists($key, $temp)) {
                $temp = &$temp[$key];
            } else {
                return null;
            }
        }

        if (is_array($temp)) {
            foreach ($temp as $key => &$val) {
                $this->setValues($val, null, $translatedwords, $index) ;
            }
        } else {
            $temp = $translatedwords[$index];
            $index++;
        }
    }

    public function getValue($data, $path)
    {
        $temp = $data;
        foreach ($path as $key) {
            if (array_key_exists($key, $temp)) {
                $temp = $temp[$key];
            } else {
                return null;
            }
        }
        return $temp ;
    }

    public function formatForApi($string)
    {
        $string = '"'.$string.'"';
        return json_decode(str_replace('\\/', '/', str_replace('\\\\', '\\', $string)));
    }

    public function unformatFromApi($string)
    {
        $string =  str_replace('"', '', str_replace('/', '\\\\/', str_replace('\\u', '\\\\u', json_encode($string))));
        return $string;
    }

    public function url_origin($s, $use_forwarded_host = false)
    {
        $ssl      = (! empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp       = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port     = $s['SERVER_PORT'];
        $port     = ((! $ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host     = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host     = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }
    public function full_url($s, $use_forwarded_host = false)
    {
        return $this->url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
    }
    public function full_trim($word)
    {
        return trim($word, " \t\n\r\0\x0B\xA0�");
    }

    /* This function remove inline tag when they are wrapping the whole sentence */
    public function trim_inline($word,&$prewords,&$endwords) {

        $word = html_entity_decode($word);

        $all_nodes = "";
        foreach (self::$NODES_TO_IGNORE as $n) {
            $all_nodes .= $n.'|';
        }
        $all_nodes = trim($all_nodes,'|');

        $regex = "#^<($all_nodes)>.*</($all_nodes)>$#";

        $pre = "";
        $suf = "";

        while(preg_match($regex,$word,$matches)) {
            $b = $matches[1];
            $prefix = "<$b>";
            $suffix = "</$b>";

           if (substr($word, 0, strlen($prefix)) == $prefix) {
                $word = substr($word, strlen($prefix));
               $pre .= $prefix;
            }

            if (substr($word, strlen($word)-strlen($suffix), strlen($suffix)) == $suffix) {
                $word = substr($word , 0 , strlen($word)-strlen($suffix));
                $suf .=$suffix;
            }
        }

        array_push($prewords,$pre);
        array_push($endwords,$suf);

        return $this->ignoreNodes($word);
    }

}
