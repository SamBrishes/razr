<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Loader/StringLoader.php
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

    class StringLoader implements LoaderInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getSource($name)
        {
            return $name;
        }

        /**
         * {@inheritdoc}
         */
        public function getCacheKey($name)
        {
            return $name;
        }

        /**
         * {@inheritdoc}
         */
        public function isFresh($name, $time)
        {
            return true;
        }
    }
