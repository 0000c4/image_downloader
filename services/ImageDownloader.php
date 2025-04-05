<?php

class ImageDownloader
{
    private $logger;
    private $fileHandler;
    private $downloadDir;
    
    private $totalUrls = 0;
    private $downloadedCount = 0;
    private $skippedCount = 0;
    private $errorCount = 0;
    
    public function __construct(Logger $logger, FileHandler $fileHandler, string $downloadDir)
    {
        $this->logger = $logger;
        $this->fileHandler = $fileHandler;
        $this->downloadDir = $downloadDir;
        
        $this->fileHandler->ensureDirectoryExists($downloadDir);
    }
    
    public function processUrlsFromFile(string $sourceFile): void
    {
        $urls = $this->fileHandler->readLines($sourceFile);
        $this->totalUrls = count($urls);
        
        $this->logger->info("Начало загрузки изображений. Всего URL: {$this->totalUrls}");
        
        foreach ($urls as $url) {
            $this->downloadImage($url);
        }
        
        $this->logger->info("Завершение загрузки изображений.");
        $this->logger->info("Итоги: Скачано: {$this->downloadedCount}, Пропущено: {$this->skippedCount}, Ошибок: {$this->errorCount}");
    }
    
    private function downloadImage(string $url): void
    {
        $fileName = $this->fileHandler->getFileNameFromUrl($url);
        $filePath = $this->downloadDir . '/' . $fileName;
        
        // Если файл уже существует, пропускаем
        if ($this->fileHandler->fileExists($filePath)) {
            $this->skippedCount++;
            $this->logger->detailed("Пропущено: $url (файл уже существует)");
            return;
        }
        
        // Инициализация cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $fileSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($imageData && $httpCode == 200) {
            if (file_put_contents($filePath, $imageData)) {
                $this->downloadedCount++;
                $this->logger->detailed("Скачано: $url");
                $this->logger->debug("Файл: $fileName, Размер: " . $this->logger->formatBytes($fileSize) . ", HTTP код: $httpCode");
            } else {
                $this->errorCount++;
                $this->logger->detailed("Ошибка: Не удалось сохранить файл: $url");
                $this->logger->debug("HTTP код: $httpCode, Ошибка записи файла");
            }
        } else {
            $this->errorCount++;
            $this->logger->detailed("Ошибка: Не удалось скачать изображение: $url");
            $this->logger->debug("HTTP код: $httpCode, Ошибка: $error");
        }
    }
    
    public function getTotalUrls(): int
    {
        return $this->totalUrls;
    }
    
    public function getDownloadedCount(): int
    {
        return $this->downloadedCount;
    }
    
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }
}