<?php
namespace Bauplan\Language\AST\Node;

class Variable extends Type {
  private $value;

  function __construct($id, $value="", $directives=array()) {
    parent::__construct($id, $direcives);
    $this->value = $value;
  }

  function getValue() {
    return $this->value;
  }

  function evaluate() {
    return $this->getValue();
  }
}
?>
