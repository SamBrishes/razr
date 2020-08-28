<?php
/*
 |  HARX AUTOLOADER FOR PEOPLE WHO DON'T USE COMPOSER
 |  @file       ./Harx.php
 |  @author     SamBrishes <sam@pytes.net>
 |  @version    0.1.0 [0.1.0] - Alpha
 |
 |  @website    https://github.com/pytesNET/Harx
 |  @license    X11 / MIT License
 |  @copyright  Copyright © 2020 pytesNET <info@pytes.net>
 |
 |  @fork       This is a PHP-7.2 fork of razr made by PageKit @ 2014
 |              https://github.com/pagekit/razr
 */

    require_once "constants.php";
    
    spl_autoload_register(function(string $class): void {
        $path = realpath(__DIR__) . DIRECTORY_SEPARATOR;
        require_once strtr($path . $class, "\\", DIRECTORY_SEPARATOR) . ".php";
    });
