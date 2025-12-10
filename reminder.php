<?php
// modules/addons/reminder/reminder.php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Load helper functions
require_once __DIR__ . '/includes/functions.php';

// Module configuration
function reminder_config() {
    // در reminder_output()، بعد از فرم تنظیمات و قبل از <hr> بخش آمار، این کد را اضافه کنید:

echo '<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
               padding: 25px; 
               border-radius: 10px; 
               margin: 30px 0; 
               text-align: center;
               box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <h3 style="color: white; margin-bottom: 15px;">❤️ Support This Module</h3>
        <p style="color: rgba(255,255,255,0.9); margin-bottom: 20px;">
            If you find this module useful, consider making a crypto donation to support further development.
        </p>
        <a href="https://nowpayments.io/donation?api_key=3dac6108-1f9b-4eb0-abf8-be38dc47f72a" 
           target="_blank" 
           rel="noreferrer noopener"
           style="display: inline-block; 
                  background: white; 
                  color: #764ba2; 
                  padding: 12px 30px; 
                  border-radius: 5px; 
                  text-decoration: none; 
                  font-weight: bold; 
                  font-size: 16px;
                  transition: all 0.3s;
                  border: 2px solid white;">
            <i class="fa fa-heart" style="margin-right: 8px;"></i>
            Donate with Crypto
        </a>
        <div style="margin-top: 15px; color: rgba(255,255,255,0.8); font-size: 12px;">
            Supports: Bitcoin, Ethereum, USDT & 100+ cryptocurrencies
        </div>
      </div>';
    
    $LANG = loadReminderLanguage();
    
    return [
        'name' => 'ReminderSarv',
        'description' => 'remindersarv',
        'version' => '1.2',
        'author' => 'Your Name',
        'language' => 'english',
        'fields' => [
            'enabled' => [
                'FriendlyName' => $LANG['enabled'],
                'Type' => 'yesno',
                'Description' => $LANG['enabled_description'],
                'Default' => '1'
            ],
            'reminder_hours' => [
                'FriendlyName' => $LANG['reminder_hours'],
                'Type' => 'text',
                'Description' => $LANG['reminder_hours_description'],
                'Default' => '24,48,72',
                'Size' => '50'
            ],
            'email_template_id' => [
                'FriendlyName' => $LANG['email_template'],
                'Type' => 'dropdown',
                'Description' => $LANG['email_template_description'],
                'Options' => getEmailTemplates(),
                'Default' => ''
            ],
            'max_reminders' => [
                'FriendlyName' => $LANG['max_reminders'],
                'Type' => 'text',
                'Description' => $LANG['max_reminders_description'],
                'Default' => '3',
                'Size' => '10'
            ],
            'exclude_domains' => [
                'FriendlyName' => $LANG['exclude_domains'],
                'Type' => 'yesno',
                'Description' => $LANG['exclude_domains_description'],
                'Default' => '0'
            ],
            'test_mode' => [
                'FriendlyName' => $LANG['test_mode'],
                'Type' => 'yesno',
                'Description' => $LANG['test_mode_description'],
                'Default' => '1'
            ],
            'admin_email' => [
                'FriendlyName' => $LANG['admin_email'],
                'Type' => 'text',
                'Description' => $LANG['admin_email_description'],
                'Default' => '',
                'Size' => '100'
            ],
            'language_override' => [
                'FriendlyName' => $LANG['language_override'],
                'Type' => 'dropdown',
                'Description' => $LANG['language_override_description'],
                'Options' => getAvailableLanguages(),
                'Default' => 'auto'
            ]
        ]
    ];
    
}

// Module activation
function reminder_activate() {
    try {
        $query = "CREATE TABLE IF NOT EXISTS `mod_reminder_logs` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `order_id` INT(11) NOT NULL,
            `user_id` INT(11) NOT NULL,
            `reminder_number` INT(11) NOT NULL,
            `reminder_hours` INT(11) NOT NULL,
            `sent_time` DATETIME NOT NULL,
            `email_template` VARCHAR(255) NOT NULL,
            `language` VARCHAR(10) DEFAULT 'english',
            PRIMARY KEY (`id`),
            KEY `order_id` (`order_id`),
            KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        full_query($query);
        
        $query2 = "CREATE TABLE IF NOT EXISTS `mod_reminder_order_settings` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `order_id` INT(11) NOT NULL,
            `disable_reminders` TINYINT(1) DEFAULT 0,
            `custom_hours` VARCHAR(255) DEFAULT NULL,
            `preferred_language` VARCHAR(10) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `order_id` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        full_query($query2);
        
        return [
            'status' => 'success',
            'description' => 'Module installed successfully. Required tables created.'
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Installation error: ' . $e->getMessage()
        ];
    }
}

