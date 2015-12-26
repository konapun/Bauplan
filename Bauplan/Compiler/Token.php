<?php
namespace Bauplan\Compiler;

class Token {

  private $type;
  private $value;
  private $cursor;

  function __construct($value, $type, $cursor=-1) {
    $this->type = $type;
    $this->value = $value;
    $this->cursor = $cursor;
  }

  function getType() {
    return $this->type;
  }

  function getValue() {
    return $this->value;
  }

  function getCursor() {
    return $this->cursor;
  }

  function compareType($type) {
    return $this->type === $type;
  }

  function oneOf($types) {
    $thisType = $this->type;
    foreach ($types as $type) {
      if ($type === $thisType) return true;
    }

    return false;
  }

  function __toString() {
    return sprintf("%s:%s (%d)", $this->type, $this->value, $this->cursor);
  }
}
?>
