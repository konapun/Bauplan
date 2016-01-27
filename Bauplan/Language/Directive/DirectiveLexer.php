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
      '/^;;(.*)/'                     => Lexer::SKIP, // ;; inline comment
      '/^(\|)/'                       => Token::T_PIPE, // |
      '/^(,)/'                        => Token::T_COMMA, // ,
      '/^\[([^\]]*)\]/'               => Token::T_COMMA, // [anything] - functions as a syntactic comma
      '/^(:)/'                        => Token::T_COLON, // :
      '/^"([^"]*)"/'                  => Token::T_STRING, // "directive string"
      '/^([-+]?[0-9]*\.?[0-9]+)/'     => Token::T_NUMBER, // -1.234
      '/^(true)/'                     => Token::T_TRUE, // true
      '/^(false)/'                    => Token::T_FALSE, // false
      '/^([a-z_\-][a-zA-Z_\-0-9]*)/'  => Token::T_KEY,
      '/^(\s+)/'                      => Lexer::SKIP // whitespace
    );
  }
}
?>
