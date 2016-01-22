<?php
namespace Bauplan\Language\AST\IO;

use Bauplan\Language\AST\TreeWalker as Walker;

/*
 * Support importing/exporting trees in JSON format
 */
class JSON implements IO {
  private $walker;

  function __construct() {
    $this->walker = new Walker();
  }

  function import($source) {
    // TODO
  }

  function export($tree) {
    $walker = $this->walker;

    echo "EXPORTING!\n";
    $walker->walk($tree, function($node) {
      echo "On node $node\n";
    });

    // TODO
  }
}
?>
