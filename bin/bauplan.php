<?php
include_once('Bauplan.php');
//use Bauplan\CLI;
use Bauplan\Language\Template\TemplateLexer as Lexer;
//use Bauplan\Compiler\Compiler as Compiler;
//use Bauplan\Compiler\SyntaxTreeExporter\ArrayExporter as TreeExporter;

$file = 't/template.bau';
if (count($argv) > 1) {
  $file = $argv[1];
}
$lexer = new Lexer();
$tokenStream = $lexer->tokenize(file_get_contents($file));
print "START TOKEN LIST:\n";
print "----------------\n";
foreach ($tokenStream as $token) {
  echo $token->getValue() . " (" . $token->getType() . ")\n";
}
?>
