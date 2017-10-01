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

        $config = __DIR__."/app/config.php";

        // Check if the file exists
        if( !file_exists($config) )
            die( "Looks like you need to install echelon. Go to yourdomain/install" );

        include __DIR__."/app/config.php";
        if( defined( 'UNIQUE_KEY' ) )
            die( "Looks like you already have unique key. You should never attempt to regenerate this\r\n" );

        $file = file_get_contents($config);

        $echKey = hash( "sha256", SES_SALT.time().DBL_PASSWORD );
        $file .= "\r\ndefine('UNIQUE_KEY', '$echKey');";

        file_put_contents( $config, $file );
        print "Your unique key has been generated and added to your config.\r\n";
    }
    else
        print "Bye";

    print "Bye";

    function getInput() {
        $stdin = fopen('php://stdin', 'r');
        return fgetc($stdin);
    }