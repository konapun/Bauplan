<?php
namespace Bauplan\Language;

use Bauplan\Language\StateMachine\PDA as PDA;
use Bauplan\Exception\ParseException as ParseException;

abstract class Parser {

  /*
   * The rules method takes the start node of a state machine and sets up all
   * other nodes and transitions.
   */
  abstract protected function rules($pda);

  final function parse($tokens) {
    $pda = new PDA();
    $pda->onTransition(PDA::FAIL, function() {
      throw new ParseException();
    });
    $this->rules($pda); // call to abstract function from concrete implementor

    $pda->reset();
    foreach ($tokens as $token) {
      $pda->transition($token->getType());
    }
    $pda->transition(PDA::ACCEPT); // must end in accept state in order to be a valid parse
  }
}
?>
