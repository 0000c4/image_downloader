<?php

class Application
{
    private $config;
    private $logger;
    private $fileHandler;
    private $imageDownloader;
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->fileHandler = new FileHandler();
        $this->logger = new Logger($config['log_level'], $config['log_dir']);
        $this->imageDownloader = new ImageDownloader(
            $this->logger,
            $this->fileHandler,
            $config['download_dir']
        );
    }
    
    public function run(): void
    {
        try {
            $startTime = microtime(true);
            $this->logger->info("Скрипт запущен: " . date('Y-m-d H:i:s'));
            
            $this->imageDownloader->processUrlsFromFile($this->config['source_file']);
            
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);
            $this->logger->info("Скрипт завершен: " . date('Y-m-d H:i:s') . " (время выполнения: {$executionTime} сек.)");
            
        } catch (Exception $e) {
            $this->logger->info("Ошибка: " . $e->getMessage());
            echo "Произошла ошибка: " . $e->getMessage();
        }
    }
}