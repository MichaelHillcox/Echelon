<?php

    if( php_sapi_name() != 'cli' )
        die( "You can not use this in the browser" );

    system('clear');
    print "
Welcome to the Echelon Console
------------------------------

Here you can:
- [0] Generate a Echelon Unique Key\r\n";

    print "\r\n> ";
    $input = getInput();

    if( $input == "0" ) {

        

    }
    else
        print "Bye";

    function getInput() {
        $stdin = fopen('php://stdin', 'r');
        return fgetc($stdin);
    }