<?php
namespace Bauplan\Language\StateMachine;

/*
 * Nodes are used internally by the state machine. All user interactions with
 * the state network are handled by the state machine using node IDs, not nodes
 * themselves.
 */
 class Node implements NodeAdapter {
   private $id;
   private $transitions;

   function __construct($id) {
     $this->id = $id;
     $this->transitions = array();
   }

   function getID() {
     return $this->id;
   }

   function addTransition($node) {
     $this->transitions[$node->getID()] = true;
   }

   function transition($node) {
     if (array_key_exists($node->getID(), $this->transitions)) {
       return $node;
     }
     return null;
   }

   function __toString() {
     return $this->id;
   }
 }
?>
