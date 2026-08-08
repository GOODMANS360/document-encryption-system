# DOCUMENT ENCRYPTION SYSTEM
## USER MANUAL

**Developer:** GOODMAN SUNDAY  
**Project:** ND Computer Science Project  
**Version:** 1.0

---

# TABLE OF CONTENTS

1. Introduction
2. System Requirements
3. Getting Started
4. Understanding the Interface
5. How to Encrypt a File
6. How to Decrypt a File
7. Understanding Error Messages
8. Frequently Asked Questions
9. Security Best Practices
10. Important Security Information
11. Troubleshooting
12. Project Limitations
13. Contact & Support
14. Document Version

---

# 1. INTRODUCTION

The **Document Encryption System** is a web-based application that allows users to encrypt and decrypt files using password-protected AES-256-based encryption.

The system is designed to help users protect files from unauthorized access.

Users can:

- Select a file.
- Create a password.
- Encrypt the file.
- Download the encrypted `.enc` file.
- Later upload the encrypted file.
- Enter the correct password.
- Decrypt and download the original file.

The application does not require a database and is designed to run on a PHP/Apache web server.

---

# 2. SYSTEM REQUIREMENTS

The system requires:

- PHP 7.4 or higher
- OpenSSL PHP extension
- Apache web server
- XAMPP or WAMP for local installation
- Modern web browser

Supported browsers include:

- Google Chrome
- Mozilla Firefox
- Microsoft Edge
- Safari

---

# 3. GETTING STARTED

## Accessing the System

After installing the application on your local web server, open your browser and navigate to:

```text
http://localhost/encryption_system/
```

The main Document Encryption System interface should appear.

---

# 4. UNDERSTANDING THE INTERFACE

The main interface contains several sections.

## Header

Displays the name and description of the Document Encryption System.

## File Selection

Allows you to select the file you want to encrypt or decrypt.

The system displays information about the selected file.

## Password Section

Used to enter the password required for encryption or decryption.

During encryption, you must confirm the password.

## Action Buttons

The main actions include:

**ENCRYPT FILE**

Used to encrypt a selected file.

**DECRYPT FILE**

Used to decrypt an encrypted `.enc` file.

## Status Area

Displays messages showing whether the operation was successful or whether an error occurred.

## Download Area

After a successful operation, a download option is provided for the processed file.

---

# 5. HOW TO ENCRYPT A FILE

Follow these steps to encrypt a file.

## STEP 1: Select Your File

1. Click **Choose File**.
2. Browse to the location of the file.
3. Select the file.
4. The system will display the selected file information.

The maximum supported file size is **10MB**.

---

## STEP 2: Create a Password

Enter a password in the password field.

The password must meet the minimum length requirement configured by the application.

For example:

```text
MySecure123!
```

Confirm the password in the confirmation field.

### Password Recommendations

Use a strong password containing a combination of:

- Uppercase letters
- Lowercase letters
- Numbers
- Special characters

For better security, use a password of **12 characters or more**.

### IMPORTANT

**Do not lose your password.**

The application does not provide a password recovery mechanism.

If you lose the password required to decrypt your encrypted file, you may not be able to recover the original file.

---

# STEP 3: Encrypt the File

After selecting the file and entering the password:

1. Click **ENCRYPT FILE**.
2. The system will begin processing the file.
3. Wait for the operation to complete.
4. A success or error message will be displayed.

Do not close the browser while the operation is processing.

---

# STEP 4: Download the Encrypted File

After successful encryption:

1. Locate the **Download File** button.
2. Click the button.
3. Save the encrypted file to your computer.

The encrypted file will normally have the:

```text
.enc
```

extension.

For example:

```text
document.pdf
```

may produce:

```text
document.pdf.enc
```

The original file remains unchanged unless you manually delete it.

---

# 6. HOW TO DECRYPT A FILE

To recover the original file, follow these steps.

## STEP 1: Select the Encrypted File

1. Click **Choose File**.
2. Locate your encrypted `.enc` file.
3. Select the file.

For example:

```text
document.pdf.enc
```

---

# STEP 2: Enter the Password

Enter the exact password used when the file was encrypted.

Remember:

- Passwords are case-sensitive.
- Spelling must be exact.
- Spaces can affect the password.

---

# STEP 3: Decrypt the File

Click:

**DECRYPT FILE**

The system will process the encrypted file.

Wait until the operation has completed.

---

# STEP 4: Download the Decrypted File

After successful decryption:

1. Click **Download File**.
2. Save the file to your computer.
3. Open the file to verify its contents.

The decrypted file should contain the same information as the original file.

---

# 7. UNDERSTANDING ERROR MESSAGES

| Error Message | Meaning | Solution |
|---|---|---|
| Please select a file first | No file was selected | Select a file |
| Password must be at least 8 characters | Password is too short | Use a password meeting the minimum length |
| Passwords do not match | Password confirmation is different | Enter the same password |
| File too large | File exceeds the size limit | Use a file smaller than 10MB |
| Invalid password | Incorrect decryption password | Enter the original password |
| Select a .enc encrypted file | Selected file is not recognized as an encrypted file | Select the correct `.enc` file |
| Decryption failed | File may be corrupted or incompatible | Verify the file and password |
| OpenSSL extension not found | PHP OpenSSL is unavailable | Enable OpenSSL and restart Apache |
| Cannot write to directory | Server cannot write to a required directory | Check folder permissions |

