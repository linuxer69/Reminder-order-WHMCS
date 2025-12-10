<?php
// modules/addons/reminder/lang/english.php

$_ADDONLANG = [
    // Module Information
    'module_name' => 'Reminder System',
    'module_description' => 'Send reminder emails for unpaid orders',
    
    // General Settings
    'settings_title' => 'Reminder System Settings',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'save_settings' => 'Save Settings',
    'settings_saved' => 'Settings saved successfully',
    
    // Settings Fields
    'enabled' => 'Enable Module',
    'enabled_description' => 'Enable or disable the module',
    'reminder_hours' => 'Reminder Hours',
    'reminder_hours_description' => 'Hours after order placement to send reminders',
    'reminder_hours_example' => 'Example: 24,48,72',
    'email_template' => 'Email Template',
    'email_template_description' => 'Email template for sending reminders',
    'max_reminders' => 'Maximum Reminders',
    'max_reminders_description' => 'Maximum number of reminder emails per order',
    'exclude_domains' => 'Exclude Domain Orders',
    'exclude_domains_description' => 'Do not send reminders for domain orders',
    'test_mode' => 'Test Mode',
    'test_mode_description' => 'In test mode, emails are sent only to admin',
    'admin_email' => 'Admin Email for Testing',
    'admin_email_description' => 'In test mode, emails are sent to this address',
    'language_override' => 'Language Settings',
    'language_override_description' => 'Language used for reminder emails',
    'language_override_help' => 'If "Auto-detect" is selected, each client\'s language will be detected individually',
    
    // Statistics and Reports
    'statistics' => 'Statistics',
    'total_emails_sent' => 'Total Emails Sent',
    'pending_orders_count' => 'Pending Orders Count',
    'recent_emails' => 'Recent Sent Emails',
    'order_number' => 'Order Number',
    'customer' => 'Customer',
    'reminder_number' => 'Reminder Number',
    'hours' => 'Hours',
    'sent_date' => 'Sent Date',
    'language' => 'Language',
    
    // System Messages
    'email_sent_log' => 'Reminder email #:reminder_number sent for order #:order_number in :language',
    'processing_complete' => 'Processing completed. Orders processed: :count',
    
    // Instructions
    'instructions' => 'Usage Instructions',
    'instructions_text' => "1. This module must be executed by WHMCS Cron Job\n2. Place the cron/reminder_cron.php file in cron job\n3. Configure email settings in WHMCS Settings > Emails\n4. For multilingual support, create email templates for each language",
    
    // Email Variables
    'invoice_link_text' => 'Click here to view invoice',
    'client_area_text' => 'Access Client Area',
    
    // Time Units
    'hour' => 'hour',
    'hours' => 'hours',
    'day' => 'day',
    'days' => 'days',
];