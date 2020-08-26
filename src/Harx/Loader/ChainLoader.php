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

    class ChainLoader implements LoaderInterface
    {
        protected $loaders = array();

        /**
         * Constructor.
         *
         * @param LoaderInterface[] $loaders
         */
        public function __construct(array $loaders = array())
        {
            foreach ($loaders as $loader) {
                $this->addLoader($loader);
            }
        }

        /**
         * Adds a loader instance.
         *
         * @param LoaderInterface $loader
         */
        public function addLoader(LoaderInterface $loader)
        {
            $this->loaders[] = $loader;
        }

        /**
         * {@inheritdoc}
         */
        public function getSource($name)
        {
            foreach ($this->loaders as $loader) {
                try {
                    return $loader->getSource($name);
                } catch (RuntimeException $e) {}
            }

            throw new RuntimeException(sprintf('Template "%s" is not defined (%s).', $name));
        }

        /**
         * {@inheritdoc}
         */
        public function getCacheKey($name)
        {
            foreach ($this->loaders as $loader) {
                try {
                    return $loader->getCacheKey($name);
                } catch (RuntimeException $e) {}
            }

            throw new RuntimeException(sprintf('Template "%s" is not defined (%s).', $name));
        }

        /**
         * {@inheritdoc}
         */
        public function isFresh($name, $time)
        {
            foreach ($this->loaders as $loader) {
                try {
                    return $loader->isFresh($name, $time);
                } catch (RuntimeException $e) {}
            }

            return false;
        }
    }
