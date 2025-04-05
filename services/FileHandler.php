<?php

class FileHandler
{
    public function readLines(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new Exception("Файл с URL изображений не найден: $filePath");
        }
        
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        return $lines ? $lines : [];
    }
    
    public function ensureDirectoryExists(string $dir): void
    {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new Exception("Не удалось создать директорию: $dir");
            }
        }
    }
    
    public function getFileNameFromUrl(string $url): string
    {
        $fileName = basename(parse_url($url, PHP_URL_PATH));
        if (empty($fileName) || strpos($fileName, '.') === false) {
            $fileName = md5($url) . '.jpg';
        }
        return $fileName;
    }
    
    public function fileExists(string $path): bool
    {
        return file_exists($path);
    }
}