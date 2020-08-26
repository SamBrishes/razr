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

    abstract class Directive implements DirectiveInterface
    {
        protected $name;
        protected $engine;
        protected $parser;

        /**
         * {@inheritdoc}
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @{inheritdoc}
         */
        public function setEngine(Harx $engine)
        {
            $this->engine = $engine;
            $this->parser = $engine->getParser();
        }
    }
