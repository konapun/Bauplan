<?php
namespace Bauplan\Language\Directive;

use Bauplan\Language\Lexer as Lexer;
use Bauplan\Language\Directive\DirectiveToken as Token;

/*
 *
 *
 * TODO: Handle escape character
 */
class DirectiveLexer extends Lexer {

  function __construct() {}

  protected function tokens() {
    return array(
      '(\|)'                   => Token::T_PIPE, // |
      '(,)'                    => Token::T_COMMA, // ,
      '(:)'                    => Token::T_COLON, // :
      '"([^"]*)"'              => Token::T_STRING, // "directive string"
      '([-+]?[0-9]*\.?[0-9]+)' => Token::T_NUMBER, // -1.234
      '(true)'                 => Token::T_TRUE, // true
      '(false)'                => Token::T_FALSE, // false
      '(\w+)'                  => Token::T_KEY,
      '(\s+)'                  => Lexer::SKIP // whitespace
    );
  }
}
?>
