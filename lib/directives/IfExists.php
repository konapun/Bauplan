<?php
use Bauplan\Directive as Directive;

/*
 * A directive to conditionally display a section's contents based on certain
 * criteria
 *
 * Example:
 * 	*(template
 * 		@(lambda { display: false }
 * 			$(var1)
 * 			$(var2)
 * 		)
 *
 * 		@(lambda {if-exists: var1}
 * 			Var1 exists
 * 		)
 * 		@(lambda {if-exists: var3}
 * 			Won't display because var3 doesn't exist within the template scope
 * 		)
 * 	)
 */
class IfExists extends SectionDirective {

  function registersAs() {
    return "if-exists";
  }

  function install($arguments) {

  }

  function configure() {
    $this->acceptsAnyArguments(); // if this isn't set, you can still do checks yourself on the $arguments object in the execute call
  }

  function execute($arguments) {
    $section = $this->getDirectiveOwner();

    $arguments->each(function($arg) use ($section) {
      if ($section->hasInScope($arg))
    });
  }
}
?>
