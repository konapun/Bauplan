<?php
namespace Bauplan\Type\Directive;

class DefInstruction extends CodeDirective {

  function registersAs() {
    'def-instruction';
  }

  /*
   * Set the highest runlevel because actually creating the instruction should
   * be the last operation done
   */
  function runLevel() {
    return RunLevel::RL_5;
  }

  function execute() {
    $runtime = $this->getRuntime();
    $typenode = $this->getOwner();

    $namespace = $runtime->getNamespaceFor($typenode);
    $id = $typenode->getIdentifier(); // the ID is used as the instruction name

    $instruction = new Instruction($id);
    
    // TODO magic...

    $namespace->inject($instruction); // make it
  }
}
?>
