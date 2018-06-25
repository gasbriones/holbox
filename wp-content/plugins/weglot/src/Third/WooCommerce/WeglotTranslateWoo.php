<?php

namespace Weglot\Third\WooCommerce;

use Weglot\Models\TranslateInterface;

class WeglotTranslateWoo implements TranslateInterface
{
    public function translate($html, &$words)
    {
        $countTranslate = 0;
        $countTranslate += $this->addWCLabels($html, $words);
    
        return $countTranslate;
    }

    public function addWCLabels($html, &$words)
    {
        $count         = 0;

        preg_match('#wc_address_i18n_params(.*?);#', $html, $match);
        if (!isset($match[1])) {
            return $count;
        }

        preg_match_all('#(label|placeholder)\\\":\\\"(.*?)\\\"#', $match[1], $all);

        $allWords = $all[2];
    
        foreach ($allWords as $value) {
            $value = $this->formatForApi($value);
            array_push(
               $words,
               array(
                   't' => 1,
                   'w' => $value,
               )
            );
            $count++;
        }
        
        return $count;
    }

    public function formatForApi($string) // TODO : Refactoring in a service
    {
        $string = '"'.$string.'"';
        return json_decode(str_replace('\\/', '/', str_replace('\\\\', '\\', $string)));
    }
}
