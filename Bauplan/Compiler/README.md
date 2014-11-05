# Bauplan Compiler
Compilation takes place in the following steps:
  - Preprocessor (eventual)
    - Preprocessor lexer runs -> preprocessor tokens
    - Preprocessor parse tree built
    - Preprocessor parse tree -> AST
    - Preprocessor code generation
  - Main
    - Lexer -> Tokens
    - Parser -> Concrete syntax tree
    - Concrete syntax tree -> Abstract syntax tree transformation/optimization
    - TODO

## Details
Parse tree structure:
  * internal nodes - nonterminals
  * leaves - tokens

AST structure:
  * internal nodes - constructor functions
  * leaves - atoms