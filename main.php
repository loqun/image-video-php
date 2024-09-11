<?php

//HI this script for compressing the images and the videos in directory or something  
require_once __DIR__ . '/vendor/autoload.php';
use Utils\Manager;
use FFMpeg\FFMpeg;
use Utils\Env;
use Utils\ShellFFmpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;

    //set up the  dir for testing the file to compress 
    print("setting up the folder for the script execution \n");

    //load the env file
    try{
        Env::loadEnv();
    }catch( Exception $e ){

        echo $e;

    }finally{
        echo "done running script tp fetch env \n";
    }
    

    if(!is_dir('./compress')){

        print("\nThere is no folder to compress ");

        //so make the dir to compress 
        $dir = 'compress';

        if (mkdir("$dir", 0777, true)) {
            echo "Directory '$dir' created successfully.";
        } else {
            echo "Failed to create directory '$dir'.";
        }
    }else{
        print("Lessgo ");
    }

    $manager =  new Manager();
    $instances = $manager->createInstance('imagick');

    //get all images inside the compress folder     
    $allFiles = glob("./compress/*");

    if(count($allFiles) ==0 ){
        
        echo "\nThere is no file to compress";
        return;
    }

    echo "\n list all file in the compress folder";

    foreach($allFiles as $file){    
        
        echo "\n".$file;

    }


    //set the compressed file folder path
    $outDir = './compressed';

    if(!is_dir($outDir)){
        mkdir($outDir, 0777, true);
    }


    //compress all the item in the folder
    $manager->compressAllImage($instances, $allFiles, $outDir);

    echo "\n==========================================================\n";
    echo 'Trying the color profile';

    $manager->getColorProfileICCFromImage($instances);


    echo "\ntrying to use ffmpeg";

    $ffmpegPath = getenv('FFMPEG_PATH');
    $ffprobePath = getenv('FFPROBE_PATH');
    $ffplayPath = getenv('FFPLAY_PATH');



    $ffmpeg = FFMpeg::create([
        'ffmpeg.binaries'  => $ffmpegPath,
        'ffprobe.binaries' => $ffprobePath,
        'ffplay.binaries' => $ffplayPath,

    ]);

    echo "\n==========================================================\n";
    // echo 'Trying to play media using shell on ffmpeg bin'."\n";

    // ShellFFmpeg::play('./video/test.mp4');

    //convert format using ffmpeg 

    // $toConvertVideo = $ffmpeg->open('./video/test.mp4');

    // // create export dir
    // if(!is_dir('./exported')){
    //     mkdir('exported', 0777, true);
    // }

    // $toConvertVideo->save(new \FFMpeg\Format\Video\WebM(),  './exported/export_video.webm');

    // then compress the video

    // Open the video file
    $video = $ffmpeg->open('./video/test.mp4');

    // Set the desired bitrate and resolution
    $video->filters()->resize(new Dimension(1280, 720))->synchronize();
    $video->frame(TimeCode::fromSeconds(1))->save('./exported/frame.jpg'); // Optional: Extract a frame for preview

    // Save the compressed video
    $format = new X264('libmp3lame', 'libx264');
    $format->setVideoCodec('libx264')
        ->setAudioCodec('aac')
        ->setKiloBitrate(1000) // Set the video bitrate (e.g., 1000 kbps)
        ->setAudioKiloBitrate(128); // Set the audio bitrate (e.g., 128 kbps)

    if(!is_dir('./video_compressed')){
        mkdir('./video_compressed', 0777, true );
    }

    $video->save($format, './video_compressed/test.mp4');




?>
