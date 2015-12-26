<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\Lexer as Lexer;
use TemplateToken as Token;

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
      '/^(\$\$)\s*/'                       => Token::T_PREPROC_DECL, // $$
      '/^(\*)\s*/'                         => Token::T_TEMPLATE, // *
      '/^(@)\s*/'                          => Token::T_SECTION, // @
      '/^(\$)\s*/'                         => Token::T_VARIABLE, // $
      '/^(&)\s*/'                          => Token::T_CODE, // &
      '/^(#)\s*/'                          => Token::T_INSTRUCTION, // #
      '/^(\()\s*/'                         => Token::T_TYPE_OPEN, // (
      '/^(\))\s*/'                         => Token::T_TYPE_CLOSE, // )
      '/^({)\s*/'                          => Token::T_DIRBLOCK_OPEN, // {
      '/^(})\s*/'                          => Token::T_DIRBLOCK_CLOSE, // }
      '/^>>>\s*/'                          => LEXER::T_LITERAL_OPEN, // >>>
      '/^>>>\s*/'                          => LEXER::T_LITERAL_CLOSE, // <<<
      '/^([$A-Z_\-\+][0-9A-Z_\-\+]*)\s*/i' => Token::T_IDENTIFIER,
      '/^(;;\()/'                          => Lexer::BLOCK_IGNORE_START,
      '/^(;;\))/'                          => Lexer::BLOCK_IGNORE_END,
      '/^(;;.*)/'                          => Lexer::SKIP, // ;; inline comment
      '/^(\s+)/'                           => Lexer::SKIP, // spaces
      '/^(.+?)/'                           => Token::T_ANY // anything else
    );
  }
}
?>