---

# 8. FREQUENTLY ASKED QUESTIONS

## Q: Can I recover my file if I lose the password?

**A:** The application does not provide password recovery. If the correct password is lost, the encrypted file may not be decryptable.

Always store important passwords securely.

---

## Q: What file types can I encrypt?

**A:** The system is designed to process different file types, including:

- TXT
- PDF
- DOCX
- JPG
- PNG
- XLSX
- ZIP
- CSV

The exact file types accepted depend on the application's file validation configuration.

---

## Q: What is the maximum file size?

**A:** The current application limit is **10MB**.

---

## Q: Is a database required?

**A:** No.

The current version is designed to operate without a database.

---

## Q: Are passwords stored?

**A:** The application is designed not to store user passwords in a database.

---

## Q: Are my files permanently stored on the server?

**A:** Files are processed using server-side temporary storage. The application includes an automatic cleanup mechanism for temporary files.

However, users should not assume that temporary server storage is equivalent to secure permanent deletion.

---

## Q: Can someone see my password?

**A:** Passwords should be transmitted securely when the application is deployed over HTTPS.

Using HTTP on a network can expose transmitted information to interception.

For production or network-based deployment, HTTPS should be used.

---

## Q: Does AES-256 guarantee that my files can never be accessed?

**A:** No encryption system should be described as absolutely unbreakable.

AES-256 is a widely used strong encryption standard, but the overall security of an encryption application depends on its complete implementation, including password handling, key derivation, random number generation, file storage, server security, and transport security.

---

## Q: Why does decryption fail even when I think the password is correct?

Possible causes include:

- Incorrect password
- Incorrect capitalization
- Corrupted encrypted file
- Incomplete file upload/download
- Modified encrypted file
- Application configuration differences

Verify the password and make sure the encrypted file has not been modified.

---

## Q: Can I encrypt multiple files at once?

**A:** The current version processes one file at a time.

---

## Q: Does the system work on mobile devices?

**A:** The interface is designed to be responsive and can be accessed using modern mobile browsers. Actual performance may depend on the device, browser, server, and file size.

---

# 9. SECURITY BEST PRACTICES

For safer use of the system:

### 1. Use Strong Passwords

Use long, unique passwords.

A password of 12 or more characters is recommended.

---

### 2. Protect Your Password

Never share the password used to encrypt an important file with unauthorized people.

---

### 3. Use a Password Manager

For important passwords, use a reputable password manager rather than storing passwords in plain text.

---

### 4. Keep Backups

Keep appropriate backups of important encrypted files.

Remember that losing the password may prevent recovery.

---

### 5. Use HTTPS

When deploying the application on a network or production server, use HTTPS.

Avoid sending passwords over unencrypted HTTP connections.

---

### 6. Protect the Server

Keep the following updated:

- PHP
- Apache
- Operating system
- Browser
- Security software

---

### 7. Protect Original Files

Encryption does not automatically delete the original file.

If you no longer need the unencrypted copy, handle its deletion according to your security requirements.

---

### 8. Avoid Shared Computers

Avoid entering sensitive passwords on public or shared computers.

---

# 10. IMPORTANT SECURITY INFORMATION

This project was developed as an **ND Computer Science academic and portfolio project**.

It demonstrates practical concepts including:

- Web application development
- PHP programming
- File handling
- Password-based encryption
- OpenSSL
- Input validation
- Error handling
- Server-side processing

Before using the system for highly sensitive or production data, it should undergo a proper security review and additional hardening.

---

# 11. TROUBLESHOOTING

## Problem: OpenSSL Is Not Available

### Solution

1. Open `php.ini`.
2. Enable the OpenSSL extension.
3. Save the configuration.
4. Restart Apache.
5. Reload the application.

---

## Problem: Files Cannot Be Uploaded

Check:

- File size
- PHP upload limits
- Folder permissions
- Apache configuration
- Application validation rules

---

## Problem: Cannot Write to Directory

Verify that:

```text
uploads/
encrypted/
decrypted/
```

exist and are writable by the web server.

---

## Problem: Blank Page

Check:

- PHP error logs
- Apache error logs
- PHP version
- OpenSSL installation
- PHP configuration

For development only, PHP error reporting can temporarily be enabled to identify the problem.

---

# 12. PROJECT LIMITATIONS

The current version has the following limitations:

- Maximum file size of 10MB
- One file processed at a time
- No user authentication
- No database
- No password recovery
- No cloud storage
- No multi-user management
- Additional security hardening is required for production deployment

---

# 13. CONTACT & SUPPORT

For technical issues, check:

- PHP error logs
- Apache error logs
- Browser developer console
- PHP configuration
- OpenSSL configuration
- Directory permissions

---

# 14. DOCUMENT VERSION

**Document:** Document Encryption System User Manual  
**Version:** 1.0  
**Developer:** GOODMAN SUNDAY  
**Project:** ND Computer Science Project

---

## END OF USER MANUAL