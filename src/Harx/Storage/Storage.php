<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Storage/Storage.php
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
    namespace Harx\Storage;

    abstract class Storage {
        /*
         |  TEMPLATE STRING
         |  @type   string
         */
        protected $template;


        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  string  The template string to store.
         */
        public function __construct(string $template) {
            $this->template = $template;
        }

        /*
         |  MAGIC :: OBJECT STRING REPRESENTATION
         |  @since  0.1.0
         |
         |  @return string  The template object as string.
         */
        public function __toString() {
            return (string) $this->template;
        }

        /*
         |  ABSTRACT :: GET TEMPLATE CONTENT
         |  @since  0.1.0
         |
         |  @return string  The template content.
         */
        abstract public function getContent(): string { }
    }
