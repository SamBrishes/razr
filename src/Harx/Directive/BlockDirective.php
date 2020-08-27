<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Directive/BlockDirective.php
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

    use Harx\Token;
    use Harx\TokenStream;


    class BlockDirective extends Directive {
        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @return string  The directive name as string.
         */
        public function __construct() {
            $this->name = "block";
        }

        /*
         |  PARSE DIRECTIVE
         |  @since  0.1.0
         |
         |  @param  object  The token stream instance.
         |  @param  object  The token instance.
         |
         |  @return string  The string directive representation or null.
         */
        public function parse(TokenStream $stream, Token $token): ?string {
            if($stream->nextIf("block") && $stream->expect("(")) {
                return sprintf("\$this->getExtension('core')->startBlock%s", $this->parser->parseExpression());
            }
            if($stream->nextIf("endblock")) {
                return "echo(\$this->getExtension('core')->endBlock())";
            }
            return null;
        }
    }
