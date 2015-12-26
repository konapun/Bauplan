<?php
namespace Bauplan\Language;
use Bauplan\Exception\SyntaxError;

/*
 * Break source into tokens to be consumed by the parser
 */
abstract class Lexer {

  /* Types that have special meanings within the lexer which may be used by different concrete lexers */
  const SKIP = 'SKIP';
  const LITERAL = 'T_LITERAL';
  const BLOCK_IGNORE_START = 'BLOCK_IGNORE_START';
  const BLOCK_IGNORE_END = 'BLOCK_IGNORE_END';
  const LITERAL_START = 'LITERAL_START';
  const LITERAL_END = 'LITERAL_END';
  const T_ESCAPE = 'T_ESCAPE';

  abstract function mapTerminals();

  function tokenize($source) {
    if (!is_array($source)) $source = array($source);

    $tokens = array();
    foreach ($source as $number => $line) {
      $offset = 0;
      while ($offset < strlen($line)) {
        $string = substr($line, $offset);
        $result = $this->match($string, $number+1);
        if ($result === false) {
          throw new SyntaxError(sprintf('Unexpected character "%s"', $string[$offset]), $offset);
        }

        array_push($tokens, $result);
        $offset += strlen($result->getValue());
      }
    }

    return $this->postLex(new TokenStream($this->finalize($tokens)));
  }

  /*
   * Override this to reduce/transform
   */
  protected function postLex($tokenStream) {
    return $tokenStream;
  }

  private function finalize($tokens) {
    return $this->removeSkippedTokens($this->handleLiterals($tokens));
  }

  /*
   * Re-lex tokens enclosed in literal tags as literals
   */
  private function handleLiterals($tokens) {
    $inLiteral = false;
    $literalValue = "";
    $realTokens = array();
    foreach ($tokens as $token) {
      switch ($token->getType()) {
        case Lexer::LITERAL_START:
          array_push($realTokens, $token);
          $inLiteral = true;
          break;
        case Lexer::LITERAL_END:
          array_push($realTokens, new Token($literalValue, Lexer::LITERAL, $token->getLine()));
          array_push($realTokens, $token);
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
        case Lexer::BLOCK_IGNORE_START:
          $inBlockComment = true;
          break;
        case Lexer::BLOCK_IGNORE_END:
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

  private function match($string, $lineNumber) {
    foreach ($this->mapTerminals() as $pattern => $tokenName) {
      if (preg_match($pattern, $string, $matches)) {
        return new Token($matches[1], $tokenName, $lineNumber);
      }
    }

    return false;
  }
}
?>
