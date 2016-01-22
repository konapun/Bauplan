<?php
namespace Bauplan\Language\AST;

class TreeWalker {

  /* Algorithms */
  const DEPTH_FIRST = 0;
  const BREADTH_FIRST = 1;

  function walk($tree, $fn, $algorithm=0) {
    switch ($algorithm) {
      case self::DEPTH_FIRST:
      default: // default is BF
        $this->walkDF($tree, $fn);
        break;
    }
  }

  private function walkDF($tree, $fn) {
    $fn($tree);
    foreach ($tree->getChildren() as $node) {
      $this->walkDF($node, $fn);
    }
  }

}
 ?>
