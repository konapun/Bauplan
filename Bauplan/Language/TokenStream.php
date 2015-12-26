<?php
namespace Bauplan\Language;

use Bauplan\Exception\SyntaxError as SyntaxError;

/*
 * A stream of tokens generated by the lexer and sent to the parser. A stream is
 * associated with a Bauplan source file
 *
 * Adapted from the Symfony project
 */
class TokenStream extends \ArrayIterator {
  private $position;
  private $file;

  function __construct($tokens, $file=null) {
    parent::__construct($tokens);
    $this->position = 0;
    $this->file = $file;
  }

  function setFile($file) {
    $this->file = $file;
  }

  function getFile() {
    return $this->file;
  }

  /*
   * In fatal mode: Throws a syntax error if an unexpected token is encountered
   * In regular mode: Returns true or false depending on whether the expected
   * token is found
   */
  function expect($type, $optionalOrMessage=null) {
    $returnVal;
    if ($optionalOrMessage === null) {
      $returnVal = $this->expectOptional($type);
    }
    else {
      $returnVal = $this->expectFatal($type, $optionalOrMessage);
    }

    if ($returnVal !== false) $this->next();
    return $returnVal;
  }

  function expectOneOf($types, $optionalOrMessage=null) {
    $current = $this->current;
    if ($current->oneOf($types)) {
      $this->next();
      return $current;
    }
    if ($optionalOrMessage) {
      throw new SyntaxError(sprintf('%sUnexpected type "%s" with value "%s" (expected "%s %s") at line %d in file %s', $message ? $message . '. ' : '', $token->getType(), $token->getValue(), $type, $value, $token->getCursor(), $this->file));
    }
    return false;
  }

  /*
   * Return whether or not the expected type was matched
   */
  private function expectOptional($type) {
    $current = $this->current;
    return $current->compareType($type) ? $current : false;
  }

  /*
   * Throw error on unexpected token type
   */
  private function expectFatal($type, $message) {
    $token = $this->current;
    if (!$token->compareType($type)) {
      throw new SyntaxError(sprintf('%sUnexpected type "%s" with value "%s" (expected "%s") at line %d in file %s', $message ? $message . '. ' : '', $token->getType(), $token->getValue(), $type, $token->getCursor(), $this->file));
    }
    return $token;
  }

}

?>