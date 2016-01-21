<?php
namespace Bauplan\Language\StateMachine;

/*
 * Create PDA transitions using production rules
 */
class ProductionMachine {
  private $productions;

  const START = '__start__';
  const EPSILON = '__epsilon__'; // symbol for empty productions

  function __construct() {
    $this->productions = array();
  }

  function getProduction($name) {
    return $this->productions[$name]; // TODO: Error checking
  }

  /*
   * Create rules for a single production
   */
  function createProduction($name, $rules) {
    $this->productions[$name] = $parsedRules;
  }

  /*
   * Create a block of partitions.
   * Each rule in the partition value is specified by an array defining
   * sequential transitions. "OR" transitions are specified by nesting rules
   * inside an array.
   *
   *  // example machine which defines a grammar for adding or subtracting two numbers
   * 	$pm->createProduction(array(
   *  	PushdownMachine::START => array($pm->getProduction('Expression')),
   *  	'Expression' => array(Token::NUMBER, $pm->getProduction('Operation'), Token::Number),
   *  	'Operation'  => array(
   *  										array(Token::PLUS),
   *  										array(Token::MINUS)
   *  									)
   * 	));
   */
  function createProductions($productions) {
    foreach ($productions as $name => $rules) {
      $this->createProduction($name, $rules);
    }
  }

  /*
   * Add transitions specified by these productions into a PDA
   */
  function createTransitions($pda=null) {
    if (is_null($pda)) {
      $pda = new PDA();
    }

    foreach ($this->productions as $name => $rules) {
      $pda->addTransition($from, $to); // FIXME
    }

    return $pda;
  }
}
?>
