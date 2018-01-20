<?php

    if( (!isset($_GET['req']) || empty($_GET['req'])) ) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }
    
    $request = strip_tags(htmlentities($_GET['req']));

    $final = __DIR__."/../actions/".$request.".php";
    if( strpos($request, "+b3") !== false )
        $final = __DIR__."/../actions/b3/".str_replace("+b3", "", $request).".php";

    if( file_exists($final) ) {
        include $final;
    } else {
        header("HTTP/1.0 404 Not Found");
        exit;
    }
