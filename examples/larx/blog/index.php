<?php
/*
 |  LARX SIMPLE BLOG EXAMPLE
 */
    require_once "../../../src/Larx/Larx.php";
    require_once "article.php";

    // Get Larx
    use Larx\Larx;

    // Init Larx
    $larx = new Larx([realpath(__DIR__ . "/../../includes/xhtml/")], [
        "cache"         => false,
        "cache_path"    => __DIR__ . DIRECTORY_SEPARATOR . "cache"
    ]);

    // Pass Includes Path
    $larx->set(Larx::TYPE_GLOBAL, "includes_path", "../../includes/");

    // Pass Pseudo Blog Data
    $larx->set(Larx::TYPE_GLOBAL, "blog", [
        "url"           => "https://harx.io",
        "title"         => "My Blog Title",
        "description"   => "This is just an example to demonstrate the Harx templating engine using a common Blog environment.",
        "dateformat"    => "H:i -- d. F, Y"
    ]);

    // Pass Pseudo User Data
    $larx->set(Larx::TYPE_GLOBAL, "is_logged_in", false);
    $larx->set(Larx::TYPE_GLOBAL, "user", [
        "username"  => "Harx User"
    ]);

    // Pass Pseudo WhereAmI View
    if(isset($_GET["post"]) && $_GET["post"] === 1) {
        $larx->set(Larx::TYPE_GLOBAL, "view", "post");
        $larx->set(Larx::TYPE_GLOBAL, "post", new Article([
            "title"     => "Hello World!",
            "author"    => "Harx User",
            "content"   => "This is just a Lorem-Ipsum demonstration article, to show how the Harx templating engine uses them within the .xhtml template files.",
            "date"      => date("Y-m-d H:i:s")
        ]));
    } else if(!isset($_GET["post"])) {
        $larx->set(Larx::TPE_GLOBAL, "view", "home");
        $larx->set(Larx::TPE_GLOBAL, "posts", [
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
    $larx->set(Larx::TYPE_TAG, "harx:header", function(array $attributes = [ ], string $content = "", string $return = "") {
        if(empty($content) && empty($attributes)) {
            return "1";
        } else if(empty($content)) {
            return "2";
        }
        return "3";
    });

    // Pass Pseudo Function
    $larx->set(Larx::TYPE_FUNCTION, "copyright", function() {
        return '<p>Copyright &copy; ' . date("Y") . ' <a href="https://harx.io">Harx.io</a></p>';
    });

    // Print Larx
    $larx->print("template.xhtml");
