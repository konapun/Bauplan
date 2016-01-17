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

$pda->onTransition(function($from, $to) {
  echo "Transitioning from $from to $to!\n";
});
$pda->onTransition(PDA::FAIL, function() {
  echo "FAILED!\n";
});

$good = '()([])';
$bad = '())))|';

$input = $good;
foreach (str_split($input) as $token) {
  $pda->transition($token);
  echo "After transition, state is " . $pda->getState() . "\n";
}
 ?>
