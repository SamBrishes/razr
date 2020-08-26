<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Extension/ExtensionInterface.php
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
    namespace Harx\Extension;

    use Harx\Harx;


    interface ExtensionInterface {
        /*
         |  GET EXTENSION NAME
         |  @since  0.1.0
         |
         |  @return string  The extension na,e.
         */
        public function getName(): string;

        /*
         |  INITIALIZE EXTENSION
         |  @since  0.1.0
         |
         |  @param  object  The Harx engine instance.
         |
         |  @return void
         */
        public function initialize(Harx $engine): void;
    }
