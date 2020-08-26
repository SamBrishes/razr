<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Extension/CoreExtension.php
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

    use Harx\Directive\BlockDirective;
    use Harx\Directive\ControlDirective;
    use Harx\Directive\ExtendDirective;
    use Harx\Directive\IncludeDirective;
    use Harx\Directive\RawDirective;
    use Harx\Directive\SetDirective;
    use Harx\Harx;
    use Harx\Exception\InvalidArgumentException;
    use Harx\Exception\RuntimeException;

    class CoreExtension implements ExtensionInterface
    {
        protected $blocks = array();
        protected $openBlocks = array();

        /**
         * {@inheritdoc}
         */
        public function getName()
        {
            return 'core';
        }

        /**
         * {@inheritdoc}
         */
        public function initialize(Harx $engine)
        {
            // directives
            $engine->addDirective(new BlockDirective);
            $engine->addDirective(new ControlDirective);
            $engine->addDirective(new ExtendDirective);
            $engine->addDirective(new IncludeDirective);
            $engine->addDirective(new RawDirective);
            $engine->addDirective(new SetDirective);

            // functions
            $engine->addFunction('e', array($engine, 'escape'));
            $engine->addFunction('escape', array($engine, 'escape'));
            $engine->addFunction('block', array($this, 'block'));
            $engine->addFunction('constant', array($this, 'getConstant'));
            $engine->addFunction('json', 'json_encode');
            $engine->addFunction('upper', 'strtoupper');
            $engine->addFunction('lower', 'strtolower');
            $engine->addFunction('format', 'sprintf');
            $engine->addFunction('replace', 'strtr');
        }

        /**
         * Gets or sets a block.
         *
         * @param  string $name
         * @param  mixed  $value
         * @return string
         */
        public function block($name, $value = null)
        {
            if ($value === null) {
                return isset($this->blocks[$name]) ? $this->blocks[$name] : null;
            }

            $this->blocks[$name] = $value;
        }

        /**
         * Starts a block.
         *
         * @param  string $name
         * @throws InvalidArgumentException
         */
        public function startBlock($name)
        {
            if (in_array($name, $this->openBlocks)) {
                throw new InvalidArgumentException(sprintf('A block "%s" is already started.', $name));
            }

            $this->openBlocks[] = $name;

            if (!isset($this->blocks[$name])) {
                $this->blocks[$name] = null;
            }

            ob_start();
            ob_implicit_flush(0);
        }

        /**
         * Stops a block.
         *
         * @throws RuntimeException
         * @return string
         */
        public function endBlock()
        {
            if (!$this->openBlocks) {
                throw new RuntimeException('No block started.');
            }

            $name  = array_pop($this->openBlocks);
            $value = ob_get_clean();

            if ($this->blocks[$name] === null) {
                $this->blocks[$name] = $value;
            }

            return $this->blocks[$name];
        }

        /**
         * Reset all blocks.
         *
         * @return void
         */
        public function resetBlocks()
        {
            $this->blocks = array();
            $this->openBlocks = array();
        }

        /**
         * Gets a constant from an object.
         *
         * @param  string $name
         * @param  object $object
         * @return mixed
         */
        public function getConstant($name, $object = null)
        {
            if ($object !== null) {
                $name = sprintf('%s::%s', get_class($object), $name);
            }

            return constant($name);
        }
    }
