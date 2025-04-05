<?php

class Logger
{
    const INFO_LEVEL = 1;
    const DETAILED_LEVEL = 2;
    const DEBUG_LEVEL = 3;
    
    private $logLevel;
    private $logFile;
    
    public function __construct(int $logLevel, string $logDir)
    {
        $this->logLevel = $logLevel;
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $this->logFile = $logDir . '/log_' . date('Y-m-d_H-i-s') . '.txt';
        
        // Создаем пустой лог-файл
        file_put_contents($this->logFile, '');
    }
    
    public function info(string $message): void
    {
        $this->log($message, self::INFO_LEVEL);
    }
    
    public function detailed(string $message): void
    {
        $this->log($message, self::DETAILED_LEVEL);
    }
    
    public function debug(string $message): void
    {
        $this->log($message, self::DEBUG_LEVEL);
    }
    
    private function log(string $message, int $level): void
    {
        if ($this->logLevel >= $level) {
            $timestamp = date('Y-m-d H:i:s');
            $logLine = "[$timestamp] $message" . PHP_EOL;
            file_put_contents($this->logFile, $logLine, FILE_APPEND);
        }
    }
    
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}