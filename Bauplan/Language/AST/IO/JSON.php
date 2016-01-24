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
    return json_encode($this->convert($tree));
  }

  private function convert($tree) {
    $data = $tree->getData();
    $node = array(
      //'type'     => $data->getType(), // FIXME change this once we have the actual AST. Actual properties will be from attributes field, like PHPparser
      'value'    => $data,
      'children' => array()
    );
    foreach ($tree->getChildren() as $child) {
      array_push($node['children'], $this->convert($child));
    }
    return $node;
  }
}
?>
