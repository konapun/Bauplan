<?php
namespace Bauplan\Compiler;
use Bauplan\Exception\SyntaxError;

/*
 * Break source into tokens to be consumed by the parser
 *
 * Taken from tutorial here: http://nitschinger.at/Writing-a-simple-lexer-in-PHP
 *
 * FIXME - retain quotes and literal start/end tokens or convert here? (probably
 * keep and include in CST? See how other languages handle heredocs)
 */
class Lexer {

  private static $terminals = array(
    '/^(\$\$)\s*/'                   => Token::T_PREPROC_DECL, // $$
    '/^(\*)\s*/'                     => Token::T_TEMPLATE, // *
    '/^(@)\s*/'                      => Token::T_SECTION, // @
    '/^(\$)\s*/'                     => Token::T_VARIABLE, // $
    '/^(&)\s*/'                      => Token::T_CODE, // &
    '/^(#)\s*/'                      => Token::T_INSTRUCTION, // #
    '/^(\()\s*/'                     => Token::T_TYPE_OPEN, // (
    '/^(\))\s*/'                     => Token::T_TYPE_CLOSE, // )
    '/^({)\s*/'                      => Token::T_DIRBLOCK_OPEN, // {
    '/^(})\s*/'                      => Token::T_DIRBLOCK_CLOSE, // }
    '/^(:)\s*/'                      => Token::T_DIR_KEYVAL_SEP, // :
    '/^(\|)\s*/'                     => Token::T_DIRECTIVE_SEP, // |
    '/^(,)\s*/'                      => Token::T_VAL_SEP, // ,
    '/^(\d+)\s*/'                    => Token::T_INTEGER, // 0-9
    '/^(\d+\.?\d*$)\s*/'             => Token::T_DOUBLE,
    '/^(true|false)\s*/'             => Token::T_BOOL,
    '/^(".*")\s*/'                   => Token::T_QUOTED_STRING,
    '/^([$A-Z_\-\+][0-9A-Z_\-\+]*)\s*/i' => Token::T_IDENTIFIER,
    '/^(;;\()/'                      => 'BLOCKCOMMENT_START',
    '/^(;;\))/'                      => 'BLOCKCOMMENT_END',
    '/^(<<<)/'                       => 'LITERAL_START',
    '/^(>>>)/'                       => 'LITERAL_END',
    '/^(;;.*)/'                      => 'SKIP', // ;; inline comment
    '/^(\s+)/'                       => 'SKIP', // spaces
    '/^(.+?)/'                       => Token::T_BAREWORD // anything else
  );

  function __construct() {}

  function tokenize($source) {
    if (!is_array($source)) $source = array($source);

    $tokens = array();
    foreach ($source as $number => $line) {
      $offset = 0;
      while ($offset < strlen($line)) {
        $string = substr($line, $offset);
        $result = static::match($string, $number+1);
        if ($result === false) {
          throw new SyntaxError(sprintf('Unexpected character "%s"', $offset), $offset); // FIXME
        }

        array_push($tokens, $result);
        $offset += strlen($result->getValue());
      }
    }

    return new TokenStream($this->finalize($tokens));
  }

  private function finalize($tokens) {
    return $this->removeSkippedTokens($this->handleLiterals($tokens));
  }

  /*
   * Re-lex tokens enclosed in literal tags as literals
   * FIXME: Do at concrete syntax tree -> abstract syntax tree transition?
   */
  private function handleLiterals($tokens) {
    $inLiteral = false;
    $literalValue = "";
    $realTokens = array();
    foreach ($tokens as $token) {
      switch ($token->getType()) {
        case 'LITERAL_START':
          $inLiteral = true;
          break;
        case 'LITERAL_END':
          array_push($realTokens, new Token($literalValue, 'T_LITERAL', $token->getCursor()));
          $inLiteral = false;
          break;
        default:
          if ($inLiteral) {
            $literalValue .= $token->getValue();
          }
          else {
            array_push($realTokens, $token);
          }
      }
    }

    return $realTokens;
  }

  /*
   * Remove whitespace and comment tokens
   */
  private function removeSkippedTokens($tokens) {
    $inBlockComment = false;
    $realTokens = array();
    foreach ($tokens as $token) {
      switch ($token->getType()) {
        case 'BLOCKCOMMENT_START':
          $inBlockComment = true;
          break;
        case 'BLOCKCOMMENT_END':
          $inBlockComment = false;
          break;
        case 'SKIP':
          break;
        default:
          if (!$inBlockComment) {
            array_push($realTokens, $token);
          }
      }
    }

    return $realTokens;
  }

  private static function match($string, $lineNumber) {
    foreach (static::$terminals as $pattern => $tokenName) {
      if (preg_match($pattern, $string, $matches)) {
        return new Token($matches[1], $tokenName, $lineNumber);
      }
    }

    return false;
  }
}
?>
