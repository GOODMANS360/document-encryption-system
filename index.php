<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="A web-based document encryption and decryption system using AES-256 encryption.">

    <meta name="author" content="GOODMAN SUNDAY">

    <title>Document Encryption System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/icons/favicon.png">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header class="main-header">
            <h1>
                <i class="fas fa-lock"></i> 
                Document Encryption System
            </h1>
            <p class="subtitle">Secure your files with AES-256 encryption</p>
        </header>

        <!-- Main Content Card -->
        <div class="content-card">
            <!-- File Upload Section -->
            <div class="form-group">
                <label for="fileInput" class="form-label">
                    <i class="fas fa-file-upload"></i> Select File
                </label>
                <div class="file-input-wrapper">
                    <input type="file" id="fileInput" class="file-input">
                    <div class="file-info" id="fileInfo">
                        <span class="placeholder-text"><i class="fas fa-folder-open"></i> No file selected</span>
                    </div>
                </div>
                <div class="file-details" id="fileDetails" style="display: none;">
                    <div class="detail-item">
                        <i class="fas fa-file-alt"></i>
                        <span id="fileName"></span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-database"></i>
                        <span id="fileSize"></span>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-key"></i> Enter Password
                </label>
                <div class="password-wrapper">
                    <input type="password" id="password" class="form-input" placeholder="Enter password (min. 8 characters)">
                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword" class="form-label">
                    <i class="fas fa-check-circle"></i> Confirm Password
                </label>
                <div class="password-wrapper">
                    <input type="password" id="confirmPassword" class="form-input" placeholder="Re-enter password">
                    <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength"></div>
                <div class="password-match" id="passwordMatch"></div>
            </div>

            <!-- Action Buttons Section -->
            <div class="button-group">
                <button type="button" id="encryptBtn" class="btn btn-encrypt" onclick="processFile('encrypt')">
                    <i class="fas fa-lock"></i> ENCRYPT FILE
                </button>
                <button type="button" id="decryptBtn" class="btn btn-decrypt" onclick="processFile('decrypt')">
                    <i class="fas fa-unlock-alt"></i> DECRYPT FILE
                </button>
            </div>

            <!-- Result/Status Section -->
            <div id="statusMessage" class="status-message" style="display: none;"></div>
            
            <div id="resultSection" class="result-section" style="display: none;">
                <div class="result-header">
                    <i class="fas fa-download"></i> Download Your File
                </div>
                <div class="result-body">
                    <button type="button" id="downloadBtn" class="btn btn-download" onclick="downloadFile()">
                        <i class="fas fa-download"></i> Download File
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <footer class="main-footer">
            <p>
                <i class="fas fa-info-circle"></i>
                Select a file, enter a password, then choose Encrypt or Decrypt.
            </p>

            <p>
                Supported files: TXT, PDF, DOCX, JPG, PNG, XLSX and more.
                Maximum file size: 10MB.
            </p>

            <p>
                &copy; <?= date('Y') ?> GOODMAN SUNDAY |
                Document Encryption System
            </p>

            <p>
                AES-256-CBC Encryption | ND Computer Science Project
            </p>
</footer>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin fa-3x"></i>
            <p id="loadingText">Processing... Please wait</p>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
</body>
</html>