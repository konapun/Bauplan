<?php
namespace Bauplan\Language\Template;

use Bauplan\Language\Token as Token;

class TemplateToken extends Token {
  const T_PREPROC_DECL = 'preprocessor delcaration';
  const T_TEMPLATE = 'template sigil';
  const T_SECTION = 'section sigil';
  const T_VARIABLE = 'variable sigil';
  const T_CODE = 'code sigil';
  const T_INSTRUCTION = 'instruction sigil';
  const T_TYPE_OPEN = 'type open';
  const T_TYPE_CLOSE = 'type close';
  const T_DIRBLOCK_OPEN = 'directive block open';
  const T_DIRBLOCK_CLOSE = 'directive block close';
  const T_DIR_KEYVAL_SEP = 'directive key:value separator';
  const T_DIRECTIVE_SEP = 'directive separator';
  const T_VAL_SEP = 'value separator';
  const T_INTEGER = 'integer';
  const T_DOUBLE = 'double';
  const T_BOOLEAN = 'boolean';
  const T_QUOTED_STRING = 'quoted string';
  const T_IDENTIFIER = 'identifier';
  const T_LITERAL = 'literal';
  const T_STRING = 'string';
  const T_LAMBDA = 'lambda';
}
?>
