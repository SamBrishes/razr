<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Loader/ChainLoader.php
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
    namespace Harx\Loader;

    use Harx\Exception\RuntimeException;


    class ChainLoader implements LoaderInterface {
        /*
         |  LOADER INSTANCEs
         |  @type   array
         */
        protected $loaders = [ ];

        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  array   An array with all loader instances.
         */
        public function __construct(array $loaders = []) {
            foreach($loaders AS $loader) {
                $this->addLoader($loader);
            }
        }

        /*
         |  ADD A LOADER INSTANCE
         |  @since  0.1.0
         |
         |  @param  object  The LoaderInterface implementing instance.
         |
         |  @return void
         */
        public function addLoader(LoaderInterface $loader) {
            $this->loader[] = $loader;
        }

        /*
         |  GET TEMPLATE SOURCE CODE
         |  @since  0.1.0
         |
         |  @param  string  The template name as string.
         |
         |  @return string  The respective source code of the template.
         */
        public function getSource(string $name): string {
            foreach($this->loaders as $loader) {
                try {
                    return $loader->getSource($name);
                } catch (RuntimeException $e) { }
            }
            throw new RuntimeException(sprintf('Template "%s" is not defined (%s).', $name));
        }

        /*
         |  GET TEMPLATE CACHE KEY
         |  @since  0.1.0
         |
         |  @param  string  The template name as string.
         |
         |  @return string  The cache key of the passed template name.
         */
        public function getCacheKey(string $name): string {
            foreach($this->loaders as $loader) {
                try {
                    return $loader->getCacheKey($name);
                } catch (RuntimeException $e) { }
            }
            throw new RuntimeException(sprintf('Template "%s" is not defined (%s).', $name));
        }

        /*
         |  CHECK IF TEMPLATE IS STILL FRESH
         |  @since  0.1.0
         |
         |  @param  string  The template name as string.
         |  @param  int     The timestamp to compare with.
         |
         |  @return bool    TRUE if the template is still fresh, FALSE if not.
         */
        public function isFresh(string $name, int $time): bool {
            foreach ($this->loaders as $loader) {
                try {
                    return $loader->isFresh($name, $time);
                } catch (RuntimeException $e) { }
            }
            return false;
        }
    }
