<?php
/*
 |  HARX SIMPLE BLOG EXAMPLE
 */
    require_once "../../src/autoload.php";
    require_once "article.php";

    // Get Harx
    use Harx\Harx;

    // Init Harx
    $harx = new Harx(Harx::fileLoader([__DIR__ . DIRECTORY_SEPARATOR . "xhtml"]), [
        "cache"         => false,
        "cache_path"    => __DIR__ . DIRECTORY_SEPARATOR . "cache"
    ]);

    // Pass Pseudo Blog Data
    $harx->setGlobal("blog", [
        "url"           => "https://harx.io",
        "title"         => "My Blog Title",
        "description"   => "This is just an example to demonstrate the Harx templating engine using a common Blog environment.",
        "dateformat"    => "H:i -- d. F, Y"
    ]);

    // Pass Pseudo User Data
    $harx->setGlobal("is_logged_in", false);    // <-- Change this to true, to show the user
    $harx->setGlobal("user", [
        "username"  => "Harx User"
    ]);

    // Pass Pseudo WhereAmI View
    if(isset($_GET["post"]) && $_GET["post"] === 1) {
        $harx->setGlobal("view", "post");
        $harx->setGlobal("post", new Article([
            "title"     => "Hello World!",
            "author"    => "Harx User",
            "content"   => "This is just a Lorem-Ipsum demonstration article, to show how the Harx templating engine uses them within the .xhtml template files.",
            "date"      => date("Y-m-d H:i:s")
        ]));
    } else if(!isset($_GET["post"])) {
        $harx->setGlobal("view", "home");
        $harx->setGlobal("posts", [
            new Article([
                "id"        => 4,
                "title"     => "Hello World!",
                "author"    => "Harx User",
                "content"   => "This is just a Lorem-Ipsum demonstration article, to show how the Harx templating engine uses them within the .xhtml template files.",
                "date"      => date("Y-m-d H:i:s")
            ]),
            new Article([
                "id"        => 3,
                "title"     => "Hello World!",
                "author"    => "Harx User",
                "content"   => "This is just a Lorem-Ipsum demonstration article, to show how the Harx templating engine uses them within the .xhtml template files.",
                "date"      => date("Y-m-d H:i:s")
            ]),
            new Article([
                "id"        => 2,
                "title"     => "Hello World!",
                "author"    => "Harx User",
                "content"   => "This is just a Lorem-Ipsum demonstration article, to show how the Harx templating engine uses them within the .xhtml template files.",
                "date"      => date("Y-m-d H:i:s")
            ]),
            new Article([
                "id"        => 1,
                "title"     => "Hello World!",
                "author"    => "Harx User",
                "content"   => "This is just a Lorem-Ipsum demonstration article, to show how the Harx templating engine uses them within the .xhtml template files.",
                "date"      => date("Y-m-d H:i:s")
            ])
        ]);
    }

    // Pass Pseudo Custom Tag
    $harx->setTag("harx:header", function(array $attributes = [ ], string $content = "", string $return = "") {
        if(empty($content) && empty($attributes)) {
            return "1";
        } else if(empty($content)) {
            return "2";
        }
        return "3";
    });

    // Pass Pseudo Function
    $harx->setFunction("copyright", function() {
        return '<p>Copyright &copy; ' . date("Y") . ' <a href="https://harx.io">Harx.io</a></p>';
    });

    // Print Harx
    $harx->print("template.xhtml");
