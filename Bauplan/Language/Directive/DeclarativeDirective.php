<?php
namespace Bauplan\Language\Directive;

/*
 * Allow building of directives from PHP hashes
 *
 * Example:
 * 	return array(
 *
 * 	)
 */
abstract class DeclarativeDirective implements Directive {
  private $declarative;

  final function __construct() {
    $declarative = $this->DeclarativeDirective();
    $this->validate($declarative);

    $this->declarative = $declarative;
  }

  /*
   * Return a hash with the following field values:
   * 	[REQUIRED]
   * 	- registersAs: The name that will invoke the directive when called from Bauplan code
   * 	- execute: This directive's action
   * 	[OPTIONAL]
   * 	(TODO)
   */
  abstract protected function declareDirective();

  function registersAs() {
    return $this->declarative['registersAs'];
  }

  function execute($args) {
    return $this->declarative['execute']($args);
  }

  private function validate($hash) {
    // TODO: Make sure all keys exist
  }
}
 ?>
