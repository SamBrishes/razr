<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Loader/SFilesystemLoader.php
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


    class FilesystemLoader implements LoaderInterface {
        /*
         |  CHECK IF FILE IS AN EXISTING ABSOLUTE PATH
         |  @since  0.1.0
         |
         |  @param  string  The file path to check.
         |
         |  @return bool    TRUE if the file is within an absolute path, FALSE if not.
         */
        static protected function isAbsolutePath(string $file): bool {
            if(strlen($file) >= 3 && ctype_alpha($file[0]) && $file[1] === ":" && ($file[2] === "\\" || $file[2] === "/")) {
                return true;
            }
            return $file[0] === "/" || $file[0] === "\\" || realpath($file) === $file || null !== parse_url($file, PHP_URL_SCHEME);
        }


        /*
         |  TEMPLATE PATHs
         |  @since  array
         */
        protected $paths;

        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  array   The template paths.
        */
        public function __construct(array $paths = [ ]) {
            $this->paths = $paths;
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
            return file_get_contents($this->findTemplate($name));
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
            return $this->findTemplate($name);
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
            return filemtime($this->findTemplate($name)) <= $time;
        }

        /*
         |  FIND TEMPLATE
         |  @since  0.1.0
         |
         |  @param  string  The template name to search as string.
         |
         |  @return string  The template path.
         */
        protected function findTemplate(string $name): string {
            if(self::isAbsolutePath($name) && is_file($name)) {
                return $name;
            }

            $name = ltrim(strtr($name, '\\', '/'), '/');
            foreach($this->paths AS $path) {
                if(is_file($file = $path . DIRECTORY_SEPARATOR . $name)) {
                    return $file;
                }
            }
            throw new RuntimeException(sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths)));
        }
    }
