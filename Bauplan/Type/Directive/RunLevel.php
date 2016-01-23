<?php
namespace Bauplan\Type\Directive;

/*
 * Runlevels define at which point a directive is run. Because multiple
 * directives can be loaded into a type and the user shouldn't be responsible
 * for specifying them in the correct order, directives set a runlevel to
 * establish the correct order.
 *
 * Directives with lower runlevels are run before those with higher levels.
 */
class RunLevel {
  const RL_1 = 1;
  const RL_2 = 2;
  const RL_3 = 3;
  const RL_4 = 4;
  const RL_5 = 5;
}
?>
