<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Larx.php
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
    namespace Larx;


    class Larx {
        const VERSION = "0.1.0";
        const STATUS = "Alpha";
        const DEFAULTS = [
            "cache"         => true,        // Enable Caching
            "cache_path"    => "~/",        // Caching Path ~/ points to the template directory
            "charset"       => "UTF-8",     // Used Charset for Escaping
        ];

        const TYPE_GLOBAL = "global";
        const TYPE_DIRECTIVE = "directive";
        const TYPE_FUNCTION = "function";
        const TYPE_TAG = "tag";
        
        const STATE_DATA = 0;
        const STATE_OUTPUT = 1;
        const STATE_DIRECTIVE = 2;
        const REGEX_CHAR = '/@{2}|@(?=\(|[a-zA-Z_])/s';
        const REGEX_START = '/\(|([a-zA-Z_][a-zA-Z0-9_]*)(\s*\()?/A';
        const REGEX_STRING = '/"([^#"\\\\]*(?:\\\\.[^#"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As';


        /*
         |  INSTANCE CONFIGURATION
         |  @type   array | object
         */
        public $config = [ ];

        /*
         |  INSTANCE PATHs
         |  @type   array
         */
        public $paths = [ ];

        /*
         |  INSTANCE GLOBALs
         |  @type   array
         */
        public $globals = [ ];

        /*
         |  INSTANCE DIRECTIVES
         |  @type   array
         */
        public $directives = [ ];

        /*
         |  INSTANCE FUNCTIONS
         |  @type   array
         */
        public $functions = [ ];

        /*
         |  INSTANCE TAGS
         |  @type   array
         */
        public $tags = [ ];

        /*
         |  CURRENT PART
         |  @type   string
         */
        public $current;

        /*
         |  CURRENT PARENT
         |  @type   array
         */
        public $parents = [ ];

        /*
         |  CURRENT TOKENs
         |  @type   array
         */
        public $tokens = [ ];


        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  array   An array with template file paths or NULL to set no path at all.
         |  @param  array   An array with additional configurations (see DEFAULTS above).
         |  @param  bool    TRUE to skip the default init process, FALSE to keep it.
         */
        public function __construct(?array $data = null, array $config = [], bool $skipInit = false) {
            $this->config = (object) array_merge(self::DEFAULTS, $config);

            // Init Data
            if($data !== null) {
                foreach($data AS $path) {
                    if(realpath($path) === false) {
                        continue;
                    }
                    $this->paths[] = $path;
                }
            }
            if(!$skipInit) {
                $this->init();
            }
        }

        /*
         |  INIT LARX
         |  @since  0.1.0
         |
         |  @return void
         */
        protected function init(): void {
            $this->set(self::TYPE_DIRECTIVE, "block", function() {

            });
            $this->set(self::TYPE_DIRECTIVE, "control", function() {

            });
            $this->set(self::TYPE_DIRECTIVE, "extend", function() {

            });
            $this->set(self::TYPE_DIRECTIVE, "include", function() {

            });
            $this->set(self::TYPE_DIRECTIVE, "raw", function() {

            });
            $this->set(self::TYPE_DIRECTIVE, "set", function() {

            });

            $this->set(self::TYPE_FUNCTION, "e", [$this, "escape"]);
            $this->set(self::TYPE_FUNCTION, "escape", [$this, "escape"]);
            $this->set(self::TYPE_FUNCTION, "block", [$this, "block"]);
            $this->set(self::TYPE_FUNCTION, "constant", [$this, "getAttribute"]);
            $this->set(self::TYPE_FUNCTION, "json", "json_encode");
            $this->set(self::TYPE_FUNCTION, "upper", "strtoupper");
            $this->set(self::TYPE_FUNCTION, "lower", "strtolower");
            $this->set(self::TYPE_FUNCTION, "format", "sprintf");
            $this->set(self::TYPE_FUNCTION, "replace", "strtr");
        }

        /*
         |  SET GLOBAL CONTENT
         |  @since  0.1.0
         |
         |  @param  string  The type you want to set, use the "TYPE_*" constants.
         |  @param  string  The key of the desired type.
         |  @param  mixed   The desired value of the set type.
         |
         |  @return object  The Larx instance itself.
         */
        public function set(string $type, string $key, /* mixed */ $value): Larx {
            if(!in_array($type, ["global", "directive", "function", "tag"])) {
                throw new \Exception("The passed type is unknown.");
            }

            // Set & Return
            $pool = &$this->{strtolower($type) . "s"};
            $pool[$key] = $value;
            return $this;
        }

        /*
         |  GET GLOBAL CONTENT
         |  @since  0.1.0
         |
         |  @param  string  The type you want to set, use the "TYPE_*" constants.
         |  @param  string  The key of the desired type.
         |
         |  @return mixed   The respective value or null if not found.
         */
        public function get(string $type, string $key)/* : mixed */ {
            if(!in_array($type, ["global", "directive", "function", "tag"])) {
                throw new \Exception("The passed type is unknown.");
            }
            return $this->{strtolower($type) . "s"}[$key] ?? null;
        }

        /*
         |  GET ATTRIBUTE
         |  @since  0.1.0
         |
         |  @param  name    The property / attribute key.
         |  @param  mixed   The target object or array.
         |  @param  array   Some additional arguments for constructs / classes.
         |  @param  string  The desired type to handle.
         |
         |  @return mixed   The respective attribute value or NULL if the key does not exist.
         */
        public function getAttribute(string $key, /* mixed */ $object, array $args = [], string $type = self::ANY_CALL)/*: mixed */ {
            if($type === self::ANY_CALL || $type === self::ARRAY_CALL) {
                $key = is_bool($key) || is_float($key)? (int) $key: $key;

                if((is_array($object) && array_key_exists($key, $object)) || ($object instanceof \ArrayAccess && isset($object[$key]))) {
                    return $object[$key];
                }
                if($type === self::ARRAY_CALL) {
                    return null;
                }
            }

            // Object Handler
            if(!is_object($object)) {
                return null;
            }

            // Check Property
            if($type === self::ANY_CALL && isset($object->$key)) {
                return $object->$key;
            }

            // Check Method
            if(($class = get_class($object)) === false) {
                return null;
            }

            // Get Method
            if(method_exists($object, $key) || method_exists($class, "__call")) {
                $callable = [$object, $key];
            } else if(method_exists($object, "get" . ucfirst($key))) {
                $callable = [$object, "get" . ucfirst($key)];
            } else {
                return null;
            }

            // Handle
            return call_user_func_array($callable, $args);
        }

        /*
         |  APPLIES A FUNCTION
         |  @since  0.1.0
         |
         |  @param  string  The desired function key to apply.
         |  @param  array   The additional arguments for the function call.
         |
         |  @return mixed   The respective returning value from the function, or null if the
         |                  function does not exist.
         */
        public function applyFunction(string $key, array $args = [])/*: mixed */ {
            if(!array_key_exists($key, $this->functions)) {
                return null;
            }
            return call_user_func_array($this->functions[$key], $args);
        }

        /*
         |  EXTEND A TEMPLATE WITH ANOTHER ONE
         |  @since  0.1.0
         |
         |  @param  string  The template as STRING.
         |
         |  @return object  The Harx instance itself.
         */
        public function extend(string $template): Larx {
            $this->parents[$this->current] = $template;
            return $this;
        }

        /*
         |  ESCAPE A STRING
         |  @since  0.1.0
         |
         |  @param  string  The string to escape.
         |
         |  @return string  The escaped string on success, null otherwise.
         */
        public function escape(string $value): ?string {
            if(!is_string($value) || is_numeric($value)) {
                return null;
            }
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, $this->config->charset, false);
        }

        /*
         |  RENDER A TEMPLATE
         |  @since  0.1.0
         |
         |  @param  string  The template key.
         |  @param  array   Additinal global variables.
         |
         |  @return string  The rendered template.
         */
        public function render(string $name, array $params = []): string {
            $storage = $this->load($name);
            $params = array_replace($this->globals, $params);

            // Store Template
            $this->current = $key = sha1(serialize($storage));
            $this->parents[$key] = null;

            // Evaluate Template
            if(($content = $this->evaluate($storage, $params)) === null) {
                throw new \Exception("The template cannot be rendered.");
            }

            // Evalulate Parents
            if($this->parents[$key]) {
                $content = $this->render($this->parents[$key], $params);
            }
            return $content;
        }

        /*
         |  RENDER AND PRINT A TEMPLATE
         |  @since  0.1.0
         |
         |  @param  string  The template key.
         |  @param  array   Additinal global variables.
         |
         |  @return string  The rendered template.
         */
        public function print(string $name, array $params = []): void {
            print($this->render($name, $params));
        }

        /*
         |  LOAD A TEMPLATE
         |  @since  0.1.0
         |
         |  @param  string  The template name.
         |
         |  @return object  The Storage instance.
         */
        protected function load(string $name): Storage {
            if($this->config->cache && $this->config->cache_path) {
                $cache = sprintf('%s/%s.cache', $this->config->cache_path, sha1($name));
            } else {
                $cache = null;
            }

            // Handle Storage
            if(!$cache) {
                foreach($this->paths AS $path) {
                    if(is_file($file = $path . DIRECTORY_SEPARATOR . $name)) {
                        break;
                    }
                }
                if(!isset($file) || !is_file($file)) {
                    throw new \Exception("The template file '$name' could not be found.");
                }
                $source = $this->compile(file_get_contents($file), $name);

                if($this->config->cache()) {
                    $this->writeCacheFile($cache, $source);
                }
            }
            return $source;
        }

        /*
         |  WRITE CACHE FILE
         |  @since  0.1.0
         |
         |  @param  string  The cache file name.
         |  @param  string  The cache file content.
         |
         |  @return void
         */
        protected function writeCacheFile(string $file, string $content): void {
            $dir = dirname($file);

            if(!is_dir($dir)) {
                if(@mkdir($dir, 0777, true) === false || !is_dir($dir)) {
                    throw new \Exception("Unable to create the cache directory ($dir).");
                }
            } else if(!is_writable($dir)) {
                throw new \Exception("Unable to write in the cache directory ($dir).");
            }

            // Write
            if(!file_put_contents($file, $content)) {
                throw new \Exception("Unable to write cache file ($file).");
            }
        }

        /*
         |  EVALUATE A TEMPLATE
         |  @since  0.1.0
         |
         |  @param  object  The template Storage instance.
         |  @param  array   The global parameters.
         |
         |  @return string  The evaluated template on success, null otherwise.
         */
        protected function evaluate(Storage $template, array $params = []): string {
            $this->template = $template;
            $this->parameters = $params;
            unset($template, $params);

            extract($this->parameters, EXTR_SKIP);
            $this->parameters = null;

            // Evaluate
            ob_start();
            eval('; ?>' . $this->template . '<?php ;');
            $this->template = null;
            return ob_get_clean();
        }

        /*
         |  COMPILE A TEMPLATE
         |  @since  0.1.0
         |
         |  @param  string  The source template.
         |  @param  string  The filename of the tremplate.
         |
         |  @return string  The parsed source template.
         */
        protected function compile(string $source, ?string $filename = null): string {
            $this->tokenize($source);
            return $this->parse();
        }

        /*
         |  TOKENIZE
         |  @since  0.1.0
         */
        protected function tokenize(string $code) {
            if(function_exists("mb_internal_encoding") && ((int) ini_get("mbstring.func_overload")) & 2) {
                $encoding = mb_internal_encoding();
                mb_internal_encoding("ASCII");
            }

            // Set initial data
            $code = str_replace(["\r\n", "\r"], "\n", $code); // Force Linux Line-Endings
            $source = "";
            $filename = $filename;
            $cursor = 0;
            $lineno = 1;
            $end = strlen($code);
            $tokens = [];
            $state = self::STATE_DATA;
            $states = [];
            $brackets = [];
            $position = -1;

            // Match Tokens
            preg_match_all(self::REGEX_CHAR, $code, $positions, PREG_OFFSET_CAPTURE);

            // Move Cursor
            while($cursor < $end) {
                switch($state) {
                    case self::STATE_DATA:
                        $this->lexData(); break;
                    case self::STATE_OUTPUT:
                        $this->lexOutput(); break;
                    case self::STATE_DIRECTIVE:
                        $this->lexDirective(); break;
                }
            }
            if($this->state !== self::STATE_DATA) {
                $this->addCode(" ?>");
                $this->popState();
            }

            // Brackets Check
            if(!empty($this->brackets)) {
                [$expect, $lineno] = array_pop($this->brackets);
                throw new SyntaxErrorException(sprintf('Unclosed "%s" at line %d in file %s', $expect, $lineno, $this->filename));
            }

            // Reset Encoding & Return
            if(isset($encoding)) {
                mb_internal_encoding($encoding);
            }
            $this->tokens = token_get_all($source);
        }

        /*
         |  PARSE
         |  @since  0.1.0
         */
        protected function parse() {

        }
    }
