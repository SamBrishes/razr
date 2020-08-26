<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Token.php
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

    /* Main Token Handler */
    class Token {
        /*
         |  RETURN TOKEN NAME
         |  @since  0.1.0
         |
         |  @param  int     The token integer value.
         |
         |  @return string  The token (constant) name.
         */
        static public function getName(int $type): string {
            if($type === T_PUNCTUATION) {
                return "T_PUNCTUATION";
            }
            return token_name($type);
        }


        /*
         |  TOKEN TYPE
         |  @type   int
         */
        protected $type;

        /*
         |  TOKEN VALUE
         |  @type   string
         */
        protected $value;

        /*
         |  TOKEN LINE NUMBER
         |  @type   int
         */
        protected $line;


        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  int     The token type.
         |  @param  string  The token value.
         |  @param  int     The token line number.
         */
        public function __construct(int $type, string $value, int $line) {
            $this->type = $type;
            $this->value = $value;
            $this->line = $line;
        }

        /*
         |  MAGIC :: RETURN STRING WITH TOKEN DETAILS
         |  @since  0.1.0
         |
         |  @return string  The token details as string.
         */
        public function __toString() {
            return sprintf('%s (%s)', self::getName($this->type), $this->value);
        }

        /*
         |  TEST TOKEN FOR TYPE AND/OR VALUE
         |  @since  0.1.0
         |
         |  @param  multi   The single type as integer or @param2 to skip the
         |                  the type check.
         |  @param  multi   The single value as string, multiple as array or
         |                  null to check the type only.
         |
         |  @return bool    TRUE if the test match, FALSE if not.
         */
        public function test(/* array | string | int */ $type, /* array | string | null */ $value = null): bool {
            if($value === null && !is_int($type)) {
                $value = $type;
                $type = $this->type;
            }
            if($this->type !== $type) {
                return false;
            }
            return $value === null || $this->value === $value || (is_array($value) && in_array($this->value, $value));
        }
    }
