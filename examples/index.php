<?php

require __DIR__.'/autoload.php';

use Harx\Harx;
use Harx\Loader\FilesystemLoader;

// simple array
$array = array();
$array['title'] = 'I am the walrus';
$array['artist'] = array('name' => 'The Beatles', 'homepage' => 'http://www.thebeatles.com');

// simple object
$object = new stdClass;
$object->title = 'I am the walrus';
$object->artist = array('name' => 'The Beatles', 'homepage' => 'http://www.thebeatles.com');

// article object
$article = new Article('My article', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Me');

// render template
$harx = new Harx(new FilesystemLoader([__DIR__]));

function hello($str) { echo "Hello ".$str; };

// $razr->addFunction('hello', 'hello');

echo $harx->render('template.xhtml', array(
    'name'    => 'World',
    'pi'      => 3.14159265359,
    'number'  => -5,
    'now'     => new DateTime,
    'array'   => $array,
    'object'  => $object,
    'article' => $article
));
