<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\Lexer as Lexer;
use Bauplan\Language\Template\TemplateToken as Token;
use Bauplan\Language\TokenStream as TokenStream;

/*
 * Break source into tokens to be consumed by the parser
 *
 * Taken from tutorial here: http://nitschinger.at/Writing-a-simple-lexer-in-PHP
 *
 * FIXME - retain quotes and literal start/end tokens or convert here? (probably
 * keep and include in CST? See how other languages handle heredocs)
 */
class TemplateLexer extends Lexer {

  function __construct() {}

  protected function tokens() {
    return array(
      ';;\(([^;;\)]*)'        => Lexer::SKIP, // ;;( block comment ;;)
      ';;.*'                  => Lexer::SKIP, // ;; inline comment
      '<<<([^\>\>\>]*)\>\>\>' => Token::T_LITERAL_STRING, // <<< literal string >>>
      '{([^}]*)}'             => Token::T_DIRECTIVE_STRING, // { directive string to be parsed later }
      '(lambda)'              => Token::T_LAMBDA, // lambda
      '(\*)'                  => Token::T_TEMPLATE, // *
      '(@)'                   => Token::T_SECTION, // @
      '(&)'                   => Token::T_CODE, // &
      '(\$)'                  => Token::T_VARIABLE, // $
      '(\#)'                  => Token::T_INSTRUCTION, // #
      '(\()'                  => Token::T_TYPE_OPEN, // (
      '(\))'                  => Token::T_TYPE_CLOSE, // )
      '(\w+)'                 => Token::T_IDENTIFIER,
      '(\s+)'                 => Lexer::SKIP // whitespace
    );
  }

  function postLex($tokenStream) {
    return $tokenStream;
    return $this->combineStrings($tokenStream);
  }

  /*
   * Combine streams of `T_STRING` into a single T_STRING with each original
   * T_STRING token separated by a single space
   */
  private function combineStrings($tokenStream) {
    $string = array();
    $reduced = array();
    foreach ($tokenStream as $token) {
      if ($token->getType() == Token::T_STRING) {
        array_push($string, $token->getValue());
      }
      else {
        if ($string) {
          $stringToken = new Token(implode($string, ' '), Token::T_STRING, $token->getLine());
          $string = array();
          array_push($reduced, $stringToken);
        }

        array_push($reduced, $token);
      }
    }

    return new TokenStream($reduced, $tokenStream->getFile());
  }
}
?>
