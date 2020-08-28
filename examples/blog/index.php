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
    $harx->setGlobal("view", "post");           // <!-- Change this to "home", to show the homepage
    $harx->setGlobal("post", new Article([
        "title"     => "Hello World!",
        "author"    => "Harx User",
        "content"   => "This is just a Lorem-Ipsum demonstration article, to show how the Harx templating engine uses them within the .xhtml template files.",
        "date"      => date("Y-m-d H:i:s")
    ]));

    // Pass Pseudo Function
    $harx->setFunction("greetings", function() {

        if(date("H"))

        return "Good to see you!";
    });

    // Print Harx
    $harx->print("template.xhtml");
