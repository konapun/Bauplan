<?php
namespace Bauplan\Language\StateMachine;

/*
 * A node can be anything that implements this interface. It will be needed in
 * order to use tokens as nodes in the PDA.
 */
interface NodeAdapter {

  /*
   * The ID for an adapted node is whatever the value used for transitions
   * is. In the case of the parser, the ID will be the token's type.
   */
  function getID();

}
?>
