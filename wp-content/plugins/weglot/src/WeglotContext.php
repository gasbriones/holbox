<?php

namespace Weglot;

abstract class WeglotContext
{
    protected static $originalLang;

    protected static $destinationLang;

    protected static $homeDirectory;

    protected static $currentLang;

    public static function setOriginalLanguage($originalL)
    {
        self::$originalLang = $originalL;
    }

    public static function setDestinationLanguage($destL)
    {
        self::$destinationLang = $destL;
    }

    public static function getOriginalLanguage()
    {
        return self::$originalLang;
    }

    public static function getDestinationLanguage(){
        return self::$destinationLang;
    }

    public static function setHomeDirectory($homeDir)
    {
        self::$homeDirectory = $homeDir;
    }

    public static function getHomeDirectory()
    {
        return self::$homeDirectory;
    }

    public static function setCurrentLanguage($currentLang)
    {
        self::$currentLang = $currentLang;
    }

    public static function getCurrentLanguage()
    {
        return self::$currentLang;
    }
}
