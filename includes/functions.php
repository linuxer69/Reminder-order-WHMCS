<?php
// modules/addons/reminder/includes/functions.php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['reminder_csrf_token'])) {
        $_SESSION['reminder_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['reminder_csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['reminder_csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['reminder_csrf_token'], $token);
}

/**
 * Load language based on current context
 */
function loadReminderLanguage() {
    $language = getCurrentLanguage();
    $langFile = __DIR__ . '/../lang/' . $language . '.php';
    
    if (file_exists($langFile)) {
        require_once $langFile;
    } else {
        require_once __DIR__ . '/../lang/english.php';
    }
    
    global $_ADDONLANG;
    return $_ADDONLANG;
}

/**
 * Detect current system language
 */
function getCurrentLanguage() {
    global $CONFIG;
    
    // Admin area
    if (defined('ADMINAREA')) {
        if (isset($_SESSION['adminlang'])) {
            return $_SESSION['adminlang'];
        }
        return $CONFIG['Language'];
    }
    
    // Client area
    if (isset($_SESSION['Language'])) {
        return $_SESSION['Language'];
    }
    
    // Client preference
    if (isset($_SESSION['uid'])) {
        $clientId = $_SESSION['uid'];
        $language = get_query_val('tblclients', 'language', ['id' => $clientId]);
        if ($language) {
            return $language;
        }
    }
    
    return $CONFIG['Language'];
}

/**
 * Load specific language file
 */
function loadSpecificLanguage($language) {
    $langFile = __DIR__ . '/../lang/' . $language . '.php';
    
    if (file_exists($langFile)) {
        require_once $langFile;
    } else {
        require_once __DIR__ . '/../lang/english.php';
    }
    
    global $_ADDONLANG;
    return $_ADDONLANG;
}

/**
 * Get client's preferred language
 */
function getClientLanguage($clientId) {
    $language = get_query_val('tblclients', 'language', ['id' => $clientId]);
    
    if (empty($language)) {
        global $CONFIG;
        $language = $CONFIG['Language'];
    }
    
    $availableLanguages = ['english', 'persian', 'arabic', 'french', 'german', 
                          'spanish', 'italian', 'russian', 'turkish', 
                          'chinese', 'japanese'];
    
    if (!in_array($language, $availableLanguages)) {
        $language = 'english';
    }
    
    return $language;
}

/**
 * Translate text
 */
function trans($key, $language = 'english', $replacements = []) {
    $langData = loadSpecificLanguage($language);
    
    if (isset($langData[$key])) {
        $text = $langData[$key];
    } else {
        $englishLang = loadSpecificLanguage('english');
        $text = isset($englishLang[$key]) ? $englishLang[$key] : $key;
    }
    
    foreach ($replacements as $search => $replace) {
        $text = str_replace(':' . $search, $replace, $text);
    }
    
    return $text;
}

/**
 * Send email with language support
 */
function sendTranslatedEmail($to, $templateId, $mergeFields, $language = 'english') {
    $template = getEmailTemplate($templateId, $language);
    
    if (!$template) {
        $template = getEmailTemplate($templateId, 'english');
    }
    
    if (!$template) {
        logActivity("Reminder Module: Email template not found for ID: $templateId");
        return false;
    }
    
    $subject = replaceMergeFields($template['subject'], $mergeFields);
    $message = replaceMergeFields($template['message'], $mergeFields);
    
    $mail = new WHMCS\Mail();
    $mail->setSubject($subject);
    $mail->setBody($message);
    $mail->addTo($to);
    
    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        logActivity("Reminder Module: Error sending email: " . $e->getMessage());
        return false;
    }
}

/**
 * Get email template for specific language
 */
function getEmailTemplate($templateId, $language = 'english') {
    $result = full_query("SELECT * FROM tblemailtemplates 
                         WHERE id = '$templateId' 
                         AND language = '$language' 
                         LIMIT 1");
    
    if (mysql_num_rows($result) > 0) {
        return mysql_fetch_assoc($result);
    }
    
    $result = full_query("SELECT * FROM tblemailtemplates 
                         WHERE id = '$templateId' 
                         AND language = 'english' 
                         LIMIT 1");
    
    if (mysql_num_rows($result) > 0) {
        return mysql_fetch_assoc($result);
    }
    
    return false;
}

/**
 * Replace merge fields
 */
function replaceMergeFields($text, $mergeFields) {
    foreach ($mergeFields as $key => $value) {
        $text = str_replace('{' . $key . '}', $value, $text);
    }
    return $text;
}