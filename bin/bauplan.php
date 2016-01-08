<?php
include_once('Bauplan.php');
//use Bauplan\CLI;
use Bauplan\Language\Template\TemplateLexer as Lexer;
use Bauplan\Perf\Timer as Timer;
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
$tokenStream = $lexer->tokenize(file_get_contents($file));
$performance['lexer'] = $timer->getTimeSinceLastPoint();






if ($showPerf) {
  showPerformance($performance);
  die();
}

print "START TOKEN LIST:\n";
print "----------------\n";
foreach ($tokenStream as $token) {
  echo $token->getValue() . " (" . $token->getType() . ")\n";
}

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
