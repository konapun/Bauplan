<?php
// TODO: Autoload?
$base = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Bauplan' . DIRECTORY_SEPARATOR;
$langBase = $base . 'Language' . DIRECTORY_SEPARATOR;
$exceptionBase = $base . 'Exception' . DIRECTORY_SEPARATOR;
$roleBase = $base . 'Role' . DIRECTORY_SEPARATOR;
$typeBase = $base . 'Type' . DIRECTORY_SEPARATOR;
$perfBase = $base . 'Perf' . DIRECTORY_SEPARATOR;

include_once($perfBase . 'Timer.php');

//include_once($base . 'Type.php');
include_once($base . 'DirectiveLoader.php');

include_once($langBase . 'StateMachine' . DIRECTORY_SEPARATOR . 'Node.php');
include_once($langBase . 'StateMachine' . DIRECTORY_SEPARATOR . 'NodeAdapter.php');
include_once($langBase . 'StateMachine' . DIRECTORY_SEPARATOR . 'PDA.php');

include_once($langBase . 'AST' . DIRECTORY_SEPARATOR . 'Node.php');
include_once($langBase . 'AST' . DIRECTORY_SEPARATOR . 'NodeFactory.php');
include_once($langBase . 'AST' . DIRECTORY_SEPARATOR . 'IO' . DIRECTORY_SEPARATOR . 'IO.php');
include_once($langBase . 'AST' . DIRECTORY_SEPARATOR . 'IO' . DIRECTORY_SEPARATOR . 'JSON.php');

include_once($langBase . 'Lexer.php');
include_once($langBase . 'Parser.php');
include_once($langBase . 'Token.php');
include_once($langBase . 'Directive' . DIRECTORY_SEPARATOR . 'DirectiveLexer.php');
include_once($langBase . 'Directive' . DIRECTORY_SEPARATOR . 'DirectiveToken.php');
include_once($langBase . 'Template' . DIRECTORY_SEPARATOR . 'TemplateLexer.php');
include_once($langBase . 'Template' . DIRECTORY_SEPARATOR . 'TemplateToken.php');
include_once($langBase . 'Template' . DIRECTORY_SEPARATOR . 'TemplateParser.php');

include_once($exceptionBase . 'IOException.php');
include_once($exceptionBase . 'SyntaxError.php');
include_once($exceptionBase . 'ParseException.php');

// TODO: Instantiate runtime, return API tree

function __construct() {

}
?>
