<?php
namespace Bauplan\Language\AST\Node;

abstract class Type implements ASTNode {
  private $identifier;
  private $directives;


  function __construct($identifier, $directives=array()) {
    $this->identifier = $identifier;
    $this->directives = $directives;
  }

  function getID() {
    return $this->identifier;
  }

  /*
   * Load a direcive into this type
   */
  function loadDirective($directive) {
    // TODO
  }

}
?>
