<?php
// modules/addons/reminder/reminder_cron.php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/includes/functions.php';

// Check if module is enabled
$enabled = get_query_val('tbladdonmodules', 'value', [
    'module' => 'reminder',
    'setting' => 'enabled'
]);

if ($enabled != '1') {
    logActivity("Reminder Module: Module is disabled");
    exit;
}

// Get module settings
$settings = [];
$result = full_query("SELECT setting, value FROM tbladdonmodules WHERE module='reminder'");
while ($data = mysql_fetch_assoc($result)) {
    $settings[$data['setting']] = $data['value'];
}

// Parse reminder hours
$reminderHours = explode(',', $settings['reminder_hours']);
$reminderHours = array_map('trim', $reminderHours);
$reminderHours = array_map('intval', $reminderHours);
sort($reminderHours);

// Get configuration values
$maxReminders = (int)$settings['max_reminders'];
$emailTemplateId = (int)$settings['email_template_id'];
$testMode = $settings['test_mode'] == '1';
$adminEmail = $settings['admin_email'];
$excludeDomains = $settings['exclude_domains'] == '1';
$languageOverride = $settings['language_override'];

// Current time
$now = date('Y-m-d H:i:s');

// Find unpaid orders
$whereConditions = "o.status = 'Pending'";
if ($excludeDomains) {
    $whereConditions .= " AND t.type != 'domain' AND t.type != 'domainregister' AND t.type != 'domaintransfer'";
}

$query = "SELECT o.id as order_id, o.userid, o.ordernum, o.date, 
                 c.firstname, c.lastname, c.email, c.language as client_language,
                 COUNT(l.id) as reminders_sent,
                 MAX(l.reminder_number) as last_reminder_number
          FROM tblorders o
          LEFT JOIN tblclients c ON o.userid = c.id
          LEFT JOIN tblhosting h ON o.id = h.orderid
          LEFT JOIN tblproducts t ON h.packageid = t.id
          LEFT JOIN mod_reminder_logs l ON o.id = l.order_id
          WHERE $whereConditions
          GROUP BY o.id
          HAVING reminders_sent < $maxReminders
          ORDER BY o.date ASC";

$orders = full_query($query);
$processedCount = 0;

while ($order = mysql_fetch_assoc($orders)) {
    $orderId = $order['order_id'];
    $userId = $order['userid'];
    $orderDate = $order['date'];
    $remindersSent = $order['reminders_sent'];
    $lastReminderNumber = $order['last_reminder_number'] ?: 0;
    
    // Determine language for this client
    if ($languageOverride && $languageOverride != 'auto') {
        $clientLanguage = $languageOverride;
    } else {
        $clientLanguage = !empty($order['client_language']) ? $order['client_language'] : 'english';
    }
    
    // Ensure valid language
    $validLanguages = ['english', 'persian', 'arabic', 'french', 'german', 
                      'spanish', 'italian', 'russian', 'turkish',
                      'chinese', 'japanese'];
    if (!in_array($clientLanguage, $validLanguages)) {
        $clientLanguage = 'english';
    }
    
    // Calculate hours passed since order
    $orderTime = strtotime($orderDate);
    $currentTime = time();
    $hoursPassed = floor(($currentTime - $orderTime) / 3600);
    
    // Check if it's time to send next reminder
    $nextReminderIndex = $lastReminderNumber;
    if ($nextReminderIndex < count($reminderHours)) {
        $nextReminderHour = $reminderHours[$nextReminderIndex];
        
        if ($hoursPassed >= $nextReminderHour) {
            // Prepare merge fields with translation
            $mergeFields = prepareMergeFields($order, $nextReminderHour, $nextReminderIndex + 1, $clientLanguage);
            
            // Determine recipient
            $to = $testMode && !empty($adminEmail) ? $adminEmail : $order['email'];
            
            // Send email with appropriate language
            $emailSent = sendTranslatedEmail($to, $emailTemplateId, $mergeFields, $clientLanguage);
            
            if ($emailSent) {
                // Save log with language
                $logQuery = "INSERT INTO mod_reminder_logs 
                            (order_id, user_id, reminder_number, reminder_hours, sent_time, email_template, language)
                            VALUES ('$orderId', '$userId', '" . ($nextReminderIndex + 1) . "', 
                                    '$nextReminderHour', '$now', '$emailTemplateId', '$clientLanguage')";
                full_query($logQuery);
                
                $processedCount++;
                
                // Log with appropriate language
                $logMessage = trans('email_sent_log', $clientLanguage, [
                    'reminder_number' => $nextReminderIndex + 1,
                    'order_number' => $order['ordernum'],
                    'language' => $clientLanguage
                ]);
                logActivity("Reminder Module: " . $logMessage);
            }
        }
    }
}

$completionMessage = trans('processing_complete', 'english', ['count' => $processedCount]);
logActivity("Reminder Module: " . $completionMessage);

/**
 * Prepare merge fields for email
 */
function prepareMergeFields($order, $hours, $reminderNumber, $language) {
    $LANG = loadSpecificLanguage($language);
    
    return [
        'client_name' => $order['firstname'] . ' ' . $order['lastname'],
        'client_first_name' => $order['firstname'],
        'client_last_name' => $order['lastname'],
        'order_id' => $order['order_id'],
        'order_number' => $order['ordernum'],
        'order_date' => $order['date'],
        'reminder_number' => $reminderNumber,
        'reminder_hours' => $hours,
        'reminder_hours_text' => $hours . ' ' . $LANG['hours'],
        'invoice_link' => rtrim(Configuration::get('SystemURL'), '/') . '/viewinvoice.php?id=' . $order['order_id'],
        'client_area_link' => rtrim(Configuration::get('SystemURL'), '/') . '/clientarea.php',
        'company_name' => Configuration::get('CompanyName'),
        'website_url' => Configuration::get('Domain')
    ];
}