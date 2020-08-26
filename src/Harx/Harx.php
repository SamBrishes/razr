<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Harx.php
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

    use Harx\Directive\Directive;
    use Harx\Directive\DirectiveInterface;
    use Harx\Exception\RuntimeException;
    use Harx\Extension\CoreExtension;
    use Harx\Extension\ExtensionInterface;
    use Harx\Loader\LoaderInterface;
    use Harx\Storage\FileStorage;
    use Harx\Storage\Storage;
    use Harx\Storage\StringStorage;


    /* Main Class */
    class Harx {
        const VERSION = '0.1.0';
        const STATUS = 'Alpha';

        const ANY_CALL    = 'any';
        const ARRAY_CALL  = 'array';
        const METHOD_CALL = 'method';

        const DEFAULTS = [
            "cache"         => true,        // Enable Caching
            "cache_path"    => "~/",        // Caching Path ~/ points to the template directory
            "charset"       => "UTF-8",     // Used Charset for Escaping
        ];


        /*
         |  LOADER INSTANCE
         |  @type   LoaderInterface
         */
        public $loader;

        /*
         |  LEXER INSTANCE
         |  @type   Lexer
         */
        public $lexer;

        /*
         |  PARSER INSTANCE
         |  @type   Parser
         */
        public $parser;

        /*
         |  CURRENT PART
         |  @type   string
         */
        public $current;

        /*
         |  PARENT PARTs
         |  @type   array
         */
        public $parents = [ ];

        /*
         |  GLOBAL VARIABLEs
         |  @type   array
         */
        public $globals = [ ];

        /*
         |  GLOBAL DIRECTIVES
         |  @type   array
         */
        public $directives = [ ];

        /*
         |  GLOBAL FUNCTIONs
         |  @type   array
         */
        public $functions = [ ];

        /*
         |  GLOBAL EXTENSIONS
         |  @type   array
         */
        public $extensions = [ ];

        /*
         |  CACHE ARRAY
         |  @type   array
         */
        protected $cache = [ ];

        /*
         |  CURRENT CONFIGURATION
         |  @type   array | object
         */
        protected $config = [ ];

        /*
         |  IS ALREADY INITIALIZED
         |  @type   bool
         */
        private $init = false;

        /*
         |  INSTANCE TEMPLATE
         |  @type   string
         */
        private $template;

        /*
         |  INSTANCE PARAMETERs
         |  @type   array
         */
        private $parameters;

        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  object  The LoaderInterface instance.
         |  @param  array   An array with additional configurations (see DEFAULTS above).
         */
        public function __construct(LoaderInterface $loader, array $config = []) {
            $this->loader = $loader;
            $this->lexer = new Lexer($this);
            $this->parser = new Parser($this);
            $this->config = (object) array_merge(self::DEFAULTS, $config);
            $this->addExtension(new CoreExtension());
        }

        /*
         |  SET GLOBAL VARIABLE
         |  @since  0.1.0
         |
         |  @param  string  The global variable key.
         |  @param  multi   The global variable value.
         |
         |  @return object  The Harx instance itself.
         */
        public function setGlobal(string $key, /* any */ $value): Harx {
            $this->globals[$key] = $value;
            return $this;
        }

        /*
         |  SET GLOBAL DIRECTIVE
         |  @since  0.1.0
         |
         |  @param  object  The DirectiveInterface instance.
         |
         |  @return object  The Harx instance itself.
         */
        public function setDirective(DirectiveInterface $directive): Harx {
            if($this->init) {
                throw new RuntimeException(sprintf("The Harx instance has already been initialized, unable to set the '%s' directive.", $directive->getName()));
            }

            // Set & Return
            $directive->setEngine($this);
            $this->directives[$directive->getName()] = $directive;
            return $this;
        }

        /*
         |  SET GLOBAL FUNCTION
         |  @since  0.1.0
         |
         |  @param  string  The unique function name as string.
         |  @param  callb.  The callable function.
         |
         |  @return object  The Harx instance itself.
         */
        public function setFunction(string $name, callable $function): Harx {
            if($this->init) {
                throw new RuntimeException(sprintf("The Harx instance has already been initialized, unable to set the '%s' function.", $name));
            }

            // Set & Return
            $this->functions[$name] = $function;
            return $this;
        }

        /*
         |  SET GLOBAL EXGTENSION
         |  @since  0.1.0
         |
         |  @param  object  The ExtensionInterface instance.
         |
         |  @return object  The Harx instance itself.
         */
        public function setExtension(ExtensionInterface $extension) {
            if($this->init) {
                throw new RuntimeException(sprintf("The Harx instance has already been initialized, unable to set the '%s' extension.", $extension->getName()));
            }

            // Set & Return
            $this->extensions[$extension->getName()] = $extensions;
            return $this;
        }

        /*
         |  GET VALUE FROM OBJECT / ARRAY / CONSTRUCT
         |  @since  0.1.0
         |
         |  @param  name    The property / attribute key.
         |  @param  multi   The target object or array.
         |  @param  array   Some additional arguments for constructs / classes.
         |  @param  string  The desired type to handle.
         |
         |  @return multi   The respective attribute value or NULL if the key does not exist.
         */
        public function getAttribute(string $key, /* any */ $object, array $args = [], string $type = self::ANY_CALL)/*: any */ {
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

            // Method Handler
            if(($class = get_class($object)) === false) {
                return null;
            }

            // Get Method
            if(method_exists([$class, $key]) || method_exists([$class, "__call"])) {
                $callable = [$class, $key];
            } else if(method_exists([$class, "get$key"])) {
                $callable = [$class, "get$key"];
            } else if(method_exists([$class, "is$key"])) {
                $callable = [$class, "is$key"];
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
         |  @return multi   The respective returning value from the function, or null if the
         |                  function does not exist.
         */
        public function applyFunction(string $key, array $args = [])/*: any */ {
            if(!array_key_exists($name, $this->functions)) {
                return null;
            }
            return call_user_func_array($this->functions[$name], $args);
        }

        /*
         |  EXTEND A TEMPLATE WITH ANOTHER ONE
         |  @since  0.1.0
         |
         |  @param  string  The template as STRING.
         |
         |  @return object  The Harx instance itself.
         */
        public function extend(string $template): Harx {
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
                throw new RuntimeException("The template cannot be rendered.");
            }

            // Evalulate Parents
            if($this->parents[$key]) {
                $content = $this->render($this->parents[$key], $params);
            }
            return $content;
        }

        /*
         |  EVALUATE A TEMPLATE
         |
         |  @param  object  The template Storage instance.
         |  @param  array   The global parameters.
         |
         |  @return string  The evaluated template on success, null otherwise.
         */
        protected function evaluate(Storage $template, array $params = []): ?string {
            $this->template = $template;
            $this->parameters = $params;
            unset($template, $params);

            extract($this->parameters, EXTR_SKIP);
            $this->parameters = null;

            // File Storage
            if($this->template instanceof FileStorage) {
                ob_start();
                require $this->template;
                $this->template = null;
                return ob_get_clean();

            // String Storage
            } else if($this->template instanceof StringStorage) {
                ob_start();
                eval('; ?>' . $this->template . '<?php ;');
                $this->template = null;
                return ob_get_clean();
            }

            // Not Found
            return null;
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
            $tokens = $this->lexer->tokenize($source, $filename);
            $source = $this->parser->parse($tokens, $filename);
            return $source;
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
            if(!$this->init) {
                $this->initialize();
            }
            if(isset($this->cache[$name])) {
                return $this->cache[$name];
            }

            $cache = $this->cache_path? sprintf('%s/%s.cache', $this->config->cache_path], sha1($name)): null;
            if(!$cache) {
                $storage = new StringStorage($this->compile($his->loader->getSource($name), $name));
            } else {
                if(!is_file($cache) || !$this->isTemplateFresh($name, filemtime($cache))) {
                    $this->writeCacheFile($cache, $this->compile($this->loader->getSource($name), $name));
                }
                $storage = new FileStorage($cache);
            }
            return $this->cache[$name] = $storage;
        }

        /*
         |  INITIALIZE THE EXTENSIONS
         |  @since  0.1.0
         |
         |  @return void
         */
        protected function initialize(): void {
            foreach($this->extensions AS $ext) {
                $extension->initialize($this);
            }
            $this->init = true;
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
        protected function writeCacheFile(stirng $file, string $content): void {
            $dir = dirname($file);

            if(!is_dir($dir)) {
                if(@mkdir($dir, 0777, true) === false || !is_dir($dir)) {
                    throw new RuntimeException("Unable to create the cache directory ($dir).");
                }
            } else if(!is_writable($dir)) {
                throw new RuntimeException("Unable to write in the cache directory ($dir).");
            }

            // Write
            if(!file_put_contents($file, $content)) {
                throw new RuntimeException("Unable to write cache file ($file).");
            }
        }

        /*
         |  CHECK IF TEMPLATE IS FRESH
         |  @since  0.1.0
         |
         |  @param  string  The template name.
         |  @param  int     The creation / last modified time int.
         */
        protected function isTemplateFresh(string $name, int $time): bool {
            foreach($this->extensions AS $extension) {
                $r = new \ReflectionObject($extension);
                if(filemtime($r->getFilename() > $time)) {
                    return false;
                }
            }
            return $this->loader->isFresh($name, $time)? true: false;
        }
    }
