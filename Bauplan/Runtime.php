<?php
namespace Bauplan;

/*
 * Set up constants, load directives, etc.
 */
class Runtime {
  private $directiveLoader;

  function __construct() {
    $this->directiveLoader = new DirectiveLoader();
  }

  function run() {

  }

  /*
   * Load directives into appropriate types. Types will then be cloned instances
   * of the type which loaded the directives
   */
  private function loadDirectives() {
    $directives = $this->directiveLoader->loadAll();
  }
}
?>
