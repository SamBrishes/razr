<?php
/*
 |  HARX    HTML annotated rendering extension
 |  @file       ./Directive/ControlDirective.php
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

    use Harx\Token;
    use Harx\TokenStream;


    class ControlDirective extends Directive {
        /*
         |  START CONTROL TOKENs
         |  @type   array
         */
        protected $control;

        /*
         |  END CONTROL TOKENs
         |  @type   array
         */
        protected $controlEnd;

        /*
         |  ALETERNATIVE CONTROL MAPPINGs
         |  @type   array
         */
        protected $controlAlt;

        /*
         |  CONSTRUCTOR
         |  @since  0.1.0
         */
        public function __construct() {
            $this->name = 'control';
            $this->control = [T_FOR, T_FOREACH, T_IF, T_ELSEIF, T_ELSE, T_WHILE];
            $this->controlEnd = [T_ENDFOR, T_ENDFOREACH, T_ENDIF, T_ENDWHILE];
            $this->controlAlt = [
                "elif"      => [T_ELSEIF, "elseif"],
                "each"      => [T_FOREACH, "foreach"],
                "endeach"   => [T_ENDFOREACH, "endforeach"]
            ];
        }

        /*
         |  PARSE DIRECTIVE
         |  @since  0.1.0
         |
         |  @param  object  The token stream instance.
         |  @param  object  The token instance.
         |
         |  @return string  The string directive representation or null.
         */
        public function parse(TokenStream $stream, Token $token): ?String {
            if(array_key_exists($token->value, $this->controlAlt)) {
                $token->type = $this->controlAlt[$token->value][0];
                $token->value = $this->controlAlt[$token->value][1];
            }
            $control = in_array($token->type, $this->control);

            // Get Return Value
            if($control || in_array($token->type, $this->controlEnd)) {
                $return = '';

                while(!$stream->test(T_CLOSE_TAG)) {
                    if((!$control || $token->type === T_ELSE) && !empty($return)) {
                        $this->parser->parseExpression();
                        continue;
                    }
                    $return .= $this->parser->parseExpression();
                }

                // Remove ( )
                if($control) {
                    $return .= ':';
                }
                return $return;
            }
            return null;
        }
    }
