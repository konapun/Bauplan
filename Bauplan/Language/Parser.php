<?php
namespace Bauplan\Language;

use Bauplan\Language\StateMachine\PDA as PDA;
use Bauplan\Exception\ParseException as ParseException;
use Bauplan\Language\AST\Node as AST;

abstract class Parser {
  const EPSILON = '__epsilon__';

  /*
   * The rules method takes the start node of a state machine and sets up all
   * other nodes and transitions.
   */
  abstract protected function rules($pda, $ast);

  /*
   * Instantiate the PDA by calling the concrete parser to establish rules.
   * Then, put the token array through the machine and attempt to transition to
   * the ACCEPT state after all tokens are exhausted. The output of this method
   * is the AST for the input tokens which is built by establishing transition
   * callbacks within the PDA
   */
  final function parse($tokens) {
    $pda = new PDA();
    $ast = new AST(self::EPSILON);
    $pda->onTransition(PDA::FAIL, function() {
      throw new ParseException(); // TODO: give a more informative error message
    });
    $this->rules($pda, $ast); // call to abstract function from concrete implementor

    $pda->reset();
    foreach ($tokens as $token) {
      $pda->transition($token);
    }
    $pda->transition(PDA::ACCEPT); // must end in accept state in order to be a valid parse
    return $ast;
  }
}
?>
