<?php
include_once('Bauplan.php');
use Bauplan\Compiler\Parser as Parser;
use Bauplan\Compiler\SyntaxTreeExporter\ArrayExporter as TreeExporter;

$parser = new Parser();
$tree = $parser->parseFile('drivers/HTML5.bau');
$exporter = new TreeExporter();
$exporter->exportTree($tree);
print "DONE\n";
?>
