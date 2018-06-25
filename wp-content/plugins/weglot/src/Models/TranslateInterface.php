<?php

namespace Weglot\Models;

interface TranslateInterface
{
    public function translate($html, &$words);
}
