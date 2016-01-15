<?php

/*
 * Example of using DeclarativeDirective to create a directive from a hash
 */
class Metadata extends DeclarativeDirective {

  function declareDirective() {
    return array(
      "package"     => "bauplan",
      "registersAs" => "metadata",
      "execute"     => function($vars) {
        $runtime = $this->runtime();
        $tag = $vars->getFirst();
        return $runtime->metadata($tag);
      }
    );
  }
}
?>
