# Reminder-order-WHMCS
A professional WHMCS module that sends automated payment reminders for pending orders. The module supports multilingual interface and auto-detects user language.
# WHMCS Order Reminder & Donation Module

A professional WHMCS module that sends automated payment reminders for pending orders and includes a crypto donation button. The module supports multilingual interface and auto-detects user language.

## 📦 Features

### 🔔 Order Reminder System
- Automatically sends reminder emails for unpaid orders
- Configurable reminder intervals (e.g., 24, 48, 72 hours)
- Multiple reminder limit per order
- Exclude domain orders from reminders (optional)
- Test mode for safe testing
- Complete logging of all sent emails
- Multilingual email templates


### 🌍 Multilingual Support
- Auto-detects user language (admin/client)
- Configurable language override
- Currently includes: English, Persian, Arabic
- Easy to add more languages
- All interface texts translatable

## 🛠 Installation

1. **Upload files** to `/modules/addons/reminder/`
2. **Activate module** in WHMCS Admin Area > Setup > Addon Modules
3. **Configure settings** in the module interface
4. **Set up cron job** to run `reminder_cron.php` hourly

### 📁 File Structure
```
/modules/addons/reminder/
├── reminder.php              # Main module file
├── reminder_cron.php         # Cron job processor
├── /includes/
│   └── functions.php         # Helper functions
├── /lang/
│   ├── english.php           # English language
│   ├── persian.php           # Persian language
│   └── arabic.php            # Arabic language
└── README.md                 # This file
```

## ⚙️ Configuration

### General Settings
- **Enable Module**: Turn the module on/off
- **Reminder Hours**: Comma-separated hours (24,48,72)
- **Email Template**: Select email template for reminders
- **Max Reminders**: Maximum reminders per order (1-10)
- **Exclude Domains**: Skip domain order reminders
- **Test Mode**: Send test emails to admin only
- **Admin Email**: Test email recipient

### Language Settings
- **Language Override**: Force specific language or auto-detect
- Supports: English, Persian, Arabic, French, German, Spanish, Italian, Russian, Turkish


## 🔧 Cron Job Setup

Add this to your server's cron jobs (run hourly):
```bash
0 * * * * php /path/to/whmcs/modules/addons/reminder/reminder_cron.php
```

## 📊 Statistics

The module provides detailed statistics:
- Total emails sent
- Pending orders count
- Recent email logs with language info
- Complete audit trail

## 🚀 Usage

### For Admin
1. Configure reminder settings
2. Monitor statistics from module dashboard
3. Use donation button to support development
4. View all sent reminders in log table

### For Clients
- Automatically receives reminder emails in their language
- Clear invoice links in emails
- Professional, translated email content

## 🔒 Security Features

- CSRF protection on all forms
- API key protection for donations
- Input sanitization and validation
- Secure database queries
- Session-based token management

## 🎨 Donation System

The module includes a beautiful donation button in the admin settings area. All donations go directly to the module developer via NOWPayments.

### Donation Features
- Beautiful gradient design
- Multiple crypto currency support
- Secure NOWPayments integration
- Optional display in admin area
- Customizable button text

## 🌐 Adding New Languages

1. Copy `/lang/english.php` to new language file
2. Translate all text strings
3. Add language to `getAvailableLanguages()` function
4. Create email templates in that language

## 🐛 Troubleshooting

### Common Issues

1. **Emails not sending**: Check cron job configuration
2. **Invalid CSRF token**: Clear browser cache and cookies
3. **Language not detected**: Check client language setting
4. **Donation button not showing**: Enable in module settings

### Logs
All module activities are logged in WHMCS activity log and module's own log table.

## 📄 License

This module is released under the **MIT License** (free and open source). You are free to:
- Use commercially
- Modify and distribute
- Use privately
- Sublicense
- Place warranty

### Copyright Notice
```
WHMCS Order Reminder & Donation Module
Copyright (c) 2024 Module Developer

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## ❤️ Support & Donations

If you find this module useful, please consider supporting its development:

[![Donate with Crypto](https://nowpayments.io/images/embeds/donation-button-black.svg)](https://nowpayments.io/donation?api_key=3dac6108-1f9b-4eb0-abf8-be38dc47f72a)

Your donations help cover:
- Server and hosting costs
- Continued development and updates
- Adding new features
- Supporting more languages

## 🤝 Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📞 Support

For support, feature requests, or bug reports:
1. Check the GitHub Issues page
2. Create a new issue with details
3. Include WHMCS version and PHP version
4. sarvhost.net

## 📈 Version History

- **v1.0** - Initial release with basic reminder system
- **v1.1** - Added multilingual support
- **v1.2** - Added donation system and improved UI
- **v1.3** - Enhanced security and bug fixes

---

**Note**: This module is independently developed and not affiliated with WHMCS. WHMCS is a registered trademark of WHMCS Limited.
