<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('AppController', 'Controller');

class UploadVideoAppController extends AppController {

    public $ffmpeg_path = "ffmpeg/ffmpeg";

    public function beforeFilter() {
        parent::beforeFilter();
        if (Configure::check('UploadVideo.video_ffmpeg_path') && Configure::read('UploadVideo.video_ffmpeg_path')) {
            $this->ffmpeg_path = Configure::read('UploadVideo.video_ffmpeg_path');
        }
    }

    protected function _convert_video_ffmpeg($sOutputPath, $sOriginalPath) {
        // Check time exec
        $sStartTime = microtime(true);

        // Set time limit and ignore_user_abort
        set_time_limit(0);
        $sPrevAbort = ignore_user_abort(true);

        // Get Video Size
        $aOutput = $iReturn = null;
        $ffprobePath = dirname($this->ffmpeg_path) . DS . 'ffprobe';
        $sScale = '-vf "scale=1280:720:force_original_aspect_ratio=increase,crop=1280:720"';
        exec(escapeshellarg($ffprobePath) . ' -v error -show_entries stream=width,height -show_entries stream_tags=rotate -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($sOriginalPath), $aOutput, $iReturn);

        if ($iReturn === 0 && !empty($aOutput)) {
            $iOutputWidth = $aOutput[0];
            $iOutputHeight = $aOutput[1];
            $iOutputRotate = !empty($aOutput[2]) ? $aOutput[2] : 0;
            if ($iOutputRotate == 90 || $iOutputRotate == 270) {
                $iOutputHeight = $aOutput[0];
                $iOutputWidth = $aOutput[1];
            }

            $sScale = '-vf "scale=1280:720,setsar=sar=1/1,setdar=dar=' . $iOutputWidth . '/' . $iOutputHeight . '"';
        }

        // Video Convert
        $aVideoFind = array('{sFFMPEGPath}', '{sOriginalPath}', '{sOutputPath}', '{sScale}', '{sDebug}');
        $aVideoReplace = array(escapeshellarg($this->ffmpeg_path), escapeshellarg($sOriginalPath), escapeshellarg($sOutputPath), $sScale, '2>&1');
        $sVideoCommand = str_replace($aVideoFind, $aVideoReplace, Configure::read('UploadVideo.video_ffmpeg_params_convert_mp4'));

        $sVideoOutput = PHP_EOL;
        $sVideoOutput .= 'Output: ' . $sOutputPath . PHP_EOL;
        $sVideoOutput .= 'Command: ' . $sVideoCommand . PHP_EOL;
        $sVideoOutput .= shell_exec($sVideoCommand);

        // Restore abort
        ignore_user_abort($sPrevAbort);
        $sEndTime = microtime(true);
        $sDelta = $sEndTime - $sStartTime;

        // Log convert
        if (Configure::read('debug')) {
            $this->log($sVideoOutput);
            $this->log(sprintf('Execution Complete of Video: %f seconds', $sDelta));
        }
    }

    protected function _convert_thumbnail_ffmpeg($sThumbPath, $sOriginalPath, $bScale = false) {
        // Check time exec
        $sStartTime = microtime(true);

        // Set time limit and ignore_user_abort
        set_time_limit(0);
        $sPrevAbort = ignore_user_abort(true);
        
        // scale image
        $sScale = '-vf "scale=0:0"';
        if ($bScale) {
            $sScale = '-vf "scale=1280:720:force_original_aspect_ratio=increase,crop=1280:720"';
        }

        // Thumbnail
        $aThumbFind = array('{sFFMPEGPath}', '{sOriginalPath}', '{sThumbPath}', '{sScale}', '{sDebug}');
        $aThumbReplace = array(escapeshellarg($this->ffmpeg_path), escapeshellarg($sOriginalPath), escapeshellarg($sThumbPath), $sScale, '2>&1');
        $sThumbCommand = str_replace($aThumbFind, $aThumbReplace, Configure::read('UploadVideo.video_ffmpeg_params_thumbnail'));

        $sThumbOutput = PHP_EOL;
        $sThumbOutput .= 'Output: ' . $sThumbPath . PHP_EOL;
        $sThumbOutput .= 'Command: ' . $sThumbCommand . PHP_EOL;
        $sThumbOutput .= shell_exec($sThumbCommand);

        // Restore abort
        ignore_user_abort($sPrevAbort);
        $sEndTime = microtime(true);
        $sDelta = $sEndTime - $sStartTime;

        // Log convert
        if (Configure::read('debug')) {
            $this->log($sThumbOutput);
            $this->log(sprintf('Execution Complete of Video: %f seconds', $sDelta));
        }
    }

    // write log
    public function log($msg, $type = 'videos', $scope = null) {
        parent::log($msg, $type, $scope);
    }

}
