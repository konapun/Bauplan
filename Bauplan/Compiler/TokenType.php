<?php

abstract class TokenType {
  const T_PREPROC_DECL = 0;
  const T_TEMPLATE = 1;
  const T_SECTION = 2;
  const T_VARIABLE = 3;
  const T_CODE = 4;
  const T_INSTRUCTION = 5;
  const T_TYPE_OPEN = 6;
  const T_TYPE_CLOSE = 7;
  const T_DIRBLOCK_OPEN = 8;
  const T_DIRBLOCK_CLOSE = 9;
  const T_DIR_KEYVAL_SEP = 10;
  const T_DIRECTIVE_SEP = 11;
  const T_VAL_SEP = 12;
  const T_INTEGER = 13;
  const T_DOUBLE = 14;
  const T_BOOL = 15;
  const T_QUOTED_STRING = 16;
  const T_IDENTIFIER = 17;
  const T_LITERAL = 18;
  const T_BAREWORD = 20;
}
?>
