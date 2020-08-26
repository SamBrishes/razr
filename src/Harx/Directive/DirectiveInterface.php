<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Directive/DirectiveInterface.php
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
    namespace Harx\Directive;

    use Harx\Harx;
    use Harx\Token;
    use Harx\TokenStream;

    interface DirectiveInterface
    {
        /**
         * Gets the name.
         *
         * @return string
         */
        public function getName();

        /**
         * Sets the engine.
         *
         * @param $engine
         */
        public function setEngine(Harx $engine);

        /**
         * Parses a directive.
         *
         * @param  TokenStream $stream
         * @param  Token       $token
         * @return string
         */
        public function parse(TokenStream $stream, Token $token);
    }
