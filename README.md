# Bauplan
Bauplan is a modular, general-purpose templating system for PHP (like so, so many others). The goal of this project is mainly to play around with creating a simple language from scratch and end with a usable product.

## Syntax
The BNF grammar for Bauplan is located [here](bnf/grammar.bnf). Logic is mainly performed through user-definable directives which eliminates the need for logic operations in the core language.

###Example
    ```
    $$PREPROCESSOR-DIRECTIVE EXAMPLE
    
    ;;(
      This is a block comment. This example shows a bauplan grammar which is
      parseable by the current version.
    ;;)
    *(template-identifier {
          directive1: "directive argument 1", "directive argument 2", 3
        | directive2: "one argument"
        | directive-with-no-args
      }
      
      @(section { ;; this is an inline comment
          section-directive-example
        }
        
        "this is the section body"
      )
      
      $(var-1 {value: "test" | readonly}) ;; a variable
    )
    ```

The PHP API into Bauplan provides a number of different operations depending on the data type (template, section, variable, etc.)

## Project status
- [x] Create grammar
- [x] Implement lexer
- [x] Implement parser
- [ ] Implement core language on top of AST, including PHP API
- [ ] Define directives
- [ ] Usable product
