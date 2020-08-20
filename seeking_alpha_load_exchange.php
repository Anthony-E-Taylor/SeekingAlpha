<?php
// Load exchange from daily dividend files in .\data to Database

$gsHelp = "
seeking_alpha_dividends.php - Extract the dividends' data for each SeekingAlpha stock and produce an .xml and .tsv file

Usage:    php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\yyyymmdd.xml      >  \\vest\\etf\\Dividends\\SeekingAplha\\yyyymmdd.tsv
          php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\yyyymmdd-pg*.xml  >  \\vest\\etf\\Dividends\\SeekingAplha\\yyyymmdd-pg*.tsv

Examples: php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\20200202.xml      >  \\vest\\etf\\Dividends\\SeekingAplha\\2020202.tsv
          php.5.4  seeking_alpha_dividend.php   \\Vest\\etf\\Dividends\\SeekingAplha\\Data\\20200202-pg2.xml  >  \\vest\\etf\\Dividends\\SeekingAplha\\2020202-pg2.tsv
\n";

    include( 'header.html' );	   
    require('connect-db.php');
    require('seek-db.php');


            
            //load_stocks_table();
            //load_dividends_table();
            //load_shareholders_table();
            load_exchange_table();
            //load_exchange_table();

?>