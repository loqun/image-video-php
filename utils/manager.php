<?php

namespace Utils;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class Manager{

    public function createInstance($driver){

        // it either gd or imagick 
        try{
            return $manager = ($driver == 'imagick') ? new ImageManager(new Driver()) : new ImageManager(new GdDriver) ;    
        }finally{
            echo "\ndone setup the driver";
        }
       
    }

    public function compressAllImage($instance, $allFiles, $compressedPath){

        if(!$instance instanceof ImageManager){

            echo "\n".'error';   
            return;
        }   

        $percentage = 50;
        $sizeMap = [];

        //read and generate the encoded image to a new folder 
        foreach($allFiles as $file){

            if(is_file($file)){


                //get the file name and record the old size
                $FileInfo = pathinfo($file);
                $filenameWithoutExtension = $FileInfo['filename']; 
                $filename = 'compressed_'.basename($filenameWithoutExtension);
                $oldSize = filesize($file);

                $readedImage = $instance->read($file);

                
                $readedImage->toWebp($percentage)->save($compressedPath.'/'."compressed_$filenameWithoutExtension.webp");
                $compressedSize = filesize($compressedPath.'/'."compressed_$filenameWithoutExtension.webp");

                //record the compressed size
                $sizeMap[$oldSize] = $compressedSize;
            }


        }
        $count = 1;
        echo "\nFinished compressed the image\nShowing the result\n";

        foreach($sizeMap as $key => $value){

            echo "Old size for image $count is $key and after compressed is $value. The file decreased to ". $value/$key*100 ."\n";
            $count++;
        }
    }


    public function getColorProfileICCFromImage($instance){

        $iccPath = dirname(__DIR__)."/toColorProfile";
        $exportPath = dirname(__DIR__).'/toColorProfile/color_profile/';

        if(!is_dir($iccPath)){
            mkdir($iccPath, 0777, true);
        }
        if(!is_dir($exportPath)){
            mkdir($exportPath, 0777, true);
        }

        $allFiles = glob(dirname(__DIR__)."/toColorProfile/*");
        //get the collor profile from image 
        foreach($allFiles as $file){
            if(is_file($file)){

                //get the file name and record the old size
                $FileInfo = pathinfo($file);
                $filenameWithoutExtension = $FileInfo['filename']; 
                $filename = 'readed'.basename($filenameWithoutExtension);

                $readedImage = $instance->read($file);
                $readedImage->profile()->save($exportPath."$filenameWithoutExtension.icc");

                echo "\n$filenameWithoutExtension.icc exported";

            }
        }
    }



}





?>