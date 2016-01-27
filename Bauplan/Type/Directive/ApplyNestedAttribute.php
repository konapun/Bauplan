<?php
namespace Bauplan\Type\Directive;
use Bauplan\Type\Diretive\SectionDirective as SectionDirective;

/*
 * Applies a list of directives to all children of the section:
 *
 * @(lambda {apply-nested-attribute: "readonly"}
 *   $(var1-ronly {value: "VAR 1"})
 *   $(var2-ronly {value: "VAR 2"})
 *   $(var3-ronly {value: "VAR 3"})
 * )
 */
class Value extends SectionDirective {

  function registersAs() {
    return 'apply-nested-attribute';
  }

  function register($variable) {

  }

  function execute($args) {

  }
}
?>
