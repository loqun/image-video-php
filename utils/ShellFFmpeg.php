<?php 

    namespace Utils;

    class ShellFFmpeg{

        static function play($path){


            $videoPath = $path;
            $command = getenv('FFPLAY_PATH')." -i " . escapeshellarg($videoPath) . " -autoexit";
            
            // Execute the command
            exec($command, $output, $return_var);
            
            // Optionally handle the output or return status
            if ($return_var === 0) {
                echo "Video playback started successfully.";
            } else {
                echo "An error occurred during playback.";
            }


        }







    }




?>