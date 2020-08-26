<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Loader/LoaderInterface.php
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    0.1.0 [0.1.0] - Alpha
 |
 |  @website    https://github.com/pytesNET/Harx
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2020 pytesNET <info@pytes.net>
 |
 |  @fork       This is a PHP-7.2 fork of razr made by PageKit @ 2014
 |              https://github.com/pagekit/razr
 */
    namespace Harx\Loader;

    interface LoaderInterface
    {
        /**
         * Gets the source code of a template, given its name.
         *
         * @param  string $name
         * @return string
         */
        public function getSource($name);

        /**
         * Gets the cache key to use for the cache for a given template name.
         *
         * @param  string $name
         * @return string
         */
        public function getCacheKey($name);

        /**
         * Returns true if the template is still fresh.
         *
         * @param  string $name
         * @param  int    $time
         * @return bool
         */
        public function isFresh($name, $time);
    }
