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
  const SKIP_BLOCK_START = 'SKIP_BLOCK_START';
  const SKIP_BLOCK_END = 'SKIP_BLOCK_END';
  const T_ESCAPE = 'T_ESCAPE';

  abstract protected function tokens();

  protected function postLex($tokenStream) {
    return $tokenStream;
  }

  final function tokenize($source) {
    if (!is_array($source)) $source = array($source);

    $tokens = array();
    foreach ($source as $number => $line) {
      $offset = 0;
      while ($offset < strlen($line)) {
        $string = substr($line, $offset);
        $result = $this->match($string, $number+1);
        if ($result === false) {
          throw new SyntaxError("Lexing failed at\n\t$string\non source line " . ($line+1) . " or $number");
        }

        list($token, $match) = $result;
        array_push($tokens, $token);
        $offset += strlen($match);
      }
    }

    return $this->postLex($this->removeSkippedTokens($tokens));
  }

  /*
   * Remove whitespace and comment tokens
   */
  private function removeSkippedTokens($tokens) {
    $inSkip = false;
    $realTokens = array();
    foreach ($tokens as $token) {
      switch ($token->getType()) {
        case self::SKIP_BLOCK_START:
          $inBlockComment = true;
          break;
        case self::SKIP_BLOCK_END:
          $inBlockComment = false;
          break;
        case self::SKIP:
          break;
        default:
          if (!$inSkip) {
            array_push($realTokens, $token);
          }
      }
    }

    return $realTokens;
  }

  private function match($string, $lineNumber) {
    //echo "String: \"$string\"\n";
    foreach ($this->tokens() as $pattern => $tokenName) {
      $pattern = "/^$pattern/";
      if (preg_match($pattern, $string, $matches)) {
        //echo "Pushing token " . $matches[1] . " of type $tokenName\n";
        //echo "----------------------------------------------------\n";
        return array(new Token($matches[1], $tokenName, $lineNumber), $matches[0]);
      }
    }

    return false;
  }
}
?>
