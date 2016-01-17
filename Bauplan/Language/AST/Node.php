<?php
namespace Bauplan\Language\AST;

/*
 * The abstract syntax tree is a recursive structure where every node is the
 * root of the subtree rooted at itself
 */
class Node {
  private $data;
  private $parent;
  private $children;

  function __construct($data) {
    $this->data = $data;
    $this->parent = null;
    $this->children = array();
  }

  function addChild($data) {
    $node = new self($data);
    return $this->addChildNode($node);
  }

  function addChildNode($node) {
    $node->parent = $this;
    array_push($this->children, $node);

    return $node;
  }

  function getParent() {
    return $this->parent;
  }

  function getChildren() {
    return $this->children;
  }

  function isRoot() {
    return is_null($this->parent);
  }

  function isLeaf() {
    return count($this->children) === 0;
  }
}
?>
