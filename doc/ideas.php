<?php
use Bauplan\Language\TokenStream as TokenStream;

$tokens = $lexer->tokenize($source);

$stream = new TokenStream($tokens);
$sm = new StateMachine();
$start = $sm->start();
$template = $start->transition('template');
$typeBegin = $template->transition('type_begin');
$identifier = $typeBegin->transition('identifier');
$directive = $identifier->transition('directive');
$body = $identifier->transition('body');
$directive->transition($body);

$fsm->start('template')
    ->transition('type_begin')
    ->transition('identifier')
    ->or(function($sm) {
      $sm->transition('directive')
    })
$fsm->onMatch(function($token) {

});
$fsm->onFail(function($token) {

});
$fsm->start();
 ?>
