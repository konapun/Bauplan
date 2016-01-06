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

  function mapTerminals() {
    return array(
      '/(;;\()\s*/'              => Lexer::BLOCK_IGNORE_START,
      '/(;;\))\s*/'              => Lexer::BLOCK_IGNORE_END,
      '/(;;.*)\s*/'              => Lexer::SKIP, // ;; inline comment
      '/(\\\)\s*/'               => Lexer::T_ESCAPE,
      //"/^'([^\"]\\S*|\".+?\")\\s*/" => 'T_QUOTED_STRING',
      '/(<<<)\s*/'               => Lexer::LITERAL_START, // <<<
      '/(>>>)\s*/'               => Lexer::LITERAL_END, // >>>
      '/(\$\$)\s*/'              => Token::T_PREPROC_DECL, // $$
      '/(\*)\s*/'                => Token::T_TEMPLATE, // *
      '/(@)\s*/'                 => Token::T_SECTION, // @
      '/(\$)\s*/'                => Token::T_VARIABLE, // $
      '/(&)\s*/'                 => Token::T_CODE, // &
      '/(#)\s*/'                 => Token::T_INSTRUCTION, // #
      '/(\()\s*/'                => Token::T_TYPE_OPEN, // (
      '/(\))\s*/'                => Token::T_TYPE_CLOSE, // )
      '/({)\s*/'                 => Token::T_DIRBLOCK_OPEN, // {
      '/(})\s*/'                 => Token::T_DIRBLOCK_CLOSE, // }
      '/(lambda)\s*/'            => Token::T_LAMBDA, // lambda
      '/(.+)\s*/'                => Lexer::LITERAL, // anything else
      '/(\s+)/'                  => Lexer::SKIP, // spaces
    );
  }

  function postLex($tokenStream) {
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