// Module deactivation
function reminder_deactivate() {
    return [
        'status' => 'success',
        'description' => 'Module deactivated successfully.'
    ];
}

// Admin output/settings page
function reminder_output($vars) {
    global $CONFIG;
    
    // Get language for display
    $languageOverride = get_query_val('tbladdonmodules', 'value', [
        'module' => 'reminder',
        'setting' => 'language_override'
    ]);
    
    if ($languageOverride && $languageOverride != 'auto') {
        $LANG = loadSpecificLanguage($languageOverride);
    } else {
        $adminLang = isset($_SESSION['adminlang']) ? $_SESSION['adminlang'] : $CONFIG['Language'];
        $LANG = loadSpecificLanguage($adminLang);
    }
    
    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    
    // Check module status
    $enabled = get_query_val('tbladdonmodules', 'value', [
        'module' => 'reminder',
        'setting' => 'enabled'
    ]);
    
    // Get email templates
    $emailTemplates = getEmailTemplates();
    $availableLanguages = getAvailableLanguages();
    
    // Handle form submission
    $message = '';
    $messageType = '';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
        // Verify CSRF token
        if (!verifyCSRFToken($_POST['token'])) {
            $message = 'Invalid CSRF token. Please try again.';
            $messageType = 'error';
        } else {
            // Save settings
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'reminder_') === 0 || in_array($key, ['enabled', 'language_override'])) {
                    update_query('tbladdonmodules', [
                        'value' => $value
                    ], [
                        'module' => 'reminder',
                        'setting' => $key
                    ]);
                }
            }
            
            $message = $LANG['settings_saved'];
            $messageType = 'success';
        }
    }
    
    // Display message if any
    if ($message) {
        $alertClass = $messageType == 'error' ? 'alert-danger' : 'alert-success';
        echo '<div class="alert ' . $alertClass . '">' . $message . '</div>';
    }
    
    // Generate new CSRF token
    $csrfToken = generateCSRFToken();
    
    // Display settings form
    echo '<div class="reminder-container">
        <h2>' . $LANG['settings_title'] . '</h2>
        <form method="post" action="">
            <input type="hidden" name="token" value="' . $csrfToken . '">
            
            <div class="form-group">
                <label for="enabled">' . $LANG['enabled'] . ':</label>
                <select name="enabled" id="enabled" class="form-control">
                    <option value="1" ' . ($enabled == '1' ? 'selected' : '') . '>' . $LANG['active'] . '</option>
                    <option value="0" ' . ($enabled == '0' ? 'selected' : '') . '>' . $LANG['inactive'] . '</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="language_override">' . $LANG['language_override'] . ':</label>
                <select name="language_override" id="language_override" class="form-control">';
    
    $selectedLang = get_query_val('tbladdonmodules', 'value', [
        'module' => 'reminder',
        'setting' => 'language_override'
    ]);
    
    foreach ($availableLanguages as $code => $name) {
        echo '<option value="' . $code . '" ' . ($code == $selectedLang ? 'selected' : '') . '>' . $name . '</option>';
    }
    
    echo '</select>
                <small class="form-text text-muted">' . $LANG['language_override_help'] . '</small>
            </div>
            
            <div class="form-group">
                <label for="reminder_hours">' . $LANG['reminder_hours'] . ':</label>
                <input type="text" name="reminder_hours" id="reminder_hours" 
                       value="' . htmlspecialchars(get_query_val('tbladdonmodules', 'value', [
                           'module' => 'reminder',
                           'setting' => 'reminder_hours'
                       ])) . '" class="form-control">
                <small class="form-text text-muted">' . $LANG['reminder_hours_example'] . '</small>
            </div>
            
            <div class="form-group">
                <label for="email_template_id">' . $LANG['email_template'] . ':</label>
                <select name="email_template_id" id="email_template_id" class="form-control">';
    
    $selectedTemplate = get_query_val('tbladdonmodules', 'value', [
        'module' => 'reminder',
        'setting' => 'email_template_id'
    ]);
    
    foreach ($emailTemplates as $id => $name) {
        echo '<option value="' . $id . '" ' . ($id == $selectedTemplate ? 'selected' : '') . '>' . htmlspecialchars($name) . '</option>';
    }
    
    echo '</select>
            </div>
            
            <div class="form-group">
                <label for="max_reminders">' . $LANG['max_reminders'] . ':</label>
                <input type="number" name="max_reminders" id="max_reminders" 
                       value="' . htmlspecialchars(get_query_val('tbladdonmodules', 'value', [
                           'module' => 'reminder',
                           'setting' => 'max_reminders'
                       ])) . '" class="form-control" min="1" max="10">
            </div>
            
            <div class="form-group form-check">
                <input type="checkbox" name="exclude_domains" id="exclude_domains" value="1" ' . 
                (get_query_val('tbladdonmodules', 'value', [
                    'module' => 'reminder',
                    'setting' => 'exclude_domains'
                ]) == '1' ? 'checked' : '') . ' class="form-check-input">
                <label for="exclude_domains" class="form-check-label">' . $LANG['exclude_domains'] . '</label>
            </div>
            
            <div class="form-group form-check">
                <input type="checkbox" name="test_mode" id="test_mode" value="1" ' . 
                (get_query_val('tbladdonmodules', 'value', [
                    'module' => 'reminder',
                    'setting' => 'test_mode'
                ]) == '1' ? 'checked' : '') . ' class="form-check-input">
                <label for="test_mode" class="form-check-label">' . $LANG['test_mode'] . '</label>
            </div>
            
            <div class="form-group">
                <label for="admin_email">' . $LANG['admin_email'] . ':</label>
                <input type="email" name="admin_email" id="admin_email" 
                       value="' . htmlspecialchars(get_query_val('tbladdonmodules', 'value', [
                           'module' => 'reminder',
                           'setting' => 'admin_email'
                       ])) . '" class="form-control">
            </div>
            
            <button type="submit" name="save" class="btn btn-primary">' . $LANG['save_settings'] . '</button>
        </form>
        
        <hr>
        
        <h3>' . $LANG['statistics'] . '</h3>';
    
    // Display statistics
    $totalSent = get_query_val('mod_reminder_logs', 'COUNT(id)', '1');
    $totalOrders = get_query_val('tblorders', 'COUNT(id)', "status='Pending'");
    
    echo '<div class="stats">
            <p>' . $LANG['total_emails_sent'] . ': <strong>' . $totalSent . '</strong></p>
            <p>' . $LANG['pending_orders_count'] . ': <strong>' . $totalOrders . '</strong></p>
        </div>
        
        <h3>' . $LANG['recent_emails'] . '</h3>';
    
    $logs = full_query("SELECT l.*, o.ordernum, c.firstname, c.lastname 
                       FROM mod_reminder_logs l
                       LEFT JOIN tblorders o ON l.order_id = o.id
                       LEFT JOIN tblclients c ON l.user_id = c.id
                       ORDER BY l.sent_time DESC LIMIT 20");
    
    echo '<table class="table table-striped">
            <thead>
                <tr>
                    <th>' . $LANG['order_number'] . '</th>
                    <th>' . $LANG['customer'] . '</th>
                    <th>' . $LANG['reminder_number'] . '</th>
                    <th>' . $LANG['hours'] . '</th>
                    <th>' . $LANG['sent_date'] . '</th>
                    <th>' . $LANG['language'] . '</th>
                </tr>
            </thead>
            <tbody>';
    
    while ($log = mysql_fetch_assoc($logs)) {
        echo '<tr>
                <td>' . htmlspecialchars($log['ordernum']) . '</td>
                <td>' . htmlspecialchars($log['firstname']) . ' ' . htmlspecialchars($log['lastname']) . '</td>
                <td>' . htmlspecialchars($log['reminder_number']) . '</td>
                <td>' . htmlspecialchars($log['reminder_hours']) . ' ' . $LANG['hours'] . '</td>
                <td>' . htmlspecialchars($log['sent_time']) . '</td>
                <td>' . htmlspecialchars($log['language']) . '</td>
              </tr>';
    }
    
    echo '</tbody></table>';
    
    echo '<div class="alert alert-info">
            <h4>' . $LANG['instructions'] . ':</h4>
            <p>' . nl2br($LANG['instructions_text']) . '</p>
        </div>
    </div>';
}

// Get email templates list
function getEmailTemplates() {
    $templates = [];
    $result = full_query("SELECT id, name FROM tblemailtemplates ORDER BY name");
    
    while ($data = mysql_fetch_assoc($result)) {
        $templates[$data['id']] = $data['name'];
    }
    
    return $templates;
}

// Get available languages
function getAvailableLanguages() {
    $languages = [
        'auto' => 'Auto-detect',
        'english' => 'English',
        'persian' => 'Persian',
        'arabic' => 'Arabic',
        'french' => 'French',
        'german' => 'German',
        'spanish' => 'Spanish',
        'italian' => 'Italian',
        'russian' => 'Russian',
        'turkish' => 'Turkish',
        'chinese' => 'Chinese',
        'japanese' => 'Japanese'
    ];
    
    return $languages;
}

// Client area output (if needed)
function reminder_clientarea($vars) {
    return [];
}

