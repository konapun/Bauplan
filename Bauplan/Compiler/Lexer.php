<?php
namespace Bauplan\Compiler;
use Bauplan\Exception\LexerException;
use Bauplan\Compiler\Token;

/*
 * Break source into tokens to be consumed by the parser
 *
 * Taken from tutorial here: http://nitschinger.at/Writing-a-simple-lexer-in-PHP
 */
class Lexer {
  
  private static $terminals = array(
    '/^(\$\$)\s*/'                   => 'T_PREPROC_DECL', // $$
    '/^(\*)\s*/'                     => 'T_TEMPLATE', // *
    '/^(@)\s*/'                      => 'T_SECTION', // @
    '/^(\$)\s*/'                     => 'T_VARIABLE', // $
    '/^(&)\s*/'                      => 'T_CODE', // &
    '/^(#)\s*/'                      => 'T_INSTRUCTION', // #
    '/^(\()\s*/'                     => 'T_TYPE_OPEN', // (
    '/^(\))\s*/'                     => 'T_TYPE_CLOSE', // )
    '/^({)\s*/'                      => 'T_DIRBLOCK_OPEN', // {
    '/^(})\s*/'                      => 'T_DIRBLOCK_CLOSE', // }
    '/^(:)\s*/'                      => 'T_DIR_KEYVAL_SEP', // :
    '/^(\|)\s*/'                     => 'T_DIRECTIVE_SEP', // |
    '/^(,)\s*/'                      => 'T_VAL_SEP', // ,
    '/^(\d+)\s*/'                    => 'T_INTEGER', // 0-9
    '/^(\d+\.?\d*$)\s*/'             => 'T_DOUBLE',
    '/^(true|false)\s*/'             => 'T_BOOL',
    '/^(".*")\s*/'                   => 'T_QUOTED_STRING',
    '/^([$A-Z_\-][0-9A-Z_\-]*)\s*/i' => 'T_IDENTIFIER',
    '/^(\w+\-*\w*)\s*/'              => 'T_BAREWORD',
    '/^(;;\()/'                      => 'T_BLOCKCOMMENT_START',
    '/^(;;\))/'                      => 'T_BLOCKCOMMENT_END',
    '/^(;;.*)/'                      => 'T_SKIP', // ;; inline comment 
    '/(\s+)/'                        => 'T_SKIP', // spaces
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
          throw new LexerException("Lexing failed at\n\t$string\non source line " . ($line+1) . " or $number");
        }
        
        array_push($tokens, $result);
        $offset += strlen($result->getValue());
      }
    }
    
    return $this->removeSkippedTokens($tokens);
  }
  
  /*
   * Remove whitespace and comment tokens
   */
  private function removeSkippedTokens($tokens) {
    $inBlockComment = false;
    $realTokens = array();
    foreach ($tokens as $token) {
      switch ($token->getType()) {
        case 'T_BLOCKCOMMENT_START':
          $inBlockComment = true;
          break;
        case 'T_BLOCKCOMMENT_END':
          $inBlockComment = false;
          break;
        case 'T_SKIP':
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
