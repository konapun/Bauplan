<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\StateMachine\PushdownMachine as PushdownMachine;
use Bauplan\Language\Parser as Parser;

class TemplateParser extends Parser {

  /*
   * Define grammar in terms of pushdown machine transitions. Note that
   * transitions are defined in terms of current symbol and pushdown stack state.
   */
  protected function rules($pm) {
    $pm->setTransition(PushdownMachine::START, PushdownMachine::ACCEPT); // an empty token sequence is valid bauplan source

    $pm->setTransition(PushdownMachine::START, TemplateToken::T_TEMPLATE);
    $pm->setTransition(TemplateToken::T_TEMPLATE, TemplateToken::T_TYPE_OPEN);
    $pm->setTransition(TemplateToken::T_TYPE_OPEN, array(TemplateToken::T_IDENTIFIER, TemplateToken::T_LAMBDA));
    $pm->setTransition(TemplateToken::T_DIRECTIVE_START, TemplateToken::T_DIRECTIVE_END);
    $pm->setTransition(array(TemplateToken::T_IDENTIFIER, TemplateToken::T_LAMBDA), array(TemplateToken::T_DIRECTIVE_START, TemplateToken::T_TYPE_CLOSE)); //  directive blocks are optional

    // TODO
    $pm->setTransition(TemplateToken::T_TYPE_CLOSE, PushdownMachine::ACCEPT);

    $this->setActions($pm);
  }

  private function setActions($pm) {
    $pm->onTransitionTo(PushdownMachine::ACCEPT, function() {
      echo "Transitioned to ACCEPT!\n";
    });
  }
}
?>
