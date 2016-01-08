<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\Lexer as Lexer;
use Bauplan\Language\Directive\DirectiveLexer as DirectiveLexer;
use Bauplan\Language\Template\TemplateToken as Token;

/*
 * Entry point for Bauplan parsing - Since Bauplan tries to be somewhat
 * unobtrusive for templating, it tries to reserve as few tokens as possible
 * while retaining expressiveness. Most of this expressiveness is needed within
 * directives, so in ordder to not have to reserve additional tokens, directives
 * go through their own lexing phase. Tokens listed here are not the full set,
 * but represent the set of all tokens which must be escaped. However, it's
 * advisable to use literals wherever possible to avoid special case munging of
 * tokens into literals.
 *
 * TODO: Handle escape character
 */
class TemplateLexer extends Lexer {
  private $directiveLexer;

  function __construct() {
    $this->directiveLexer = new directiveLexer();
  }

  protected function tokens() {
    return array(
      ';;\(([^;;\)]*);;\)'    => Lexer::SKIP, // ;;( block comment ;;)
      //';;(.*)'                => Lexer::SKIP, // ;; inline comment
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
      '(\s+)'                 => Lexer::SKIP, // whitespace
      '(.+)'                  => Token::T_ANY // anything else that didn't match. We do this in order to allow continuing no matter what since we'll reclassify it into a literal later
    );
  }

  // TODO - Eventually, combine sequences of T_IDENTIFIER tokens into T_LITERAL_STRING tokens
  function postLex($tokens) {
    $lexer = $this->directiveLexer;

    $postTokens = array();
    foreach ($tokens as $token) {
      if ($token->getType() == Token::T_DIRECTIVE_STRING) {
        $source = $token->getValue();

        foreach ($lexer->tokenize($source) as $directiveToken) {
          array_push($postTokens, $directiveToken);
        }
      }
      else {
        array_push($postTokens, $token);
      }
    }
    return $postTokens;
  }

}
?>
