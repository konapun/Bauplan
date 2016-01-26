<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\StateMachine\PDA as PDA;
use Bauplan\Language\AST\Node as Node;
use Bauplan\Language\Parser as Parser;
use Bauplan\Language\Directive\DirectiveToken as DirectiveToken; // FIXME: parse separately through directiveparser

/*
 * Set up node transitions and built an AST.
 *
 * This parser works by first building a parse tree which is then traversed in a
 * second step to build the parse tree
 */
class TemplateParser extends Parser {

  /*
   * Define grammar in terms of pushdown machine transitions. Note that
   * transitions are defined in terms of current symbol and pushdown stack state.
   */
  protected function rules($pda, $ast) {
    $productions = $this->getProductions();

    $pda->addTransition(PDA::START, PDA::ACCEPT); // an empty token sequence is valid bauplan source
    $pda->addTransition(PDA::START, TemplateToken::T_TEMPLATE);
    $pda->addTransition($productions['TEMPLATE_TYPES'], TemplateToken::T_TYPE_OPEN);
    $pda->addTransition(TemplateToken::T_TYPE_OPEN, $productions['IDENTIFIERS']);
    $pda->addTransition(TemplateToken::T_DIRECTIVE_START, array(DirectiveToken::T_KEY, DirectiveToken::T_PIPE, TemplateToken::T_DIRECTIVE_END));
    $pda->addTransition(DirectiveToken::T_KEY, array(DirectiveToken::T_COLON, TemplateToken::T_DIRECTIVE_END));
    $pda->addTransition(array(DirectiveToken::T_COLON, DirectiveToken::T_COMMA), array(DirectiveToken::T_STRING, DirectiveToken::T_NUMBER, DirectiveToken::T_TRUE, DirectiveToken::T_FALSE));
    $pda->addTransition(array(DirectiveToken::T_STRING, DirectiveToken::T_NUMBER, DirectiveToken::T_TRUE, DirectiveToken::T_FALSE), array(DirectiveToken::T_COMMA, TemplateToken::T_DIRECTIVE_END, DirectiveToken::T_PIPE));
    $pda->addTransition(DirectiveToken::T_PIPE, DirectiveToken::T_KEY);
    $pda->addTransition(TemplateToken::T_DIRECTIVE_END, array_merge($productions['BODY'], array(TemplateToken::T_TYPE_CLOSE)));
    $pda->addTransition($productions['BODY'], TemplateToken::T_TYPE_CLOSE);
    $pda->addTransition(TemplateToken::T_TYPE_CLOSE, TemplateToken::T_TYPE_CLOSE);
    $pda->addTransition($productions['IDENTIFIERS'], array_merge(array(TemplateToken::T_DIRECTIVE_START, TemplateToken::T_TYPE_CLOSE), $productions['BODY'])); //  directive blocks are optional
    $pda->addTransition(TemplateToken::T_TYPE_CLOSE, $productions['BODY']);
    $pda->addTransition($productions['LITERALS'], $productions['BODY']);
    $pda->addTransition($productions['BODY'], $productions['BODY']);
    $pda->addTransition(TemplateToken::T_TYPE_CLOSE, PDA::ACCEPT);

    $pda->stackMatch(TemplateToken::T_TYPE_CLOSE, TemplateToken::T_TYPE_OPEN);

    $that = $this;
    $parseTree = $this->buildParseTree($pda);
    $pda->onTransition(PDA::ACCEPT, function() use ($that, &$parseTree, &$ast) {
      echo "BUILDING AST!\n";
      $that->_buildAST($parseTree, $ast);
    });
  }

  /*
   * Automaton traversal actions which build the parse tree
   */
  private function buildParseTree($pda) {
    $productions = $this->getProductions();

    $tree = new Node(Parser::EPSILON); // the root of the parse tree
    $currParent = $tree; // start at root
    $pda->onTransition(function($node) use (&$tree, &$currParent) {
      $type = is_object($node) ? $node->getType() : $node;
      switch ($type) {
        case TemplateToken::T_TEMPLATE:
        case TemplateToken::T_SECTION:
        case TemplateToken::T_CODE:
        case TemplateToken::T_INSTRUCTION:
        case TemplateToken::T_VARIABLE:
          $currParent = $currParent->addChild($node);
          break;

        case TemplateToken::T_TYPE_CLOSE:
          $currParent = $currParent->getParent();
          break;

        // Nodes to ignore which don't belong in the AST
        case PDA::START:
        case PDA::ACCEPT:
        case PDA::FAIL:
        case TemplateToken::T_TYPE_OPEN:
          break;

        default:
          $currParent->addChild($node);
          break;
      }
    });

    /* Debug */
    /*
    $currNode = "(empty)";
    $pda->onTransition(function($to) use (&$tree, &$currNode) {
      echo "Transitioning from '$currNode' to '$to'\n";
      $currNode = $to;
    });
    $pda->onTransition(PDA::FAIL, function($to) {
      echo "Failed while attempting to transition to $to\n";
    });
    $pda->onTransition(PDA::ACCEPT, function() {
      echo "Transitioned to ACCEPT!\n";
    });
    */
    return $tree;
  }

  /*
   * Convert the parse tree into an AST
   */
  public function _buildAST($parseTree, $ast) {
    foreach ($parseTree->getChildren() as $node) {
      $token = $node->getData();

      $this->_buildAST($node, $ast->addChild($token->getValue()));
    }
  }

  /*
   * Pseudo-LHS productions
   */
  private function getProductions() {
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
    $literals = array(
      TemplateToken::T_LITERAL_STRING,
      TemplateToken::T_ANY
    );
    $body = array_merge(
      $templateTypes,
      $literals
    );

    return array(
      'TEMPLATE_TYPES'  => $templateTypes,
      'DIRECTIVE_TYPES' => $directiveTypes,
      'IDENTIFIERS'     => $identifiers,
      'LITERALS'        => $literals,
      'BODY'            => $body
    );
  }
}
?>
