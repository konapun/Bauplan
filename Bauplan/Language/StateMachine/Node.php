<?php
namespace Bauplan\Language\StateMachine;

/*
 * Nodes are used internally by the state machine. All user interactions with
 * the state network are handled by the state machine using node IDs, not nodes
 * themselves.
 */
class Node {
  private $id;
  private $transitions;
  private $network;

  /*
   * Build a new state machine node with the given ID. The network is the full
   * list of nodes in the machine.
   */
  function __construct($id, &$network) {
    $this->id = $id;
    $this->transitions = array();

    $network[$id] = $this;
    $this->network = $network;
  }

  function getID() {
    return $this->id;
  }

  function setTransition($node) {
    $this->transitions[$node->getID()] = $node;
  }

  /*
   * Attempt to make a transition if it exists
   */
  function transition($id) {
    if (array_key_exists($id, $this->transitions)) {
      return $this->transitions[$id];
    }
    else {
      echo "FIXME: BAD TRANSITION IN NODE\n";
      //$this->transition(PushdownMachine::ERROR);
    }
  }

  function __toString() {
    return $this->getID();
  }
}
?>
