<?php
namespace Bauplan\Compiler;

class SyntaxTree {
  private $parent;
  private $data;
  private $children;
  
  function __construct($data) {
    $this->parent = null;
    $this->children = array();
    $this->data = $data;
  }
  
  function getData() {
    return $this->data;
  }
  
  function addChild($data) {
    $node = $this->addChildNode(new SyntaxTree($data));
    return $node;
  }
  
  function addChildNode($node) {
    $node->parent = $this;
    array_push($this->children, $node);
    return $node;
  }
  
  function isRoot() {
    return $this->parent == null;
  }
  
  function getChildren() {
    return count($this->children) == 0;
  }
}
?>
