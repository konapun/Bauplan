<?php
namespace Bauplan\Compiler;
use Bauplan\Exception\SyntaxError;

/*
 * Break source into tokens to be consumed by the parser
 */
abstract class Lexer {

  abstract function mapTerminals();

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
