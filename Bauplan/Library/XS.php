<?php
namespace Bauplan\Library;

/*
 * Interface that must be implemented to define a Bauplan library in PHP
 */
abstract class XS {

  /*
   * Returns an array of function names to their implementation, where each
   * function takes an arguments object
   */
  abstract protected function exportFunctions();

  function compile() {
    foreach ($this->exportFunctions() as $fn) {
      // TODO
    }
  }
}
?>
