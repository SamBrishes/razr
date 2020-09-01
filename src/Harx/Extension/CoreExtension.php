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


    class CoreExtension implements ExtensionInterface {
        /*
         |  BLOCKS HOLDER
         |  @type   array
         */
        protected $blocks = [ ];

        /*
         |  OPEN BLOCKS HOLDER
         |  @type   array
         */
        protected $openBlocks = [ ];

        /*
         |  GET EXTENSION NAME
         |  @since  0.1.0
         |
         |  @return string  The extension name.
         */
        public function getName(): string {
            return 'core';
        }

        /*
         |  INITIALIZE EXTENSION
         |  @since  0.1.0
         |
         |  @param  object  The Harx instance.
         |
         |  @return void
         */
        public function initialize(Harx $engine): void {
            // Set Directives
            $engine->setDirective(new BlockDirective);
            $engine->setDirective(new ControlDirective);
            $engine->setDirective(new ExtendDirective);
            $engine->setDirective(new IncludeDirective);
            $engine->setDirective(new RawDirective);
            $engine->setDirective(new SetDirective);

            // Set Functions
            $engine->setFunction('e', [$engine, 'escape']);
            $engine->setFunction('escape', [$engine, 'escape']);
            $engine->setFunction('block', [$this, 'block']);
            $engine->setFunction('constant', [$this, 'getConstant']);
            $engine->setFunction('json', 'json_encode');
            $engine->setFunction('upper', 'strtoupper');
            $engine->setFunction('lower', 'strtolower');
            $engine->setFunction('format', 'sprintf');
            $engine->setFunction('replace', 'strtr');
        }

        /*
         |  GET OR SET A BLOCK
         |  @since  0.1.0
         |
         |  @param  string  The block name as string.
         |  @param  string  The block content or null to receive a block.
         |
         |  @return string  The block content, if no second parameter is passed, null otherwise.
         */
        public function block(string $name, ?string $value = null): ?string {
            if ($value === null) {
                return $this->blocks[$name] ?? null;
            }
            $this->blocks[$name] = $value;
            return null;
        }

        /*
         |  START A BLOCK
         |  @since  0.1.0
         |
         |  @param  string  The block name as string.
         |
         |  @return void
         */
        public function startBlock(string $name): void {
            if(in_array($name, $this->openBlocks)) {
                throw new InvalidArgumentException(sprintf('A block "%s" is already started.', $name));
            }

            $this->openBlocks[] = $name;
            if(!isset($this->blocks[$name])) {
                $this->blocks[$name] = null;
            }

            ob_start();
            ob_implicit_flush(0);
        }

        /*
         |  END A BLOCK
         |  @since  0.1.0
         |
         |  @return string  The while block content.
         */
        public function endBlock(): string {
            if(!$this->openBlocks) {
                throw new RuntimeException('No block started.');
            }

            $name  = array_pop($this->openBlocks);
            $value = ob_get_clean();
            if ($this->blocks[$name] === null) {
                $this->blocks[$name] = $value;
            }
            return $this->blocks[$name];
        }

        /*
         |  RESET ALL BLOCKS
         |  @since  0.1.0
         |
         |  @return void
         */
        public function resetBlocks(): void {
            $this->blocks = [ ];
            $this->openBlocks = [ ];
        }

        /*
         |  GET CONSTANT FROM OBJECT
         |  @since  0.1.0
         |
         |  @param  string  The constant name.
         |  @param  object  The object to get the constant from, null to get a global constant.
         |
         |  @return multi   The constant value.
         */
        public function getConstant(string $name, ?object $object = null)/*: any */ {
            if ($object !== null) {
                $name = sprintf('%s::%s', get_class($object), $name);
            }
            return constant($name);
        }
    }
