<?php
namespace Bauplan\Type;

/*
 * A directive is executed during the compilation phase and can alter the
 * underlying type
 */
interface Directive {
  function registersAs(); // the name of this directive as exposed to the user
  function worksWith($type); // returns true or false depending on whether or not this directive can be used with the type that's trying to register it
  function register($type); // code called when this directive registers with a Type
  function execute($arglist); // what happens when this directive is called
}
?>
