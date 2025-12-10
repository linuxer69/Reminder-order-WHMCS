<?php
// modules/addons/reminder/lang/persian.php

$_ADDONLANG = [
    // Module Information
    'module_name' => 'سیستم یادآوری',
    'module_description' => 'ارسال ایمیل یادآوری برای سفارشات پرداخت نشده',
    
    // General Settings
    'settings_title' => 'تنظیمات سیستم یادآوری',
    'active' => 'فعال',
    'inactive' => 'غیرفعال',
    'save_settings' => 'ذخیره تنظیمات',
    'settings_saved' => 'تنظیمات با موفقیت ذخیره شد',
    
    // Settings Fields
    'enabled' => 'فعال سازی ماژول',
    'enabled_description' => 'فعال یا غیرفعال کردن ماژول',
    'reminder_hours' => 'ساعت‌های یادآوری',
    'reminder_hours_description' => 'ساعت‌های بعد از ثبت سفارش برای ارسال یادآوری',
    'reminder_hours_example' => 'مثال: 24,48,72',
    'email_template' => 'قالب ایمیل',
    'email_template_description' => 'قالب ایمیل برای ارسال یادآوری',
    'max_reminders' => 'حداکثر تعداد یادآوری',
    'max_reminders_description' => 'حداکثر تعداد ایمیل‌های یادآوری برای هر سفارش',
    'exclude_domains' => 'عدم ارسال برای سفارشات دامنه',
    'exclude_domains_description' => 'عدم ارسال یادآوری برای سفارشات دامنه',
    'test_mode' => 'حالت آزمایشی',
    'test_mode_description' => 'در حالت آزمایشی، ایمیل‌ها فقط به ادمین ارسال می‌شوند',
    'admin_email' => 'ایمیل ادمین برای تست',
    'admin_email_description' => 'در حالت آزمایشی ایمیل‌ها به این آدرس ارسال می‌شوند',
    'language_override' => 'تنظیمات زبان',
    'language_override_description' => 'زبان مورد استفاده برای ایمیل‌های یادآوری',
    'language_override_help' => 'اگر "خودکار" انتخاب شود، زبان هر مشتری به صورت جداگانه تشخیص داده می‌شود',
    
    // Statistics and Reports
    'statistics' => 'آمار',
    'total_emails_sent' => 'تعداد کل ایمیل‌های ارسال شده',
    'pending_orders_count' => 'تعداد سفارشات در انتظار پرداخت',
    'recent_emails' => 'آخرین ایمیل‌های ارسال شده',
    'order_number' => 'شماره سفارش',
    'customer' => 'مشتری',
    'reminder_number' => 'شماره یادآوری',
    'hours' => 'ساعت',
    'sent_date' => 'تاریخ ارسال',
    'language' => 'زبان',
    
    // System Messages
    'email_sent_log' => 'ایمیل یادآوری شماره :reminder_number برای سفارش #:order_number به زبان :language ارسال شد',
    'processing_complete' => 'پردازش تکمیل شد. تعداد سفارشات پردازش شده: :count',
    
    // Instructions
    'instructions' => 'راهنمای استفاده',
    'instructions_text' => "1. این ماژول باید توسط کرون جاب WHMCS اجرا شود\n2. فایل cron/reminder_cron.php را در کرون جاب قرار دهید\n3. تنظیمات ایمیل را در قسمت تنظیمات WHMCS > ایمیل‌ها انجام دهید\n4. برای پشتیبانی چندزبانه، قالب‌های ایمیل را برای هر زبان ایجاد کنید",
    
    // Email Variables
    'invoice_link_text' => 'برای مشاهده فاکتور اینجا کلیک کنید',
    'client_area_text' => 'ورود به پنل کاربری',
    
    // Time Units
    'hour' => 'ساعت',
    'hours' => 'ساعت',
    'day' => 'روز',
    'days' => 'روز',
];