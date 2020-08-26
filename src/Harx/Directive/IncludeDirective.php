<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Directive/IncludeDirectuve.php
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

    class IncludeDirective extends Directive
    {
        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->name = 'include';
        }

        /**
         * @{inheritdoc}
         */
        public function parse(TokenStream $stream, Token $token)
        {
            if ($stream->nextIf('include') && $stream->expect('(')) {
                return sprintf("\$_defined = array%s; echo(\$this->render(\$_defined[0], array_merge(get_defined_vars(), isset(\$_defined[1]) ? \$_defined[1] : [])))", $this->parser->parseExpression());
            }
        }
    }
