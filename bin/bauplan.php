<?php
include_once('Bauplan.php');
//use Bauplan\CLI;
use Bauplan\Perf\Timer as Timer;
use Bauplan\Language\Template\TemplateLexer as Lexer;
use Bauplan\Language\Template\TemplateParser as Parser;
use Bauplan\Language\AST\IO\JSON as JSONIO;
//use Bauplan\Compiler\Compiler as Compiler;
//use Bauplan\Compiler\SyntaxTreeExporter\ArrayExporter as TreeExporter;

$options = getopt("lprth::");

if (array_key_exists('h', $options)) { // help
  echo help();
  exit(0);
}

$timer = new Timer();
$showPerf = array_key_exists('r', $options);
$performance = array(
  'lexer'    => -1,
  'parser'   => -1,
  'compiler' => -1
);

$file = $argv[count($argv)-1]; // take input from command line

$timer->createPoint('lexer');
$lexer = new Lexer();
$tokens = $lexer->tokenize(file_get_contents($file));
$performance['lexer'] = $timer->getTimeSinceLastPoint();

echo "HERE\n";
if ($showPerf) {
  showPerformance($performance);
  die();
}

if (array_key_exists('l', $options)) {
  echo "Tokens\n";
  echo "------\n";
  foreach ($tokens as $token) {
    echo $token->getValue() . " (" . $token->getType() . ")\n";
  }
}

$parser = new Parser();
$ast = $parser->parse($tokens);

$exporter = new JSONIO();
$exporter->export($ast);

function help() {
  return <<<EOS
Usage: bauplan.php [OPTIONS] <file|source>
Options:
  -l              Lex only
  -p              Parse only
  -t <string>     Output abstract syntax tree in the given format (available
                  ormats are php, json, bauplan)
  -r              Give performance measures for each step
  -h              Show help

EOS;
}

function showPerformance($measures) {
  echo "Performance\n";
  echo "-----------\n";
  foreach ($measures as $key => $time) {
    if ($time > 0) {
      echo ucfirst($key) . ": $time\n";
    }
  }
}
?>
