<?php
/**
 * ND Computer Science Project
 * Document Encryption System - File Processing Handler
 * Handles file encryption and decryption using AES-256-CBC
 * 
 * @author SUNDAY GOODMAN AKPAN
 * @version 1.0 - Fixed file extension preservation
 */

// Error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Configuration
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_DIR', 'uploads/');
define('ENCRYPTED_DIR', 'encrypted/');
define('DECRYPTED_DIR', 'decrypted/');
define('CLEANUP_TIMEOUT', 300); // 5 minutes in seconds

/**
 * Clean up old temporary files
 * Deletes files older than CLEANUP_TIMEOUT seconds
 */
function cleanupOldFiles() {
    $dirs = [UPLOAD_DIR, ENCRYPTED_DIR, DECRYPTED_DIR];
    $now = time();
    
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '*');
            foreach ($files as $file) {
                if (is_file($file) && ($now - filemtime($file)) > CLEANUP_TIMEOUT) {
                    unlink($file);
                }
            }
        }
    }
}

/**
 * Create necessary directories if they don't exist
 */
function createDirectories() {
    $dirs = [UPLOAD_DIR, ENCRYPTED_DIR, DECRYPTED_DIR];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

/**
 * Generate a random IV for encryption
 * 
 * @return string Random initialization vector
 */
function generateIV() {
    return openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
}

/**
 * Derive a 256-bit key from password using PBKDF2
 * 
 * @param string $password User's password
 * @param string $salt Random salt (32 bytes)
 * @return string Derived 256-bit key
 */
function deriveKey($password, $salt) {
    return hash_pbkdf2('sha256', $password, $salt, 100000, 32, true);
}

/**
 * Encrypt file using AES-256-CBC
 * Stores original file extension with the encrypted data
 * 
 * @param string $sourcePath Path to source file
 * @param string $destPath Path to encrypted output file
 * @param string $password Encryption password
 * @param string $originalExtension Original file extension
 * @return bool True on success, false on failure
 */
function encryptFile($sourcePath, $destPath, $password, $originalExtension) {
    // Generate random salt and IV
    $salt = openssl_random_pseudo_bytes(32);
    $iv = generateIV();
    
    // Derive key from password
    $key = deriveKey($password, $salt);
    
    // Read source file
    $data = file_get_contents($sourcePath);
    if ($data === false) {
        return false;
    }
    
    // Encrypt data
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    if ($encrypted === false) {
        return false;
    }
    
    // Store extension info with the encrypted data
    // Format: [4 bytes: extension length][extension string][encrypted data]
    $extLength = strlen($originalExtension);
    $extLengthPacked = pack('N', $extLength); // 4 bytes unsigned long
    
    // Combine: salt (32) + iv (16) + ext_length (4) + extension + encrypted_data
    $final = $salt . $iv . $extLengthPacked . $originalExtension . $encrypted;
    
    // Write to destination
    return file_put_contents($destPath, $final) !== false;
}

/**
 * Decrypt file using AES-256-CBC
 * Restores original file extension from stored data
 * 
 * @param string $sourcePath Path to encrypted file
 * @param string $destPath Path to decrypted output file
 * @param string $password Decryption password
 * @return bool|string True on success, false on failure, 'wrong_password' if password incorrect
 */
function decryptFile($sourcePath, $destPath, $password) {
    // Read encrypted file
    $data = file_get_contents($sourcePath);
    if ($data === false) {
        return false;
    }
    
    // Extract components
    $salt = substr($data, 0, 32);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 32, $ivLength);
    
    // Current position after salt and iv
    $pos = 32 + $ivLength;
    
    // Read extension length (4 bytes)
    $extLengthBin = substr($data, $pos, 4);
    $extLength = unpack('N', $extLengthBin)[1];
    $pos += 4;
    
    // Read original extension
    $originalExtension = substr($data, $pos, $extLength);
    $pos += $extLength;
    
    // Read encrypted data
    $encrypted = substr($data, $pos);
    
    // Derive key from password
    $key = deriveKey($password, $salt);
    
    // Attempt decryption
    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    
    if ($decrypted === false) {
        return 'wrong_password';
    }
    
    // Write decrypted data with correct path
    return file_put_contents($destPath, $decrypted) !== false;
}

/**
 * Gets the appropriate download name for decrypted file
 * 
 * @param string $originalName Original encrypted filename
 * @param string $restoredExtension The extension restored from encryption
 * @return string Proper filename with correct extension
 */
