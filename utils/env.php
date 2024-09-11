<?php

namespace Utils;



class Env {


    protected $protectedPath = __DIR__;

    public static function loadEnv(){
        {

            $basePath = dirname(__FILE__,2);
            $env_path = $basePath.'/.env';


            if (!file_exists($env_path)) {
                throw new Exception(".env file not found");
            }
        
            $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
            foreach ($lines as $line) {
                // Ignore commented lines
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
        
                // Split line into key and value
                list($key, $value) = explode('=', $line, 2);
        
                // Remove surrounding quotes from value, if any
                $value = trim($value, '"\' ');
        
                // Set environment variables
                putenv(sprintf('%s=%s', $key, $value));
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }


    }




}






?>