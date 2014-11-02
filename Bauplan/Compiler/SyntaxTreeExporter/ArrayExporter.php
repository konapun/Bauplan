<?php
namespace Bauplan\Compiler\SyntaxTreeExporter;
use Bauplan\Compiler\SyntaxTreeExporter\TreeExporter as TreeExporter;
use Bauplan\Compiler\TreeTraversal as TreeTraversal;

/*
 * Exports a syntax tree as a native PHP array
 */
class ArrayExporter implements TreeExporter {

  function __construct() {}

  function exportTree($tree) {
    $array = array();
    $walker = new TreeTraversal($tree);
    $walker->walk(TreeTraversal::TRAVERSE_BF, function($node) {
      $token = $node->getData();

      print "Got token " . $token->getValue() . "\n";
      // TODO
    });

    return $array;
  }
}
?>
