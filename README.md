PHP-Rapidgen
============

### Why?

PHP Parser is an awesome tool but falls a little flat in the code generation area.
The templating is basic and the syntax in general is quite verbose. Not something
you'd want to hand-write or maintain. Also, for some cases, you need a stupid,
fast fallback.

### How?

PHP-Rapidgen does two things:

1. Mix PHP-Parser and handlebars.php, with canonical template traversal, helpers
2. Introduce a Shorthand JSON syntax for PHP Parser

Handlebars is a great way to write a lot of static code that might need just a
touch of dynamic replacements. PHP Parser is great for super-accurate generation
of code.

PHP-Rapidgen uses PHP-Parser as its native source, while also allowing for handlebars
templates. Furthermore, you can mix and match them to your heart's content with the
one caveat that calling handlebars templates from within a PHP-Parser template
can be quite resource intensive as it has to first convert the template back
into a PHP-Parser AST.

In general, it is preferable to work in this hierarchy:

```
output <-- handlebars.php (for simple stuff) <--- PHP-Parser (for complex stuff)
                                              \-- Helpers (also based on PHP-Parser)
```

This is made possible by a canonical template traversal on the one hand (see below
for examples). On the other hand, helpers are natively written in PHP-Parser Syntax
(well, a shorthand version) and can also be used from both sources.

Finally, PHP-Rapidgen also handles context canonically such that both handlebars
and PHP-Parser derive data from the same source.

Example
=======

*Your Project*
```
project
|-- templates
|    |-- main.handlebars
|    |-- main.json
|    +-- simple.php
|-- context.json
+-- generate.php
```

As an example, both the handlebars and the json file produce the same output.

*main.handlebars*
```handlebars
<?php
{{#credits class.credits}}
class my_class_{{class.id}} extends {{class.parent}}
{
	public function __construct()
	{
		return {{#array class.info}};
	}
}
```

Notes:
- #array calls on the same canonical helper as `{"h.array":...}` below
- The main benefit here is object traversal when compared to native PHP-Parser templates

*main.json*
```json
[
{
	"f.class":[
		{"h.concat":["my_class_",{"c":"class.id"}]},
		{
			"stmts":[
				{"f.method":["__construct",{"stmts":[{"st.Return":[{"h.array":{"c":"class.info"}}]}]}]},
			],
			"extend":{"c":"class.parent"}
		},
		{
			"comments":[{"h.credits":{"c":"class.credits"}}]
		}
	]
}
]
```

Notes:
- The basic structure is always `{"command":[/* input */]}`
- The input for each command corresponds with the PHP Parser method arguments
- The dot notation emulates javascript syntax for object traversal
- Shorthands are used extensively, ie. f is the `PHPParser_BuilderFactory`
- `h` is for helpers, `c` is used to call from the context
- `t` can be used to call in another template file

*context.json*
```json
{
	"class": {
		"id": "myname",
		"info": {
			"name": "My Class",
			"type": "a class"
		},
		"parent": "myparent",
		"credits": {
			"author": "David Deutsch",
			"package": "PHP-Rapidgen",
			"copyright": "(c) David Deutsch 2013",
			"license": "GNU GPL v3.0"
		}
	}
}
```

*generate.php*
```php
RapidGenerator::configure();

RapidGenerator::context(
	json_decode( file_get_contents(__DIR__.'/context.json')
);

RapidGenerator::convert(
	__DIR__.'/templates/main.json',
	__DIR__.'/output.php'
)
```
