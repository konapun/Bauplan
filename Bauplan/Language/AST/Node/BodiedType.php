<?php
namespace Bauplan\Language\AST\Node;

abstract class BodiedType extends Type {
  private $body;

  function __construct($id, $directives=array(), $body=array()) {
    parent::__construct($id, $directives);
    $this->body = $body;
  }

  /*
   * Locate a child node by its Type ID
   */
  function get($id) {
    foreach ($this->body as $node) {
      if ($node instanceof Type) {
        if ($node->getID() == $id) {
          return $node;
        }
      }
    }
  }
}
?>
