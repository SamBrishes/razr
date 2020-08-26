HARX - HTML-annotated rendering extension
=========================================

HARX is a PHP 7.2 written HTML-annotated rendering and templating extension, based on PageKit's
[Razr](https://github.com/pagekit/razr) engine whose syntax was inspired by ASP.NET's Razor. Harx
contains the basic, still sightly changed, syntax from Razr and extends the environment with `<custom />`
HTML tags to make it easier to embed several identical elements.


Features
--------

-   Set, Get, Print and Work with **Variables** and **Constants**
-   Use **Conditional** Control Structures (`@if`, `@elif`, `@endif`)
-   Use **Loops** (`@each`, `@endeach`, `@for`, `@endfor`, `@while`, `@endwhile`)
-   Define Parts (`@part`, `@endpart`) and Blocks (`@block`, `@enblock`) of Code
-   Directly **Include** (`@include`), **Extend** (`@extend`) or **Embed** (`@embed`) Content
-   Register `<custom />` HTML tags, which gets rendered using your callback function.
-   **Caches everything** up to improve the performance and speed


### Differences to pagekit/razr

-   Requires PHP 7.2.x, Tested up To PHP 7.4.6
-   The `<custom />` HTML tags has been added
-   The `@elseif` directive has been replaced with `@elif`
-   The `@foreach` directive has been replaced with `@each`
-   The `@for` directive has been added
-   The `@continue` and `@break` Loop directives has been added
-   The `@embed` directive has been added
-   The `@part` directive has been added
-   A new configuration structure and environment
-   A new caching system
-   A new `Larx` called single-file drop-in library
-   And a few more changes...


Versions
--------

The HARX extension contains 2 different versions:

- **Harx** - `Harx\Harx`
- **Larx** -`Larx\Larx`

HARX contains the whole environment and functionallity, provided by this library, while Larx is a
single-file drop-in version which is perfectly capable for really small projects or even plugins.
Of course, both support the whole HARX syntax as described on the [documentation](https://harx.io/docs).

### Differences

Larx ...

-   ... doesn't contain custom Exceptions
-   ... doesn't contain the Loader interfaces
-   ... use a smaller caching system ...
-   ... and doesn't cache per default
-   ... is build within one class
-   ... offers just a bunch of public methods


Requirements
------------

The HARX library just requires **PHP 7.2.0** (or above), no dependencies.


Performance
-----------

The Harx library is build for speed and tries, at least, to be faster than Twig and Blade, while
still offering an extensive environment, providing a small single-file drop-in version called Larx.
Check out the Performance Table below, more information can be found on [harx.io/performance](https://harx.io/performance).


Basic Usage
-----------

As you may have seen, we'are using the `.xhtml` extension instead of `.razr` to allow the basic
HTML syntax working on your favourite Editor and IDE, but the files are - of course - not valid
XHTML files at all. We're working on additional extensions for ATOM and Visual Studio Code to
support the future used `.hxht` (**h**ar**x** **ht**ml) extension.

```php
<?php
    // Create a new Instance
    $harx = new Harx\Harx([
        "cache" => true
    ]);

    // Create Variables
    $array = [
        "variable"  => 12
    ];

    // Create Classes
    $article = new Article(1);

    // Render your file, Pass the variables you need
    $harx->render("./index.xhtml", [
        "name"      => "My Name",
        "data"      => $array,
        "article"   => $article
    ]);
```

### Syntax

- [@( )](https://harx.io/docs/@)
- [@e( )](https://harx.io/docs/@e)
- [@escape( )](https://harx.io/docs/@escape)
- [@raw( )](https://harx.io/docs/@raw)
- [@dump( )](https://harx.io/docs/@dump)
- [@json( )](https://harx.io/docs/@json)
- [@upper( )](https://harx.io/docs/@upper)
- [@lower( )](https://harx.io/docs/@lower)
- [@format( )](https://harx.io/docs/@format)
- [@replace( )](https://harx.io/docs/@replace)
- [@set( )](https://harx.io/docs/@set)
- [@get( )](https://harx.io/docs/@get)
- [@constant( )](https://harx.io/docs/@constant)
- [@if( ) - @elif( ) - @else( ) - @endif( )](https://harx.io/docs/@if)
- [@while( ) - @endwhile( )](https://harx.io/docs/@while)
- [@for( ) - @endfor( )](https://harx.io/docs/@for)
- [@each( ) - @endeach( )](https://harx.io/docs/@each)
- [@continue( )](https://harx.io/docs/@continue)
- [@break( )](https://harx.io/docs/@break)
- [@part( ) - @endpart( )](https://harx.io/docs/@part)
- [@block( ) - @endblock( )](https://harx.io/docs/@block)
- [@include( )](https://harx.io/docs/@include)
- [@embed( )](https://harx.io/docs/@embed)
- [@extend( )](https://harx.io/docs/@extend)


Copyright & License
-------------------

Published under the MIT-License; Copyright © 2020 SamBrishes, pytesNET

This is a Fork of [PageKit's Razr Library](https://github.com/pagekist/razr) (Version 0.10.0),
developed back in 2014 and published under the MIT-License as well.
