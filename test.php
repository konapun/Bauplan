<?php
include_once('Bauplan.php');
use Bauplan\Compiler\Parser as Parser;

$parser = new Parser();
$tokens = $parser->parseFile('t/test1.bau');
print "DONE\n";
?>
