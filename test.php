<?php
include_once('Bauplan.php');
use Bauplan\Compiler\Parser as Parser;

$parser = new Parser();
$tree = $parser->parseFile('t/bad1.bau');
print "DONE\n";
?>
