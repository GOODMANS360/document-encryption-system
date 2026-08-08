<?php
/**
 * Document Encryption System - File Download Handler
 * Securely serves files for download and removes them after delivery
 * 
 * @author ND Computer Science Project
 * @version 1.0
 */

// Configuration
define('ENCRYPTED_DIR', 'encrypted/');
define('DECRYPTED_DIR', 'decrypted/');

// Validate request
if (!isset($_GET['file']) || !isset($_GET['name'])) {
    header('HTTP/1.0 400 Bad Request');
    die('Invalid download request');
}

$file = basename($_GET['file']);
$downloadName = basename($_GET['name']);

// Security: Only allow alphanumeric, underscore, hyphen, dot in filename
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $file)) {
    header('HTTP/1.0 400 Bad Request');
    die('Invalid filename');
}

if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $downloadName)) {
    $downloadName = 'download';
}

// Check both possible directories
$filePath = null;
if (file_exists(ENCRYPTED_DIR . $file)) {
    $filePath = ENCRYPTED_DIR . $file;
} elseif (file_exists(DECRYPTED_DIR . $file)) {
    $filePath = DECRYPTED_DIR . $file;
}

if (!$filePath || !is_file($filePath)) {
    header('HTTP/1.0 404 Not Found');
    die('File not found');
}

// Set headers for download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Clear output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Read file and output
readfile($filePath);

// Delete file after download
unlink($filePath);
exit;
?>