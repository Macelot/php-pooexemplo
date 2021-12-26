<?php
//echo substr(getcwd(),0,30);
//echo $_SERVER['HTTP_HOST'];
if($_SERVER['HTTP_HOST']=="127.0.0.1:8033"){
  define('HOST', 'localhost');
  define('USER', 'root');
  define('PASS', 'usbw');
  define('BASE', 'test');
  define('PORT', '3307');
  // Change this to your connection info.
  $DATABASE_HOST = 'localhost';
  $DATABASE_USER = 'root';
  $DATABASE_PASS = 'usbw';
  $DATABASE_NAME = 'test';
}
