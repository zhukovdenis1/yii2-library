<?php

namespace app\components\services;

use Yii;
use yii\base\Component;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * File Upload Service
 * Handles file uploads with support for local and external storage
 */
class FileUploadService extends Component
{
    /**
     * @var string Upload directory path (relative to @webroot)
     */
    public $uploadPath = 'uploads/books';

    /**
     * @var int Maximum image width for resize
     */
    public $maxWidth = 800;

    /**
     * @var int Maximum image height for resize
     */
    public $maxHeight = 1200;

    /**
     * @var bool Enable image resize
     */
    public $enableResize = true;

    /**
     * @var string Storage type: 'local' or 'external' (S3, etc.)
     * For future external storage integration
     */
    public $storageType = 'local';

    /**
     * Upload file
     *
     * @param UploadedFile $file
     * @param string|null $oldFileName Old file name to delete
     * @return string|false Uploaded file name or false on error
     */
    public function upload(UploadedFile $file, $oldFileName = null)
    {
        if (!$file) {
            return false;
        }

        // Generate unique filename
        $fileName = $this->generateFileName($file);

        // Get full upload path
        $uploadDir = $this->getUploadDir();

        // Ensure directory exists
        if (!$this->ensureDirectory($uploadDir)) {
            Yii::error("Failed to create upload directory: {$uploadDir}", 'file.upload');
            return false;
        }

        $filePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        // Save file based on storage type
        if ($this->storageType === 'local') {
            return $this->uploadToLocal($file, $filePath, $fileName, $oldFileName);
        }

        // Future: external storage (S3, etc.)
        // return $this->uploadToExternal($file, $fileName, $oldFileName);

        return false;
    }

    /**
     * Upload file to local storage
     *
     * @param UploadedFile $file
     * @param string $filePath
     * @param string $fileName
     * @param string|null $oldFileName
     * @return string|false
     */
    protected function uploadToLocal(UploadedFile $file, $filePath, $fileName, $oldFileName = null)
    {
        try {
            // Check if image BEFORE saving (when temp file still exists)
            $isImage = $this->isImage($file);

            // Save file
            if (!$file->saveAs($filePath)) {
                Yii::error("Failed to save file: {$filePath}", 'file.upload');
                return false;
            }

            // Resize image if enabled and file is image
            if ($this->enableResize && $isImage) {
                $this->resizeImage($filePath);
            }

            // Delete old file if exists
            if ($oldFileName) {
                $this->deleteFile($oldFileName);
            }

            Yii::info("File uploaded successfully: {$fileName}", 'file.upload');

            return $fileName;

        } catch (\Exception $e) {
            Yii::error("File upload error: " . $e->getMessage(), 'file.upload');
            return false;
        }
    }

    /**
     * Delete file
     *
     * @param string $fileName
     * @return bool
     */
    public function deleteFile($fileName)
    {
        if (!$fileName) {
            return true;
        }

        if ($this->storageType === 'local') {
            $filePath = $this->getUploadDir() . DIRECTORY_SEPARATOR . $fileName;

            if (file_exists($filePath)) {
                return unlink($filePath);
            }
        }

        // Future: delete from external storage

        return true;
    }

    /**
     * Resize image to fit max dimensions
     *
     * @param string $filePath
     * @return bool
     */
    protected function resizeImage($filePath)
    {
        try {
            $image = Image::getImagine()->open($filePath);
            $size = $image->getSize();

            // Check if resize needed
            if ($size->getWidth() > $this->maxWidth || $size->getHeight() > $this->maxHeight) {
                $image->thumbnail(new \Imagine\Image\Box($this->maxWidth, $this->maxHeight))
                    ->save($filePath, ['quality' => 90]);

                Yii::info("Image resized: {$filePath}", 'file.upload');
            }

            return true;

        } catch (\Exception $e) {
            Yii::error("Image resize error: " . $e->getMessage(), 'file.upload');
            return false;
        }
    }

    /**
     * Generate unique file name
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateFileName(UploadedFile $file)
    {
        $extension = $file->extension;
        $baseName = pathinfo($file->name, PATHINFO_FILENAME);
        $baseName = $this->sanitizeFileName($baseName);

        return $baseName . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Sanitize file name
     *
     * @param string $fileName
     * @return string
     */
    protected function sanitizeFileName($fileName)
    {
        // Transliterate
        $fileName = transliterator_transliterate('Any-Latin; Latin-ASCII', $fileName);

        // Remove special characters
        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileName);

        // Limit length
        return substr($fileName, 0, 50);
    }

    /**
     * Check if file is image
     *
     * @param UploadedFile $file
     * @return bool
     */
    protected function isImage(UploadedFile $file)
    {
        return in_array(strtolower($file->extension), ['jpg', 'jpeg', 'png', 'gif']);
    }

    /**
     * Get upload directory path
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return Yii::getAlias('@webroot/' . $this->uploadPath);
    }

    /**
     * Ensure directory exists
     *
     * @param string $dir
     * @return bool
     */
    protected function ensureDirectory($dir)
    {
        if (!is_dir($dir)) {
            return mkdir($dir, 0755, true);
        }

        return true;
    }

    /**
     * Future method: Upload to external storage (S3, etc.)
     *
     * Implementation example for migration to S3:
     *
     * 1. Install AWS SDK: composer require aws/aws-sdk-php
     * 2. Configure S3 client in config:
     *    's3' => [
     *        'class' => 'Aws\S3\S3Client',
     *        'credentials' => [
     *            'key' => getenv('AWS_ACCESS_KEY_ID'),
     *            'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
     *        ],
     *        'region' => getenv('AWS_REGION'),
     *        'version' => 'latest',
     *    ],
     * 3. Implement uploadToExternal():
     *    $result = Yii::$app->s3->putObject([
     *        'Bucket' => getenv('AWS_BUCKET'),
     *        'Key' => $fileName,
     *        'SourceFile' => $file->tempName,
     *        'ACL' => 'public-read',
     *    ]);
     * 4. Change storageType to 'external' in config
     * 5. Update getCoverImageUrl() in Book model to return S3 URL
     */
}