function getDecryptedDownloadName($originalName, $restoredExtension) {
    // Remove .enc extension from the original name
    $baseName = preg_replace('/\.enc$/i', '', $originalName);
    
    // If the base name already has an extension, use it
    $currentExt = pathinfo($baseName, PATHINFO_EXTENSION);
    
    if (!empty($currentExt)) {
        // Already has extension, keep it
        return $baseName;
    } elseif (!empty($restoredExtension)) {
        // Restore the original extension
        return $baseName . '.' . $restoredExtension;
    } else {
        // No extension found, use .bin as fallback
        return $baseName . '.bin';
    }
}

// Initialize directories and cleanup
createDirectories();
cleanupOldFiles();

// Set JSON response header
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
if (!isset($_FILES['file']) || !isset($_POST['password']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// Validate action
$action = $_POST['action'];
if (!in_array($action, ['encrypt', 'decrypt'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Validate file upload
if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds server maximum size',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form maximum size',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    $message = $errors[$_FILES['file']['error']] ?? 'Unknown upload error';
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Validate file size
if ($_FILES['file']['size'] > MAX_FILE_SIZE) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 10MB']);
    exit;
}

// Validate password
$password = $_POST['password'];
if ($action === 'encrypt' && strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}
if ($action === 'decrypt' && strlen($password) === 0) {
    echo json_encode(['success' => false, 'message' => 'Password cannot be empty']);
    exit;
}

// Generate unique filenames
$originalName = $_FILES['file']['name'];
$uniqueId = uniqid() . '_' . bin2hex(random_bytes(8));
$uploadPath = UPLOAD_DIR . $uniqueId . '_original';

// Move uploaded file
if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
    exit;
}

// Process based on action
$result = false;
$outputPath = null;
$downloadName = null;

if ($action === 'encrypt') {
    // Get original file extension
    $originalExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    
    // Encrypt file
    $outputPath = ENCRYPTED_DIR . $uniqueId . '.enc';
    $result = encryptFile($uploadPath, $outputPath, $password, $originalExtension);
    $downloadName = pathinfo($originalName, PATHINFO_FILENAME) . '.enc';
    
    if ($result === true) {
        echo json_encode([
            'success' => true,
            'message' => 'File encrypted successfully! Your file was saved with .enc extension. The original file type information is preserved.',
            'downloadUrl' => 'download.php?file=' . urlencode(basename($outputPath)) . '&name=' . urlencode($downloadName)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Encryption failed. Please try again.']);
    }
} else {
    // Decrypt file - must be .enc file
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($extension !== 'enc') {
        echo json_encode(['success' => false, 'message' => 'For decryption, please select a .enc encrypted file']);
        unlink($uploadPath);
        exit;
    }
    
    // First, read the encrypted file to get the original extension
    $encryptedData = file_get_contents($uploadPath);
    if ($encryptedData !== false) {
        // Extract the original extension from the encrypted file
        $salt = substr($encryptedData, 0, 32);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $pos = 32 + $ivLength;
        $extLengthBin = substr($encryptedData, $pos, 4);
        $extLength = unpack('N', $extLengthBin)[1];
        $pos += 4;
        $originalExtension = substr($encryptedData, $pos, $extLength);
    } else {
        $originalExtension = '';
    }
    
    // Attempt decryption
    $decryptedExt = !empty($originalExtension) ? $originalExtension : 'bin';
    $outputPath = DECRYPTED_DIR . $uniqueId . '_decrypted.' . $decryptedExt;
    $result = decryptFile($uploadPath, $outputPath, $password);
    
    if ($result === true) {
        // Get proper download name with correct extension
        $downloadName = getDecryptedDownloadName($originalName, $originalExtension);
        
        echo json_encode([
            'success' => true,
            'message' => 'File decrypted successfully! Your original file has been restored with the correct extension.',
            'downloadUrl' => 'download.php?file=' . urlencode(basename($outputPath)) . '&name=' . urlencode($downloadName)
        ]);
    } elseif ($result === 'wrong_password') {
        echo json_encode(['success' => false, 'message' => 'Invalid password. Please try again with the correct password.']);
        // Clean up the decrypted file if it was created
        if (file_exists($outputPath)) {
            unlink($outputPath);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Decryption failed. The file may be corrupted or not properly encrypted.']);
    }
}

// Clean up uploaded file
if (file_exists($uploadPath)) {
    unlink($uploadPath);
}
?>