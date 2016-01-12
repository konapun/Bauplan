<?php
namespace Bauplan\Language;

use Bauplan\Language\StateMachine\PushdownMachine as PushdownMachine;

abstract class Parser {

  /*
   * The rules method takes the start node of a state machine and sets up all
   * other nodes and transitions.
   */
  abstract protected function rules($stateMachine);

  final function parse($tokens) {
    $pushdownMachine = new PushdownMachine();
    $this->rules($pushdownMachine);

    $state = PushdownMachine::START;
    foreach ($tokens as $token) {
      echo "Trying to transition from $state to " . $token->getType() . "\n";
      $pushdownMachine->transition($state, $token->getType());
      $state = $token->getType();
    }
  }
}
?>
