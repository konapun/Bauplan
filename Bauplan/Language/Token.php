<?php
namespace Bauplan\Language;

class Token {
  private $type;
  private $value;
  private $line;

  function __construct($value, $type, $line=null) {
    $this->type = $type;
    $this->value = $value;
    $this->line = $line;
  }

  function getType() {
    return $this->type;
  }

  function getValue() {
    return $this->value;
  }

  function getLine() {
    return $this->line;
  }

  function __toString() {
    return $this->value;
  }
}
?>
