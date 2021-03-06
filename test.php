<?php
include_once('Bauplan.php');
use Bauplan\Compiler\Compiler as Compiler;
use Bauplan\Compiler\SyntaxTreeExporter\ArrayExporter as TreeExporter;

$file = 't/complex-vals.bau';
if (count($argv) > 1) {
  $file = $argv[1];
}
$compiler = new Compiler();
$tree = $compiler->compile($file); // FIXME: tree won't be compiler's output eventually
$exporter = new TreeExporter();
$exporter->exportTree($tree);
print "DONE\n";
?>
