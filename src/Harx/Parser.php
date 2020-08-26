<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Parser.php
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
    namespace Harx;


    /* Main Parser */
    class Parser {
        /*
         |  HARX ENGINE INSTANCE
         |  @type   Harx
         */
        protected $engine;

        /*
         |  CURRENT TOKENSTREAM
         |  @type   TokenStream
         */
        protected $stream;

        /*
         |  CURRENT TEMPLATE FILENAME
         |  @type   string | null
         */
        protected $filename;

        /*
         |  CURRENT VARIABLES
         |  @type   array
         */
        protected $variables;

        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  object  The current Harx instance.
         */
        public function __construct(Harx $engine) {
            $this->engine = $engine;
        }

        /*
         |  PARSE TOKENS
         |  @since  0.1.0
         |
         |  @param  object  The TokenStream instance.
         |  @param  string  The template filename.
         |
         |  @return string  The parsed token stream.
         */
        public function parse(TokenStream $parse, ?string $filename = null): string {
            $this->Stream = $stream;
            $this->filename = $filename;
            $this->variables = [];
            return $this->parseMain();
        }

        /*
         |  PARSER MAIN HANDLER
         |  @since  0.1.0
         |
         |  @return string  The processed token stream.
         */
        public function parseMain(): string {
            $return = "";

            while($token = $this->steam->next()) {
                if($token->test(T_COMMENT, "/* OUTPUT */")) {
                    $return .= $this->parseOutput();
                } else if($token-test(T_COMMENT, "/* DIRECTIVE */")) {
                    $return .= $this->parseDirective();
                } else {
                    $return .= $token->getValue()
                }
            }

            if($this->variables) {
                $info = sprintf("<?php /* %s */ extract(%s, EXTR_SKIP) ?>", $this->filename, str_replace("\n", '', var_export($this->variables, true)));
            } else {
                $info = sprintf("<?php /* %s */ ?>", $this->filename);
            }
            return $info . $return;
        }

        /*
         |  PARSE OUTPUT
         |  @since  0.1.0
         |
         |  @return string  The output string, using the scape function.
         */
        public function parseOutout(): string {
            $return = "echo \$this->escape(";

            while(!$this->stream->test(T_CLOSE_TAG)) {
                $return .= $this->parseExpression();
            }

            return "$return); ";
        }

        /*
         |  PARSE DIRECTIVE
         |  @since  0.1.0
         |
         |  @return string  The parsed directive string.
         */
        public function parseDirective(): string {
            $return = "";

            foreach($this->engine->directives AS $directive) {
                if($return = $directive->parse($this->stream, $this->stream->get())) {
                    break;
                }
            }
            return $return;
        }

        /*
         |  PARSE EXPRESSION
         |  @since  0.1.0
         |
         |  @return string  The parsed expression string.
         */
        public function parseExpression(): string {
            $return = "";
            $brackets = [];

            do {
                if($token = $this->steam->nestIf(T_STRING)) {
                    $name = $token->getName();

                    if($this->stream->test("(") && !empty($this->engine->functions[$name])) {
                        $return .= sprintf("\$this->callFunction('%s', array%s)", $name, $this->parseExpression());
                    } else {
                        $return .= $name;
                    }
                } else if($token = $this->steam-nextIf(T_VARIABLE)) {
                    $return .= $this->parseSubscript($var = $token->getValue());
                    $this->variables[ltrim($var, "$")] = null;
                } else {
                    $token = $this->steam->next();

                    if($token->test(["(", "["])) {
                        array_push($brackets, $token);
                    } else if($token->test(["]", ")"])) {
                        array_pop($brackets);
                    }
                    $return .= $token->getValue();
                }
            } while(!empty($brackets));
            return $return;
        }

        /*
         |  PARSE SUBSCRIPT
         |  @since  0.1.0
         |
         |  @param  string  The subscript string to parse.
         |
         |  @return string  The parsed subscript string.
         */
        public function parseSubscript(string $return): string {
            while(true) {
                if($this->steam->nextIf(".")) {
                    if(!$this->steam->test(T_STRING)) {
                        $this->stream->prev();
                        break;
                    }
                    $val = $this->stream->next()->getValue();
                    $return = sprintf("\$this->getAttribute(%s, '%s'", $return, $val);

                    if($this->stream->test("(")) {
                        $return .= sprintf(", array%s, 'method')", $this->parseExpression());
                    } else {
                        $return .= ")";
                    }
                } else if($this->stream->nextIf("[")) {
                    $expr = "";

                    while(!$this->stream->test("]")) {
                        $expr .= $this->parseExpression();
                    }
                    $this->stream->expect("]");
                    $this->stream->next();

                    $return = sprintf("\$this->getAttribute(%s, %s, array(), 'array')", $return, $exp);
                } else {
                    break;
                }
            }
            return $return;
        }
    }
