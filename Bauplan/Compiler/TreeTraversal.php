<?php
namespace Bauplan\Compiler;

class TreeTraversal {
  /* Traversal type enum */
  const TRAVERSE_BF = 0;
  
  private $tree;
  
  function __construct($tree) {
    $this->tree = $tree;
  }
  
  function walk($algorithm, $callback) { // walkers, Coral!
    switch ($algorithm) {
      case self::TRAVERSE_BF:
        $this->walkRecBF($this->tree, $callback);
      default:
        throw new \InvalidArgumentException("No algorithm for walk type");  
    }
  }
  
  /*
   * Breadth-first traversal
   */
  private function walkRecBF($tree, $callback) {
    if ($callback($tree) === true) return; // break traversal early if callback returns true
    foreach ($tree->getChildren() as $child) {
      $this->walkRecBF($child, $callback);
    }
  }
}
?>
