<?php
include_once('./Bauplan.php');

use Bauplan\Language\StateMachine\PushdownMachine as PDA;
use Bauplan\Language\Template\TemplateLexer as Lexer;
use Bauplan\Language\Template\TemplateToken as Token;

// Example PDA whose language is matched parentheses and square brackets
$pda = new PDA();

/* Transitions */
$pda->setTransition(PDA::START, PDA::ACCEPT);
$pda->setTransition(PDA::START, array('(', '['));
$pda->setTransition(array('(', '[', ']', ')'), array('(', '[', ']', ')'));

$pda->addStackMatch('(', ')'); // when ) is encountered, pop and expect from stack
$pda->addStackMatch('[', ']');

$good = str_split('()([])');
$bad = str_split('()|');
foreach ($bad as $token) {
  $pda->transition($token);
}
 ?>
