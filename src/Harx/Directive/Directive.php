<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Directive/Directive.php
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


    abstract class Directive {
        /*
         |  DIRECTIVE NAME
         |  @type   string
         */
        protected $name;

        /*
         |  HARX ENGINE INSTANCE
         |  @type   Harx
         */
        protected $engine;

        /*
         |  PARSER INSTANCE
         |  @type   Parser
         */
        protected $parser;

        /*
         |  GET NAME
         |  @since  0.1.0
         |
         |  @return string  The directive name as string.
         */
        public function getName(): string {
            return $this->name;
        }

        /*
         |  SET HARX ENGINE AND PARSER INSTANCES
         |  @since  0.1.0
         |
         |  @param  object  The Harx engine instance.
         |
         |  @return void
         */
        public function setEngine(Harx $engine): void {
            $this->engine = $engine;
            $this->parser = $engine->parser;
        }
    }
