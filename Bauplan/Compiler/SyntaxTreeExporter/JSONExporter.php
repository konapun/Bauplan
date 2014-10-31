<?php
namespace Bauplan\Compiler\SyntaxTreeExporter;
use Bauplan\Compiler\SyntaxTreeExporter\TreeExporter as TreeExporter;
use Bauplan\Compiler\SyntaxTreeExporter\ArrayExporter as ArrayExporter;

/*
 * Exports a syntax tree in JSON format
 */
class JSONExporter implements TreeExporter {
  private $arrayExporter;
  
  function __construct() {
    $this->arrayExporter = new ArrayExporter();
  }
  
  function exportTree($tree) {
    $array = $this->arrayExporter->export($tree);
    return json_encode($array);
  }
}
?>
