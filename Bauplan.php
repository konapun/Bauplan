<?php
// TODO: Autoload?
$base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Bauplan' . DIRECTORY_SEPARATOR;
$compilerBase = $base . 'Compiler' . DIRECTORY_SEPARATOR;
$exporterBase = $compilerBase . 'SyntaxTreeExporter' . DIRECTORY_SEPARATOR;
$exceptionBase = $base . 'Exception' . DIRECTORY_SEPARATOR;
$roleBase = $base . 'Role' . DIRECTORY_SEPARATOR;
$typeBase = $base . 'Type' . DIRECTORY_SEPARATOR;

//include_once($base . 'Type.php');
include_once($base . 'DirectiveLoader.php');

include_once($compilerBase . 'Lexer.php');
include_once($compilerBase . 'Parser.php');
include_once($compilerBase . 'SyntaxTree.php');
include_once($compilerBase . 'Token.php');
include_once($compilerBase . 'TreeTraversal.php');

include_once($exporterBase . 'TreeExporter.php');
include_once($exporterBase . 'ArrayExporter.php');
include_once($exporterBase . 'JSONExporter.php');

include_once($exceptionBase . 'IOException.php');
include_once($exceptionBase . 'LexerException.php');
include_once($exceptionBase . 'ParseException.php');

include_once($roleBase . 'Cloneable.php');
include_once($roleBase . 'Renderable.php');
// TODO

?>
