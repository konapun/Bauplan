<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\StateMachine\PDA as PDA;
use Bauplan\Language\Parser as Parser;
use Bauplan\Language\Directive\DirectiveToken as DirectiveToken; // FIXME: parse separately through directiveparser
use Bauplan\Language\AST\NodeFactory as NodeFactory;

class TemplateParser extends Parser {

  /*
   * Define grammar in terms of pushdown machine transitions. Note that
   * transitions are defined in terms of current symbol and pushdown stack state.
   */
  protected function rules($pda, $ast) {
    $pda->addTransition(PDA::START, PDA::ACCEPT); // an empty token sequence is valid bauplan source

    $templateTypes = array(
      TemplateToken::T_TEMPLATE,
      TemplateToken::T_SECTION,
      TemplateToken::T_CODE,
      TemplateToken::T_INSTRUCTION,
      TemplateToken::T_VARIABLE
    );
    $directiveTypes = array(
      DirectiveToken::T_STRING,
      //DirectiveToken::T_IDENTIFIER, // FIXME: in order to refer to template types as variables
      DirectiveToken::T_NUMBER,
      DirectiveToken::T_TRUE,
      DirectiveToken::T_FALSE
    );
    $identifiers = array(
      TemplateToken::T_IDENTIFIER,
      TemplateToken::T_LAMBDA
    );
    $body = array_merge(
      $templateTypes,
      array(
        TemplateToken::T_LITERAL_STRING,
        TemplateToken::T_ANY
      )
    );

    $pda->addTransition(PDA::START, TemplateToken::T_TEMPLATE);
    $pda->addTransition($templateTypes, TemplateToken::T_TYPE_OPEN);
    $pda->addTransition(TemplateToken::T_TYPE_OPEN, $identifiers);
    $pda->addTransition(TemplateToken::T_DIRECTIVE_START, array(DirectiveToken::T_KEY, DirectiveToken::T_PIPE, TemplateToken::T_DIRECTIVE_END));
    $pda->addTransition(DirectiveToken::T_KEY, array(DirectiveToken::T_COLON, TemplateToken::T_DIRECTIVE_END));
    $pda->addTransition(array(DirectiveToken::T_COLON, DirectiveToken::T_COMMA), array(DirectiveToken::T_STRING, DirectiveToken::T_NUMBER, DirectiveToken::T_TRUE, DirectiveToken::T_FALSE));
    $pda->addTransition(array(DirectiveToken::T_STRING, DirectiveToken::T_NUMBER, DirectiveToken::T_TRUE, DirectiveToken::T_FALSE), array(DirectiveToken::T_COMMA, TemplateToken::T_DIRECTIVE_END, DirectiveToken::T_PIPE));
    $pda->addTransition(DirectiveToken::T_PIPE, DirectiveToken::T_KEY);
    $pda->addTransition(TemplateToken::T_DIRECTIVE_END, array_merge($body, array(TemplateToken::T_TYPE_CLOSE)));
    $pda->addTransition($body, TemplateToken::T_TYPE_CLOSE);
    $pda->addTransition(TemplateToken::T_TYPE_CLOSE, TemplateToken::T_TYPE_CLOSE);
    $pda->addTransition($identifiers, array(TemplateToken::T_DIRECTIVE_START, TemplateToken::T_TYPE_CLOSE)); //  directive blocks are optional
    $pda->addTransition(TemplateToken::T_TYPE_CLOSE, $body);
    $pda->addTransition($body, $body);

    // TODO
    $pda->addTransition(TemplateToken::T_TYPE_CLOSE, PDA::ACCEPT);

    $pda->stackMatch(TemplateToken::T_TYPE_CLOSE, TemplateToken::T_TYPE_OPEN);
    $this->setActions($pda, $ast);
  }

  private function setActions($pda, $ast) {
    $currNode = $ast; // start at epsilon
    $pda->onTransition(function($from, $to) use (&$ast) {
      echo "Transitioning from $from to $to\n";
    });
    $pda->onTransition(PDA::FAIL, function($from, $to) {
      echo "Failed while attempting to transition from $from to $to\n";
    });
    $pda->onTransition(PDA::ACCEPT, function() {
      echo "Transitioned to ACCEPT!\n";
    });
  }
}
?>
