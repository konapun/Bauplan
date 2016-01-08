<?php
// TODO: Autoload?
$base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Bauplan' . DIRECTORY_SEPARATOR;
$langBase = $base . 'Language' . DIRECTORY_SEPARATOR;
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

include_once($exceptionBase . 'IOException.php');
include_once($exceptionBase . 'SyntaxError.php');

include_once($roleBase . 'Cloneable.php');
include_once($roleBase . 'Renderable.php');
// TODO

// TODO: Instantiate runtime, return API tree

function __construct() {

}
?>
