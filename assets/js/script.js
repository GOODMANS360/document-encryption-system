/**
 * ND Computer Science Project
 * Document Encryption System - Frontend JavaScript
 * Handles UI interactions, file selection, password validation, AJAX requests
 * 
 * @author SUNDAY GOODMAN AKPAN
 * @version 1.0
 */

let currentFile = null;
let currentDownloadUrl = null;
let currentAction = null;

/**
 * Toggle password visibility
 * @param {string} fieldId - ID of the password field
 */
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

/**
 * Validate password strength and match
 */
function validatePassword() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirmPassword').value;
    const strengthDiv = document.getElementById('passwordStrength');
    const matchDiv = document.getElementById('passwordMatch');
    
    // Password strength check
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    
    if (password.length === 0) {
        strengthDiv.innerHTML = '';
    } else if (strength <= 2) {
        strengthDiv.innerHTML = '<span style="color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i> Weak password</span>';
    } else if (strength <= 4) {
        strengthDiv.innerHTML = '<span style="color: #f39c12;"><i class="fas fa-chart-line"></i> Medium password</span>';
    } else {
        strengthDiv.innerHTML = '<span style="color: #27ae60;"><i class="fas fa-check-circle"></i> Strong password</span>';
    }
    
    // Password match check
    if (confirm.length > 0) {
        if (password === confirm) {
            matchDiv.innerHTML = '<span style="color: #27ae60;"><i class="fas fa-check-circle"></i> Passwords match</span>';
            return true;
        } else {
            matchDiv.innerHTML = '<span style="color: #e74c3c;"><i class="fas fa-times-circle"></i> Passwords do not match</span>';
            return false;
        }
    } else {
        matchDiv.innerHTML = '';
        return false;
    }
}

/**
 * Display status message
 * @param {string} message - Message to display
 * @param {string} type - Message type (success, error, info)
 */
function showStatus(message, type) {
    const statusDiv = document.getElementById('statusMessage');
    const iconMap = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    statusDiv.className = `status-message status-${type}`;
    statusDiv.innerHTML = `<i class="fas ${iconMap[type]}"></i> ${message}`;
    statusDiv.style.display = 'flex';
    
    // Auto-hide after 5 seconds for success/info
    if (type !== 'error') {
        setTimeout(() => {
            statusDiv.style.display = 'none';
        }, 5000);
    }
}

/**
 * Show loading overlay
 * @param {string} text - Loading text to display
 */
function showLoading(text) {
    const overlay = document.getElementById('loadingOverlay');
    const loadingText = document.getElementById('loadingText');
    loadingText.textContent = text;
    overlay.style.display = 'flex';
    
    // Disable buttons
    document.getElementById('encryptBtn').disabled = true;
    document.getElementById('decryptBtn').disabled = true;
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.style.display = 'none';
    
    // Enable buttons
    document.getElementById('encryptBtn').disabled = false;
    document.getElementById('decryptBtn').disabled = false;
}

/**
 * Handle file selection
 */
function setupFileInput() {
    const fileInput = document.getElementById('fileInput');
    const fileDetails = document.getElementById('fileDetails');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileInfo = document.querySelector('.file-info .placeholder-text');
    
    fileInput.addEventListener('change', function(e) {
        currentFile = e.target.files[0];
        
        if (currentFile) {
            // Check file size (10MB limit)
            const maxSize = 10 * 1024 * 1024;
            if (currentFile.size > maxSize) {
                showStatus(`File too large! Maximum size is 10MB. Your file is ${(currentFile.size / 1024 / 1024).toFixed(2)}MB`, 'error');
                fileInput.value = '';
                currentFile = null;
                fileDetails.style.display = 'none';
                fileInfo.innerHTML = '<i class="fas fa-folder-open"></i> No file selected';
                return;
            }
            
            // Display file info
            fileName.textContent = currentFile.name;
            fileSize.textContent = formatFileSize(currentFile.size);
            fileDetails.style.display = 'block';
            fileInfo.innerHTML = `<i class="fas fa-check-circle"></i> ${currentFile.name}`;
            
            showStatus(`File "${currentFile.name}" selected successfully`, 'success');
        } else {
            fileDetails.style.display = 'none';
            fileInfo.innerHTML = '<i class="fas fa-folder-open"></i> No file selected';
        }
    });
}

/**
 * Format file size
 * @param {number} bytes - File size in bytes
 * @returns {string} Formatted file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Process file (encrypt or decrypt)
 * @param {string} action - 'encrypt' or 'decrypt'
 */
async function processFile(action) {
    // Validate file selection
    if (!currentFile) {
        showStatus('Please select a file first', 'error');
        return;
    }
    
    // Get password
    const password = document.getElementById('password').value;
    
    if (action === 'encrypt') {
        // Validate password for encryption
        if (password.length < 8) {
            showStatus('Password must be at least 8 characters long', 'error');
            return;
        }
        
        const confirm = document.getElementById('confirmPassword').value;
        if (password !== confirm) {
            showStatus('Passwords do not match', 'error');
            return;
        }
        
        if (!validatePassword()) {
            showStatus('Please ensure passwords match', 'error');
            return;
        }
    } else {
        // For decryption, only check if password is provided
        if (password.length === 0) {
            showStatus('Please enter the password', 'error');
            return;
        }
    }
    
    // Prepare form data
    const formData = new FormData();
    formData.append('file', currentFile);
    formData.append('password', password);
    formData.append('action', action);
    
    // Show loading
    const actionText = action === 'encrypt' ? 'Encrypting' : 'Decrypting';
    showLoading(`${actionText}... Please wait`);
    
    try {
        // Send request to server
        const response = await fetch('process.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        hideLoading();
        
        if (result.success) {
            // Store download info
            currentDownloadUrl = result.downloadUrl;
            currentAction = action;
            
            // Show success message
            const successMsg = action === 'encrypt' 
                ? `File encrypted successfully! Your encrypted file is ready to download.` 
                : `File decrypted successfully! Your original file is ready to download.`;
            showStatus(successMsg, 'success');
            
            // Show result section
            const resultSection = document.getElementById('resultSection');
            resultSection.style.display = 'block';
            
            // Clear file input for security
            document.getElementById('fileInput').value = '';
            document.getElementById('fileDetails').style.display = 'none';
            document.querySelector('.file-info .placeholder-text').innerHTML = '<i class="fas fa-folder-open"></i> No file selected';
            currentFile = null;
            
            // Clear password fields for security
            if (action === 'encrypt') {
                document.getElementById('password').value = '';
                document.getElementById('confirmPassword').value = '';
                document.getElementById('passwordStrength').innerHTML = '';
                document.getElementById('passwordMatch').innerHTML = '';
            } else {
                document.getElementById('password').value = '';
            }
        } else {
            showStatus(result.message, 'error');
        }
    } catch (error) {
        hideLoading();
        showStatus('An error occurred. Please try again.', 'error');
        console.error('Error:', error);
    }
}

/**
 * Download the processed file
 */
function downloadFile() {
    if (currentDownloadUrl) {
        window.location.href = currentDownloadUrl;
        showStatus('Download started!', 'success');
        
        // Hide result section after download
        setTimeout(() => {
            document.getElementById('resultSection').style.display = 'none';
            currentDownloadUrl = null;
        }, 2000);
    }
}

/**
 * Initialize event listeners
 */
function init() {
    setupFileInput();
    
    // Add password validation listeners
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirmPassword');
    
    passwordField.addEventListener('input', validatePassword);
    confirmField.addEventListener('input', validatePassword);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', init);