<?php
include_once('Bauplan.php');
use Bauplan\Compiler\Compiler as Compiler;
use Bauplan\Compiler\SyntaxTreeExporter\ArrayExporter as TreeExporter;

$compiler = new Compiler();
$tree = $compiler->compile('t/complex-vals.bau'); // FIXME: tree won't be compiler's output eventually
$exporter = new TreeExporter();
$exporter->exportTree($tree);
print "DONE\n";
?>
