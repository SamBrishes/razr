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


    class FunctionDirective extends Directive {
        /*
         |  CALLABLE FUNCTION
         |  @type   callback
         */
        protected $function;

        /*
         |  ESCAPE RESULT
         |  @type   bool
         */
        protected $escape;

        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  string  The directive name.
         |  @param  callb.  The callback function.
         |  @param  bool    TRUE to escape the output, FALSE to do it not.
         */
        public function __construct(string $name, callable $function, bool $escape = false) {
            $this->name = $name;
            $this->function = $function;
            $this->escape = $escape;
        }

        /*
         |  CALL FUNCTION
         |  @since  0.1.0
         |
         |  @param  array   Additional argumnets to call the function.
         |
         |  @return multi   The respective returning value from the set function.
         */
        public function call(array $args = [])/*: any */ {
            return call_user_func_array($this->function, $args);
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
            if ($stream->nextIf($this->name)) {
                $return = sprintf("\$this->getDirective('%s')->call(%s)", $this->name, $stream->test('(') ? 'array' . $this->parser->parseExpression() : '');

                if($this->escape) {
                    $return = sprintf("\$this->escape(%s)", $out);
                }
                return sprintf("echo(%s)", $return);
            }
            return null;
        }
    }
