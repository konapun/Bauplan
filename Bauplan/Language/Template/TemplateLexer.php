<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\Token as BauplanToken;
use Bauplan\Language\Lexer as Lexer;
use Bauplan\Language\Directive\DirectiveLexer as DirectiveLexer;
use Bauplan\Language\Template\TemplateToken as Token;

/*
 * Entry point for Bauplan parsing - Since Bauplan tries to be somewhat
 * unobtrusive for templating, it tries to reserve as few tokens as possible
 * while retaining expressiveness. Most of this expressiveness is needed within
 * directives, so in order to not have to reserve additional tokens, directives
 * go through their own lexing phase. Tokens listed here are not the full set,
 * but represent the set of all tokens which must be escaped. However, it's
 * advisable to use literals wherever possible to avoid special case munging of
 * tokens into literals.
 *
 * Sequences of T_IDENTIFIERs are transformed into T_LITERAL_STRINGs during
 * the parsing phase.

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
      '/^;;\((.*?)(?=;;\))/s'   => Lexer::SKIP, // ;;( block comment ;;)
      '/^(;;\))/'               => Lexer::SKIP, // hack needed because the capture in the line above doesn't include the closing and will throw off the lexer offset
      '/^;;(.*)/'               => Lexer::SKIP, // ;; inline comment
      '/^<<<(.*?)(?=>>>)/s'     => Token::T_LITERAL_STRING, // <<< literal string >>>
      '/^(>>>)/'                => Lexer::SKIP, // hack like comment close above
      '/^{([^}]*)}/'            => Token::T_DIRECTIVE_STRING, // { directive string to be parsed later }
      '/^(lambda)/'             => Token::T_LAMBDA, // lambda
      '/^(\*)/'                 => Token::T_TEMPLATE, // *
      '/^(@)/'                  => Token::T_SECTION, // @
      '/^(&)/'                  => Token::T_CODE, // &
      '/^(\$)/'                 => Token::T_VARIABLE, // $
      '/^(\#)/'                 => Token::T_INSTRUCTION, // #
      '/^(\()/'                 => Token::T_TYPE_OPEN, // (
      '/^(\))/'                 => Token::T_TYPE_CLOSE, // )
      '/^(\w+)/'                => Token::T_IDENTIFIER,
      '/^(\s+)/'                => Lexer::SKIP, // whitespace
      '/^(.+)/'                 => Token::T_ANY // anything else that didn't match. We do this in order to allow continuing no matter what since we'll reclassify it into a literal later
    );
  }

  /*
   * Replace all directive strings with tokens produced by running each
   * T_DIRECTIVE_STRING through the directive lexer
   */
  function postLex($tokens) {
    $lexer = $this->directiveLexer;

    $postTokens = array();
    foreach ($tokens as $token) {
      if ($token->getType() == Token::T_DIRECTIVE_STRING) { // surround directive contents in T_DIRECTIVE_START and T_DIRECTIVE_END tokens to simplify some parsing rules
        array_push($postTokens, new BauplanToken('{', Token::T_DIRECTIVE_START));
        $source = $token->getValue();

        foreach ($lexer->tokenize($source) as $directiveToken) {
          array_push($postTokens, $directiveToken);
        }
        array_push($postTokens, new BauplanToken('}', Token::T_DIRECTIVE_END));
      }
      else {
        array_push($postTokens, $token);
      }
    }
    return $postTokens;
  }

}
?>
