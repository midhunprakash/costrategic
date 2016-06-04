<?php

    require __DIR__.'/src/bootstrap.php';

    use phpcrawler\Crawler;

    if(php_sapi_name () == 'cli')
    {
        //0 => No errors, 1 => Only errors 2=> Show warnings and notices too
        switch(DEBUG) {
            case 0:
                error_reporting(E_ALL);
                break;
            case 1:
                error_reporting(E_ERROR);
                break;
            case 2:
                error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
                break;
        }

        if (isset($argv[1])) {
            $url =  $argv[1];
        } else {
            $url = URL;
        }
        //Instantiate the crawler
        $crawler = new phpcrawler\Crawler($url);
    }else{
        die("Only CLI access is allowed");
    }
