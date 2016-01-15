<?php
namespace Bauplan\Language\StateMachine;

use Bauplan\Language\StateMachine\Node as Node;
use Bauplan\Exception\StateException as StateException;

/*
 * The pushdown machine is exposed to the parser which sets up states,
 * transitions, and transition actions in the form of callbacks in order to
 * build the parse tree.
 *
 * The public API for the machine exists in terms of ID strings which map to
 * nodes in the network, which the internal network itself uses state machine
 * nodes which are not exposed publicly.
 *
 * Reference: https://en.wikipedia.org/wiki/Pushdown_automaton
 */
class PushdownMachine {

  const START = '__start__';
  const ERROR = '__error__';
  const ACCEPT = '__accept__';

  private $state;
  private $stack;
  private $network;
  private $callbacks;

  function __construct() {
    $this->stack = array();
    $this->network = array();
    $this->callbacks = array(
      '__all__' => array(
        'to'   => array(),
        'from' => array()
      )
    );

    /* Create initial set of states for the network */
    $start = new Node(self::START, $this->network);
    $accept = new Node(self::ACCEPT, $this->network);
    $error = new Node(self::ERROR, $this->network);

    $this->state = $start;
  }

  /*
   * Reset the machine to its initial state
   */
  function reset() {
    $this->state = $this->getState(self::START);
  }

  /*
   * TODO: Allow multiple matches?
   */
  function addStackMatch($open, $close) {
    $stack = $this->stack;
    $this->onTransitionTo($open, function() use (&$stack, $open) {
      array_push($stack, $open);

    });
    $this->onTransitionTo($close, function() use (&$stack, $open) {
      $symbol = array_pop($stack);
      if ($symbol != $open) {
        echo "FAIL!\n";
      }
    });
  }

  /*
   * Create transitions from one or more states to one or more states
   */
  function setTransition($from, $to) {
    if (!is_array($from)) $from = array($from);
    if (!is_array($to)) $to = array($to);

    foreach ($from as $fromTransition) {
      foreach ($to as $toTransition) {
        $this->setSingleTransition($fromTransition, $toTransition);
      }
    }
  }

  /*
   * The `setTransition` method can take multiple states as $from and $to. This
   * simplifies it by only allowing a single transition per call
   */
  private function setSingleTransition($from, $to, $stackManipulationFunction=null) {
    if (is_null($stackManipulationFunction)) $stackManipulationFunction = function() {}; // NOP
    try {
      $from = $this->getState($from);
    }
    catch (StateException $e) {
      $from = new Node($from, $this->network);
    }
    try {
      $to = $this->getState($to);
    }
    catch (StateException $e) {
      $to = new Node($to, $this->network);
    }

    $from->setTransition($to);
  }

  /*
   * Attempt to transition from the current state to $state, if such a
   * transition is defined. Else, transition to the error state.
   */
  function transition($state) {
    echo "Current state is " . $this->state . "\n";
    echo "Transitioning to $state\n";
    echo "------------------------\n";
    $source = $this->state;
    $dest = $state;
    if (!$this->getState($state)) { // automatically transition to the error state if an unknown state is given
      $dest = self::ERROR;
    }

    // FIXME: transition callbacks
    foreach ($this->callbacks['__all__']['from'] as $callback) {
      $callback($dest);
    }
    if (array_key_exists($state, $this->callbacks)) {
      foreach ($this->callbacks[$state]['from'] as $callback) {
        $callback();
      }
    }

    array_push($this->stack, $source);
    $this->state = $this->state->transition($state); // make the actual transition

    if (array_key_exists($dest, $this->callbacks)) {
      foreach ($this->callbacks['__all__']['to'] as $callback) {
        $callback($dest); // FIXME
      }
      foreach ($this->callbacks[$dest]['to'] as $callback) {
        $callback();
      }
    }
  }

  /*
   * Set callback to be run when
   */
  function onTransitionTo($id, $callback) {
    $state = $this->getState($id);
    if (!array_key_exists($state->getID(), $this->callbacks)) {
      $this->callbacks[$state->getID()] = array(
        'from' => array(),
        'to'   => array()
      );
    }
    array_push($this->callbacks[$state->getID()]['to'], $callback);
  }

  function onTransitionFrom($id, $callback) {
    $state = $this->getState($id);
    if (!array_key_exists($state->getID(), $this->callbacks)) {
      $this->callbacks[$state->getID()] = array(
        'from' => array(),
        'to'   => array()
      );
    }
    array_push($this->callbacks[$state->getID()]['from'], $callback);
  }

  /*
   * Returns a node contained in the ntwork by its ID. If no such node exists
   * within the network, a StateException is thrown.
   */
  private function getState($id) {
    if (array_key_exists($id, $this->network)) {
      return $this->network[$id];
    }
    else {
      throw new StateException("No such state '$id'");
    }
  }
}
