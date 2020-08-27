<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./TokenStream.php
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

    use Harx\Exception\SyntaxErrorException;


    /* Main TokenStream Handler */
    class TokenStream {
        /*
         |  STORED TOKENs
         |  @type   array
         */
        protected $tokens;
        /*
         |  CURRENT TOKEN POSITION
         |  @type   int
         */
        protected $current = 0;
        /*
         |  PEEKED TOKEN
         |  @type   int
         */
        protected $peek = 0;


        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         |
         |  @param  array   The tokens to store.
         */
        public function __construct(array $tokens) {
            $line = 0;

            if(!defined("T_PUNCTUATION")) {
                define("T_PUNCTUATION", -1);
            }

            foreach($tokens AS $token) {
                if(is_array($token)) {
                    $this->tokens[] = new Token($token[0], $token[1], $line = $token[2]);
                } else if(is_string($token)) {
                    $this->tokens[] = new Token(T_PUNCTUATION, $token, $line);
                }
            }
        }

        /*
         |  MAGIC :: PRINT TOKEN STREAM DETAILS
         |  @since  0.1.0
         |
         |  @return string  The token stream details.
         */
        public function __toString(): string {
            return implode("\n", $this->tokens);
        }

        /*
         |  GET A TOKEN
         |  @since  0.1.0
         |
         |  @param  int     The token position number.
         |
         |  @return object  The Token instance or null if not found.
         */
        public function get(int $number = 0): ?Token {
            return $this->tokens[$this->current + $number] ?? null;
        }

        /*
         |  GET NEXT TOKEN
         |  @since  0.1.0
         |
         |  @return object  The next Token instance or null if not available.
         */
        public function next(): ?Token {
            $this->peek = 0;
            return $this->tokens[$this->current++] ?? null;
        }

        /*
         |  GET PREVIOUS TOKEN
         |  @since  0.1.0
         |
         |  @return object  The previous Token instance or null if not available.
         */
        public function prev(): ?Token {
            return $this->tokens[$this->current--] ?? null;
        }

        /*
         |  GET NEXT TOKEN ON CONDITION
         |  @since  0.1.0
         |
         |  @param  multi   The single type as integer or @param2 to skip the
         |                  the type check.
         |  @param  multi   The single value as string, multiple as array or
         |                  null to check the type only.
         |
         |  @return object  The next Token instance or null if condition does not match.
         */
        public function nextIf(/* array | string | int */ $type, /* array | string | null */ $value = null): ?Token {
            if($this->test($type, $value)) {
                return $this->next();
            }
            return null;
        }

        /*
         |  TEST CURRENT TOKEN
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
            return $this->tokens[$this->current]->test($type, $value);
        }

        /*
         |  TEST CURRENT TOKEN OR THROW EXCEPTION
         |  @since  0.1.0
         |
         |  @param  multi   The single type as integer or @param2 to skip the
         |                  the type check.
         |  @param  multi   The single value as string, multiple as array or
         |                  null to check the type only.
         |  @param  string  A custom message to print.
         |
         |  @return bool    TRUE if the test match, FALSE if not.
         */
        public function expect(/* array | string | int */ $type, /* array | string | null */ $value = null, string $msg = ""): Token {
            $token = $this->tokens[$this->current];
            if (!$token->test($type, $value)) {
                throw new SyntaxErrorException(sprintf('%sUnexpected token "%s" of value "%s" ("%s" expected%s) on line %s', $msg, $token, $token->getValue(), Token::getName($type), $value ? sprintf(' with value "%s"', $value) : '', $token->getLine()));
            }
            return $token;
        }

        /*
         |  RESET PEEK POINTER
         |  @since  0.1.0
         |
         |  @return void
         */
        public function resetPeek(): void {
            $this->peek = 0;
        }

        /*
         |  MOVES PEEK POINTER FORWARD
         |  @since  0.1.0
         |
         |  @return object  The token instance if available, null otherwise.
         */
        public function peek(): ?Token {
            return $this->tokens[$this->current + ++$this->peek] ?? null;
        }

        /*
         |  PEEK UNTIL MATCHED TOKEN IS FOUND
         |  @since  0.1.0
         |
         |  @return object  The token instance if available, null otherwise.
         */
        public function peekUntil(/* array | string | int */ $type, /* array | string | null */ $value = null): ?Token {
            while($token = $this->peek() && !$token->test($type, $value)) {
                $token = null;
            }
            return $token;
        }

        /*
         |  PEEK NEXT TOKEN AND RESET PEEK POINTER
         |  @since  0.1.0
         |
         |  @return object  The token instance if available, null otherwise.
         */
        public function glimpse() {
            $peek = $this->peek();
            $this->peek = 0;
            return $peek;
        }
    }
