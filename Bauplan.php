<?php
// TODO: Autoload?
$base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Bauplan' . DIRECTORY_SEPARATOR;
$langBase = $base . 'Language' . DIRECTORY_SEPARATOR;
$compilerBase = $base . 'Compiler' . DIRECTORY_SEPARATOR;
$exporterBase = $compilerBase . 'SyntaxTreeExporter' . DIRECTORY_SEPARATOR;
$exceptionBase = $base . 'Exception' . DIRECTORY_SEPARATOR;
$roleBase = $base . 'Role' . DIRECTORY_SEPARATOR;
$typeBase = $base . 'Type' . DIRECTORY_SEPARATOR;

//include_once($base . 'Type.php');
include_once($base . 'DirectiveLoader.php');

include_once($langBase . 'TokenStream.php');
include_once($langBase . 'Compiler.php');
include_once($langBase . 'Lexer.php');
include_once($langBase . 'Parser.php');
include_once($langBase . 'Token.php');
include_once($langBase . 'Template' . DIRECTORY_SEPARATOR . 'TemplateLexer.php');
include_once($langBase . 'Template' . DIRECTORY_SEPARATOR . 'TemplateToken.php');

include_once($compilerBase . 'Lexer.php');
include_once($compilerBase . 'Parser.php');
include_once($compilerBase . 'Compiler.php');
include_once($compilerBase . 'Token.php');
include_once($compilerBase . 'TokenStream.php');
include_once($compilerBase . 'TreeTraversal.php');
include_once($compilerBase . 'CST' . DIRECTORY_SEPARATOR . 'Node.php');

include_once($exporterBase . 'TreeExporter.php');
include_once($exporterBase . 'ArrayExporter.php');
include_once($exporterBase . 'JSONExporter.php');

include_once($exceptionBase . 'IOException.php');
include_once($exceptionBase . 'SyntaxError.php');

include_once($roleBase . 'Cloneable.php');
include_once($roleBase . 'Renderable.php');
// TODO

// TODO: Instantiate runtime, return API tree

function __construct() {

}
?>
