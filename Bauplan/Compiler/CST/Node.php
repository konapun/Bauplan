<?php
namespace Bauplan\Compiler\CST;
use Bauplan\Compiler\Token as Token;

/*
 * Concrete syntax tree node - the direct result of the parsing phase
 */
class Node {
  private $token;
  private $parent;
  private $children;
  private $depth;

  function __construct(Token $token) {
    $this->token = $token;
    $this->parent = null;
    $this->children = array();
    $this->depth = 0;
  }

  function getToken() {
    return $this->token;
  }

  function getParent() {
    return $this->getParent();
  }

  function getChildren() {
    return $this->children;
  }

  function getDepth() {
    return $this->depth;
  }

  function addChild(Token $token) {
    return $this->addChildNode(new Node($token));
  }

  function addChildNode(Node $node) {
    $node->parent = $this;
    $node->depth = $this->depth+1;
    array_push($this->children, $node);

    return $node;
  }

  function isRoot() {
    return $this->parent == null;
  }

  function isLeaf() {
    return count($this->children) == 0;
  }

}
?>
