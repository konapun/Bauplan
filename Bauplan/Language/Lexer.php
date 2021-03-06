<?php
namespace Bauplan\Language;

use Bauplan\Exception\SyntaxException as SyntaxException;

/*
 * Break source into tokens to be consumed by the parser
 */
abstract class Lexer {

  /* Types that have special meanings within the lexer which may be used by different concrete lexers */
  const SKIP = 'SKIP';
  const SKIP_BLOCK_START = 'SKIP_BLOCK_START';
  const SKIP_BLOCK_END = 'SKIP_BLOCK_END';
  const T_ESCAPE = 'T_ESCAPE';

  /*
   * Return a map of regexes to token name. Regexes are standard PHP regexes but
   * should be written with the expectation that each regex should match
   * starting at the beginning of the line since source will be trimmed.
   */
  abstract protected function tokens();

  /*
   * Any further operations that should be performed on the tokens after they've
   * been lexed.
   */
  protected function postLex($tokens) {
    return $tokens;
  }

  final function tokenize($source) {
    $lineNumber = 1;
    $offset = 0;

    $tokens = array();
    while ($offset < strlen($source)) {
      $string = substr($source, $offset);
      $result = $this->match($string, $lineNumber, $offset);
      if ($result === false) {
        throw new SyntaxException($string, $lineNumber, $offset); // FIXME
      }

      list($token, $match) = $result;
      array_push($tokens, $token);

      $offset += strlen($match);
      $lineNumber += substr_count($string, str_replace(array("\r\n","\n\r","\r"), "\n", $string)); // increment line number by all types of newlines
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

  private function match($string, $line, $column) {
    foreach ($this->tokens() as $pattern => $tokenName) {
      if (preg_match($pattern, $string, $matches)) {
        //echo "!!!Matched '$pattern' (for $tokenName) as '" . $matches[1] . "' on $string\n--------------------------------\n";
        return array(new Token($matches[1], $tokenName, $line, $column), $matches[0]);
      }
    }

    return false;
  }
}
?>
