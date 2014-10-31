<?php
include_once('Bauplan.php');
use Bauplan\Compiler\Parser as Parser;

$parser = new Parser();
$tree = $parser->parseFile('t/test1.bau');
var_dump($tree);
print "DONE\n";
?>
