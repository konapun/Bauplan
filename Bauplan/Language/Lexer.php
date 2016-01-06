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

  /*
   * Top-down token lexer
   */
  function tokenize($source) {
    $sourcecode = $source;
    if (!is_array($source)) $source = array($source);

    $tokens = array();
    foreach ($this->mapTerminals() as $pattern => $tokenName) {
      foreach ($source as $number => $line) {
        if (preg_match_all($pattern, $line, $matches, PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE)) {
          foreach ($matches[1] as $match) { // index 1 contains the actual match
            list($value, $index) = $match;

            array_push($tokens, new LexerToken(new Token($value, $tokenName, $line), $index));
          }
          $source = preg_replace($pattern, '!', $source); // FIXME - explode on this later
          var_dump($source);
        }
      }
    }

    $res = $this->postLex(new TokenStream($this->remapSource($this->finalize($tokens), $sourcecode)));
    var_dump($res);
    return $res;
  }

  /*
   * Override this to reduce/transform
   */
  protected function postLex($tokenStream) {
    return $tokenStream;
  }

  private function finalize($tokens) {
    $tokens = array_map(function($el) { return $el->getToken(); }, $tokens);
    // usort($tokens, function($a, $b) {
    //   echo "Comparing position " . $a->getPosition() . " vs " . $b->getPosition() . " for tokens " . $a->getToken() . " and " . $b->getToken() . "\n";
    //   return $a->getPosition() > $b->getPosition();
    // });
    // $tokens = array_map(function($el) { return $el->getToken(); }, $tokens);
    return $this->handleLiterals($tokens);
    return $this->removeSkippedTokens($this->handleLiterals($tokens));
  }

  private function remapSource($tokens, $source) {
    $index = 0;
    $literals = array();
    foreach ($tokens as $token) {
      if ($token->getType() == Lexer::LITERAL) {
        echo "ON TOKEN '" . $token->getValue() . "'\n";
        $literal = "";
        $prevStrpos = -1;
        foreach (str_split($token->getValue()) as $char) {
          $prev = $literal;
          $literal .= $char;

          $strpos = strpos($source, $literal, $index);
          if ($prevStrpos == -1) $prevStrpos = $strpos;
          if ($strpos === false || $strpos != $prevStrpos) {
            $index += strlen($prev);

            $literal = $char;
            $prevStrpos = -1;
            if ($prev != '!') array_push($literals, new Token($prev, Lexer::LITERAL, $token->getLine()));
          }
          else {
            echo "Strpos: $strpos, prevStrpos: $prevStrpos ($literal)\n";
            $prevStrpos = $strpos;
          }
        }
        if ($literal && $literal != '!') {
          array_push($literals, new Token($literal, Lexer::LITERAL, $token->getLine()));
        }
      }
    }
    return $literals;
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
}

/*
 * In order to simplify generating tokens, tokens are specified from most to
 * least specific and are lexed in the order they're defined before being pieced
 * back together in order
 */
class LexerToken {
  private $token;
  private $position;

  function __construct($token, $position) {
    $this->token = $token;
    $this->position = $position;
  }

  function getToken() {
    return $this->token;
  }

  function getPosition() {
    return $this->position;
  }
}
?>
