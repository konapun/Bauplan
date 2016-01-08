<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\Token as Token;

class TemplateToken extends Token {
  const T_LITERAL_STRING = 'T_LITERAL_STRING';
  const T_LAMBDA = 'T_LAMBDA';
  const T_TEMPLATE = 'T_TEMPLATE';
  const T_SECTION = 'T_SECTION';
  const T_CODE = 'T_CODE';
  const T_VARIABLE = 'T_VARIABLE';
  const T_INSTRUCTION = 'T_INSTRUCTION';
  const T_TYPE_OPEN = 'T_TYPE_OPEN';
  const T_TYPE_CLOSE = 'T_TYPE_CLOSE';
  const T_STRING = 'T_STRING';
  const T_DIRECTIVE_STRING = 'T_DIRECTIVE_STRING';
  const T_IDENTIFIER = 'T_IDENTIFIER';
  const T_DIRECTIVE_START = 'T_DIRECTIVE_START';
  const T_DIRECTIVE_END = 'T_DIRECTIVE_END';
}
?>
