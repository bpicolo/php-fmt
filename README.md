# php-fmt
php-fmt is an in-progress code formatter built on top of php-parser, which builds
an AST (abstract-syntax-tree) for PHP code. php-parser is the precursor to PHP7s
builtin AST, and as such is a very well built bit of code.

As opposed to other parsers, which fix formatting mistakes, php-fmt
establishes one true format. That is, given equivalent code with any formatting
whatsoever, php-fmt will return the exact same result. As a result, it
should theoretically be impossible for it to create incorrect/invalid PHP.

It may at some point be customizable, but the goal is for it to be somewhat
opinionated, and not necessarily in-line with PSR-N, whose exact formatting opinions
I'm not a fan of. Not to mention, PSR-N leave wiggle room for exact formats in
certain areas, whereas php-fmt will spit out all code exactly the same
way, which leaves no room for fuzzy or optional formatting decisions.
