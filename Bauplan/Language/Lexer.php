<?php
namespace Bauplan\Language;
use Bauplan\Exception\SyntaxError;

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
    foreach ($this->tokens() as $pattern => $tokenName) {
      $pattern = $this->mungeRegex($pattern);
      if (preg_match($pattern, $string, $matches)) {
        //echo "!!!Matched '$pattern' (for $tokenName) as '" . $matches[1] . "' on $string\n--------------------------------\n";
        return array(new Token($matches[1], $tokenName, $lineNumber), $matches[0]);
      }
    }

    return false;
  }

  /*
   * TODO: Since match wants to only match the first regex, we need to insert ^
   * into the beginning of the regex. However, some regexes want this in places
   * other than the start of the string so for now just do it in the concrete
   * lexer regexes
   */
  private function mungeRegex($regex) {
    return $regex;
    return '/^' . substr($regex, 1);
  }
}
?>
