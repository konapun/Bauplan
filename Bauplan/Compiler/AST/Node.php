<?php
namespace Bauplan\Compiler\AST;

abstract class Node {
  private $parent;
  private $firstChild;
  private $nextSibling;

  function __construct() {
    $this->parent = null;
    $this->firstChild = null;
    $this->nextSibling = null;
  }

  function appendChild($node) {
    $this->firstChild = $node;
    return $child;
  }

  function appendSibling($node) {
    $this->nextSibling = $node;
    return $node;
  }

  function getParent() {
    return $this->parent;
  }

  function getFirstChild() {
    return $this->firstChild;
  }

  function getNextSibling() {
    return $this->nextSibling;
  }

  function hasParent() {
    return $this->parent != null;
  }

  function hasChild() {
    return $this->firstChild != null;
  }

  function hasNextSibling() {
    return $this->nextSibling != null;
  }
}
?>
