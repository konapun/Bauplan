<?php

use Bauplan\Language\StateMachine\ProductionMachine as ProductionMachine;

class TemplateParser {

  function rules($pda, $ast) {
    $pm = new ProductionMachine($pda);
    $pm->set(array(
      ProductionMachine::START => $pm->getProduction('TEMPLATE'),

      /* Types */
      'Type'                 => function($pm) { // needs to be wrapped in a fn for deferred calls
                                  return array(
                                  array($pm->getProduction('Template')),
                                  array($pm->getProduction('Section')),
                                  array($pm->getProduction('Code')),
                                  array($pm->getProduction('Instruction')),
                                  array($pm->getProduction('Variable')),
                                );
                              },
      'Template'             => array(TemplateToken::T_TEMPLATE, TemplateToken::T_TYPE_OPEN, $pm->getProduction('Identifier'), $pm->getProduction('DirectiveBlock'), $pm->getProduction('Body'), TemplateToken::T_TYPE_CLOSE),
      'Section'              => array(TemplateToken::T_SECTION, TemplateToken::T_TYPE_OPEN, $pm->getProduction('Identifier'), $pm->getProduction('DirectiveBlock'), $pm->getProduction('Body'), TemplateToken::T_TYPE_CLOSE),
      'Code'                 => array(TemplateToken::T_CODE, TemplateToken::T_TYPE_OPEN, $pm->getProduction('Identifier'), $pm->getProduction('DirectiveBlock'), $pm->getProduction('Body'), TemplateToken::T_TYPE_CLOSE),
      'Instruction'          => array(TemplateToken::T_INSTRUCTION, TemplateToken::T_TYPE_OPEN, $pm->getProduction('Identifier'), $pm->getProduction('DirectiveBlock'), $pm->getProduction('Body'), TemplateToken::T_TYPE_CLOSE),
      'Variable'             => array(TemplateToken::T_VARIABLE, TemplateToken::T_TYPE_OPEN, $pm->getProduction('Identifier'), $pm->getProduction('DirectiveBlock'), TemplateToken::T_TYPE_CLOSE),

      'Identifier'           => array(
                                  array(TemplateToken::T_IDENTIFIER),
                                  array(TemplateToken::T_LAMBDA)
                                ),
      'Body'                 => array(
                                  array($pm->getProduction('Type'), $pm->getProduction('Body')),
                                  array(TemplateToken::T_LITERAL_STRING, $pm->getProduction('Body')),
                                  array(ProductionMachine::EPSILON)
                                ),

      /* Directives */
      'DirectiveBlock'       => array(
                                  array(TemplateToken::T_DIRECTIVE_START, $pm->getProduction('Directives'), TemplateToken::T_DIRECTIVE_END), // {}
                                  array(ProductionMachine::EPSILON) // empty production - directive not required
                                ),
      'Directives'           => array(
                                  array(DirectiveToken::T_KEY),
                                  // TODO
                                  array(ProductionMachine::EPSILON) // directive body not required
                                ),
      'Directive'            => array(
                                  array($pm->getProduction('UnvaluedDirective')),
                                  array($pm->getProduction('ValuedDirective'))
                                ),
      'UnvaluedDirective'    => array(DirectiveToken::T_KEY),
      'ValuedDirective'      => array(DirectiveToken::T_KEY, DirectiveToken::T_COLON, $pm->getProduction('DirectiveValues')),

      'DirectiveValues'      => array($pm->getProduction('DirectiveValue'), $pm->getProduction('DirectivesValueList')),
      'DirectivesValueList'  => array(
                                  array(DirectiveToken::T_COMMA, $pm->getProduction('DirectiveValues')),
                                  array(ProductionMachine::EPSILON)
                                )

    ));
  }
}
?>
