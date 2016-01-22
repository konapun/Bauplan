<?php
namespace Bauplan\Language\StateMachine;

use Bauplan\Language\StateMachine\Node as Node;
use Bauplan\Exception\StateException as StateException;

/*
 * A pushdown automaton
 *
 * This PDA dynamically builds its alphabet based on transitions such that every
 * transition to or from a nonexistant node creates the missing nodes. The stack
 * is only used for stackMatches while every other transition is the same as a
 * nondeterministic finite automaton.
 *
 * A failed transition will automatically go to the FAIL state which must be
 * caught by registering an onTransition callback.
 *
 * ex:
 * 	$pda->onTransition(PDA::FAIL, function() {
 * 		throw new StateException();
 * 	});
 */
class PDA {

  const START = '__start__';
  const ACCEPT = '__accept__';
  const FAIL = '__fail__';

  private $state;
  private $nodes;
  private $stack;
  private $events;

  function __construct() {
    $this->nodes = array();
    $this->stack = array();
    $this->events = array(
      '__all__' => array()
    );

    // Initial nodes
    $start = $this->getOrCreateNode(self::START);
    $accept = $this->getOrCreateNode(self::ACCEPT);
    $fail = $this->getOrCreateNode(self::FAIL);

    // Initial transitions
    $this->addTransition(self::FAIL, self::FAIL);
    $this->addTransition(self::ACCEPT, self::ACCEPT);

    $this->state = $start;
  }

  function getState() {
    return $this->state->getID();
  }

  function addTransition($id1, $id2) {
    if (!is_array($id1)) $id1 = array($id1);
    if (!is_array($id2)) $id2 = array($id2);

    foreach ($id1 as $from) {
      foreach ($id2 as $to) {
        $this->addSingleTransition($from, $to);
      }
    }
  }

  /*
   * Set a function to run when triggered by a transition to the node(s)
   * specified by $id, which may be an array. If no IDs are given, set the
   * callback to be invoked every transition.
   */
  function onTransition($id, $fn=null) {
    if (is_null($fn)) {
      $fn = $id;
      $id = '__all__';
    }
    else if (!is_array($id)) {
      $id = array($id);
    }

    if (is_array($id)) {
      foreach ($id as $nodeID) {
        $this->onSingleTransition($nodeID, $fn);
      }
    }
    else {
      $this->onSingleTransition($id, $fn);
    }
  }

  /*
   * When the node with ID $id1 is seen, pop the stack and expect $id2
   */
  function stackMatch($id1, $id2) {
    $node1 = $this->getOrCreateNode($id1);
    $node2 = $this->getOrCreateNode($id2);

    $that = $this;
    $stack = $this->stack;
    $this->onTransition($id1, function() use ($that, $id2, &$stack) {
      $symbol = array_pop($stack);
      if ($symbol != $id2) {
        $that->addTransition($that->getState(), self::FAIL);
        $that->transition(self::FAIL);
      }
    });
    $this->onTransition($id2, function() use ($id2, &$stack) {
      array_push($stack, $id2);
    });
  }

  function reset() {
    $this->state = $this->getOrCreateNode(self::START);
  }

  /*
   * The transition function attempts to make a transition from the current node
   * to the node specified by $id. If the transition doesn't exist for any
   * reason, including no such node in the network, an automatic transition to
   * the FAIL state is made, for which there are no other available transitions.
   *
   * Use transition events to catch error state.
   */
  function transition($id) {
    $node = $id;
    if (is_object($id)) { // transitioning from a NodeAdapter
      $id = $node->getID();
    }

    if (array_key_exists($id, $this->nodes) && !is_null($this->nodes[$this->state->getID()]->transition($this->nodes[$id]))) {
      $this->state = $this->nodes[$id];
    }
    else {
      $this->state = $this->nodes[self::FAIL];
    }

    $to = $this->state->getID();
    foreach (array_merge($this->events[$to], $this->events['__all__']) as $event) {
      $event($node);
    }
    return $to;
  }

  private function addSingleTransition($id1, $id2) {
    $node1 = $this->getOrCreateNode($id1);
    $node2 = $this->getOrCreateNode($id2);

    $node1->addTransition($node2);
  }

  /*
   * Set a function to run when triggered by a transition to the node with id
   * $id.
   */
  private function onSingleTransition($id, $fn=null) {
    $node = $this->getOrCreateNode($id);
    array_push($this->events[$id], $fn);
  }

  /*
   * Return the node specified by the given ID if it exists. Else, create it,
   * add it to the network, add an event slot for it, and return it.
   */
  private function getOrCreateNode($id) {
    if (array_key_exists($id, $this->nodes)) {
      return $this->nodes[$id];
    }

    $node = new Node($id);
    $this->nodes[$id] = $node;
    $this->events[$id] = array();
    return $node;
  }
}
?>
