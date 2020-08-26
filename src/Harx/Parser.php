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


        /**
         * Parse main.
         *
         * @return string
         */
        public function parseMain()
        {
            $out = '';

            while ($token = $this->stream->next()) {
                if ($token->test(T_COMMENT, '/* OUTPUT */')) {
                    $out .= $this->parseOutput();
                } elseif ($token->test(T_COMMENT, '/* DIRECTIVE */')) {
                    $out .= $this->parseDirective();
                } else {
                    $out .= $token->getValue();
                }
            }

            if ($this->variables) {
                $info = sprintf('<?php /* %s */ extract(%s, EXTR_SKIP) ?>', $this->filename, str_replace("\n", '', var_export($this->variables, true)));
            } else {
                $info = sprintf('<?php /* %s */ ?>', $this->filename);
            }

            return $info.$out;
        }

        /**
         * Parse output.
         *
         * @return string
         */
        public function parseOutput()
        {
            $out = "echo \$this->escape(";

            while (!$this->stream->test(T_CLOSE_TAG)) {
                $out .= $this->parseExpression();
            }

            return "$out) ";
        }

        /**
         * Parse directive.
         *
         * @return string
         */
        public function parseDirective()
        {
            $out = '';

            foreach ($this->engine->getDirectives() as $directive) {
                if ($out = $directive->parse($this->stream, $this->stream->get())) {
                    break;
                }
            }

            return $out;
        }

        /**
         * Parse expression.
         *
         * @return string
         */
        public function parseExpression()
        {
            $out = '';
            $brackets = array();

            do {

                if ($token = $this->stream->nextIf(T_STRING)) {

                    $name = $token->getValue();

                    if ($this->stream->test('(') && $this->engine->getFunction($name)) {
                        $out .= sprintf("\$this->callFunction('%s', array%s)", $name, $this->parseExpression());
                    } else {
                        $out .= $name;
                    }

                } elseif ($token = $this->stream->nextIf(T_VARIABLE)) {

                    $out .= $this->parseSubscript($var = $token->getValue());
                    $this->variables[ltrim($var, '$')] = null;

                } else {

                    $token = $this->stream->next();

                    if ($token->test(array('(', '['))) {
                        array_push($brackets, $token);
                    } elseif ($token->test(array(')', ']'))) {
                        array_pop($brackets);
                    }

                    $out .= $token->getValue();
                }

            } while (!empty($brackets));

            return $out;
        }

        /**
         * Parse subscript.
         *
         * @param  string $out
         * @return string
         */
        public function parseSubscript($out)
        {
            while (true) {
                if ($this->stream->nextIf('.')) {

                    if (!$this->stream->test(T_STRING)) {
                        $this->stream->prev();
                        break;
                    }

                    $val = $this->stream->next()->getValue();
                    $out = sprintf("\$this->getAttribute(%s, '%s'", $out, $val);

                    if ($this->stream->test('(')) {
                        $out .= sprintf(", array%s, 'method')", $this->parseExpression());
                    } else {
                        $out .= ")";
                    }

                } elseif ($this->stream->nextIf('[')) {

                    $exp = '';

                    while (!$this->stream->test(']')) {
                        $exp .= $this->parseExpression();
                    }

                    $this->stream->expect(']');
                    $this->stream->next();

                    $out = sprintf("\$this->getAttribute(%s, %s, array(), 'array')", $out, $exp);

                } else {
                    break;
                }
            }

            return $out;
        }
    }
