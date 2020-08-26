<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Directive/FunctionDirective.php
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

    class FunctionDirective extends Directive
    {
        protected $function;
        protected $escape;

        /**
         * Constructor.
         *
         * @param string   $name
         * @param callable $function
         * @param bool     $escape
         */
        public function __construct($name, $function, $escape = false)
        {
            $this->name     = $name;
            $this->function = $function;
            $this->escape   = $escape;
        }

        /**
         * Calls the function with an array of arguments.
         *
         * @param  array $args
         * @return mixed
         */
        public function call(array $args = array())
        {
            return call_user_func_array($this->function, $args);
        }

        /**
         * @{inheritdoc}
         */
        public function parse(TokenStream $stream, Token $token)
        {
            if ($stream->nextIf($this->name)) {

                $out = sprintf("\$this->getDirective('%s')->call(%s)", $this->name, $stream->test('(') ? 'array' . $this->parser->parseExpression() : '');

                if ($this->escape) {
                    $out = sprintf("\$this->escape(%s)", $out);
                }

                return sprintf("echo(%s)", $out);
            }
        }
    }
