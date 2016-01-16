<?php
include_once('./Bauplan.php');

use Bauplan\Language\StateMachine\PDA as PDA;
use Bauplan\Language\Template\TemplateLexer as Lexer;
use Bauplan\Language\Template\TemplateToken as Token;

// Example PDA whose language is matched parentheses and square brackets
$pda = new PDA();

/* Transitions */
$pda->addTransition(PDA::START, PDA::ACCEPT);
$pda->addTransition(PDA::START, array('(', '['));
$pda->addTransition(array('(', '[', ']', ')'), array('(', '[', ']', ')'));

$pda->stackMatch(')', '('); // when ) is encountered, pop and expect from stack
$pda->stackMatch(']', '[');

$good = str_split('()([])');
$bad = str_split('()|');
foreach ($bad as $token) {
  $pda->transition($token);
}
 ?>
