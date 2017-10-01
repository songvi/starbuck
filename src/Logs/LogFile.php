<?php
namespace AuthStack\Logs;

use Psr\Log\AbstractLogger;

class LogFile extends AbstractLogger{
    public $filePath;

    public function __construct($filePath){
        if(file_exists($filePath)){
            $this->filePath = $filePath;
        }
    }
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $this->writeLog($today = date("Ymd H:i:s") ." | ".$level. " | ".$message);
    }

    protected function writeLog($str){
        if(empty($this->filePath)) return;
        file_put_contents($this->filePath, $str, FILE_APPEND);
    }
}
