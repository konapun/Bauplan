<?php
namespace Bauplan\Type;

class Instruction extends ComplexType {

  /*
   * Example: #(capitalize {arguments: "this", "is", "a", "sentence."}) ;; returns a value list of ("this", "is", "a", "sentence") 
   */
  function getArguments() {
    $directivesList = $this->getDirectives();

    return $directivesList->hasDirective('arguments') ? $directivesList->getDirectiveValue('arguments') : array();
    // FIXME: arguments should be an object (created here or before here?)
  }
}
?>
