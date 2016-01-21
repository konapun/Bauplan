<?php
namespace Bauplan\Language;

use Bauplan\Language\StateMachine\NodeAdapter as NodeAdapter;

class Token implements NodeAdapter {
  private $type;
  private $value;
  private $line;

  function __construct($value, $type, $line=null) {
    $this->type = $type;
    $this->value = $value;
    $this->line = $line;
  }

  function getID() {
    return $this->getType();
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
