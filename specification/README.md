# The php-fmt spec

This spec will attempt to enumerate the general formatting decisions that php-fmt
will make. The list may not be exhaustive, and the test cases in this repo
may provide more context over all. When appropriate, decisions as to why
formatting works in each case is listed.

Some parts of this may be customizable eventually, but not initially.


## Golden rule
* php-fmt should NEVER change any of the semantics of your program.

Can't stress how important that is. It also guides certain design decisions about
issues that php-fmt will not correct.
An additional rule might be that 'php-fmt is not a linter'. While it may solve
many issues a linter might care about, there are others it will not change, much
because of the golden rule.


## Line length limit
* There is no hard length limit, as php-fmt is not a linter. If you really want
140 char variable names, go for it.
* There is a soft limit of 131 chars. php-fmt will break certain constructs longer
than this (e.g. method calls, definitions, arrays)

## Strings

I call out strings first because they're actually a special case. php-fmt makes
no formatting decisions whatsoever for strings (most relevantly, multiline strings).

Why? Because it's impossible to move strings without in some way changing
their semantics. php-fmt cannot know whether you have 8 tabs in your string
for indentation purposes or because you wanted 8 tabs in your string. Strings
will be preserved exactly as is.

That said, I do have some general recommendations for strings:
* Use single quotes for all strings without variable interpolation
* Use the concatenation operator `.` when trying to format multiline strings
* Some special strings, e.g. SQL in PDO should not be concatenated together.
* Heredoc and Newdoc should be avoided at all costs. This sort of formatting is
bash sadness sort of formatting. (I am not sure how php-fmt will handle these yet, if at all)


## Whitespace
* 4 spaces will be used for indentation
* Lines will have no trailing whitespace.
* The file will end in a new line.
* Outside of strings, php-fmt will preserve intentional newlines (up to 1).
* The first and last line within blocks will never be a new line
* Class inner-definitions will start and with a new line.

To explain "up to 1", if more than one intentional newline is in some place,
it will become one newline.

## Braces
* php-fmt uses the [one-true-brace-style](https://en.wikipedia.org/wiki/Indent_style#Variant:_1TBS).
* This means the if/else statements, loops, etc will ALWAYS have a block, and will never be inline

A popular debate. I don't agree with the "typical" php style. In my opinion, braces on newlines
are wasted space that don't contribute to readability.


## Comments
* Comments will never be inline, and will be hoisted upwards by a line.
* (Currently not working) Comments in blocks with no other logic should remain =/ See issue #4

## Loops
* Loops are regular blocks.
* Empty loops will have full blocks, and the end of those blocks will be on a new line.
  * This should help call attention to the fact that you have an empty loop
  * Use a linter if you want more attention drawn to this
* Not 100% sure on this yet. An argument can easily made going with the cleaner
single line loop here. Empty loops either way are no bueno though.


## Control Flow
* Empty else blocks will be removed.
* The same is not true for if, elseif, as that would change semantics.


## Some day
* A tricky problem, but in an ideal world php-fmt would hoist inline assignment
out of if/else statements, loops, function calls, etc. Inline assignment makes
code far harder to reason about.
