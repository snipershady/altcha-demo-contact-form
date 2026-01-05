# ALTCHA Demo Contact Form

A demonstration implementation of [ALTCHA](https://altcha.org) - a free, open-source CAPTCHA alternative that protects your forms from spam and abuse without requiring users to solve puzzles or identify images.

## 🌟 Features

- **Privacy-Friendly**: No tracking, no cookies, no data collection
- **Accessible**: Works for all users without visual challenges or puzzle-solving
- **Lightweight**: Minimal impact on page load times
- **Self-Hosted**: Complete control over your implementation
- **Proof-of-Work Based**: Uses cryptographic challenges instead of image recognition

## 📋 Requirements

- PHP 7.4 or higher
- Web server with SSL/HTTPS enabled (required for ALTCHA to function)
- Composer (for dependency management)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/snipershady/altcha-demo-contact-form.git
   cd altcha-demo-contact-form
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure your secret key**
   
   ⚠️ **Important**: The included secret key is for demonstration purposes only. For production use, generate a strong alphanumeric secret key of at least 64-128 characters.

4. **Set up SSL certificate**
   
   ALTCHA requires HTTPS to function properly. Options include:
   - Let's Encrypt (free, recommended for production)
   - Self-signed certificate (for local development)
   - Commercial SSL certificate

## 🏗️ Implementation Details

This demo uses:

- **Frontend**: ALTCHA widget loaded via CDN
- **Backend**: Official ALTCHA PHP library ([altcha-org/altcha](https://packagist.org/packages/altcha-org/altcha) v1.3+)
- **Integration**: Challenge creation and verification using the ALTCHA API

## 📖 Documentation

For complete integration documentation, visit the official ALTCHA documentation:
[https://altcha.org/docs/v2/](https://altcha.org/docs/v2/)

## 🔒 Security Considerations

### Production Checklist

- [ ] Replace the demo secret key with a strong, randomly generated key (64-128+ characters)
- [ ] Ensure HTTPS is properly configured
- [ ] Keep the `altcha-org/altcha` package updated
- [ ] Implement rate limiting on your endpoints
- [ ] Add proper input validation and sanitization
- [ ] Configure appropriate CORS headers if needed

### Secret Key Generation

Generate a secure secret key using one of these methods:

```bash
# Using OpenSSL
openssl rand -base64 96

# Using PHP
php -r "echo bin2hex(random_bytes(64));"
```

## ⚠️ Important Notes

1. **SSL Requirement**: ALTCHA will NOT work without HTTPS. In local development environments without SSL certificates (self-signed or Let's Encrypt), the widget will not function.

2. **Demo Purpose**: This repository is intended for demonstration and learning purposes. Additional hardening and customization are recommended for production use.

3. **Secret Key**: Never commit your production secret key to version control. Use environment variables or secure configuration management.

## 🛠️ Usage

### Basic Integration

1. Include the ALTCHA widget in your HTML form:
```html
<form action="submit.php" method="POST">
  <!-- Your form fields -->
  <altcha-widget challengeurl="api/challenge.php"></altcha-widget>
  <button type="submit">Submit</button>
</form>
```

2. Create challenge endpoint (`api/challenge.php`):
```php
<?php
require_once 'vendor/autoload.php';

use Altcha\Altcha;

$hmacKey = 'your-secret-key-here';
$challenge = Altcha::createChallenge($hmacKey);

header('Content-Type: application/json');
echo json_encode($challenge);
```

3. Verify submission:
```php
<?php
require_once 'vendor/autoload.php';

use Altcha\Altcha;

$payload = $_POST['altcha'];
$hmacKey = 'your-secret-key-here';

if (Altcha::verifySolution($payload, $hmacKey)) {
    // Valid - process form
} else {
    // Invalid - reject submission
}
```

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/snipershady/altcha-demo-contact-form/issues).

## 📝 License

This demo project is provided as-is for educational purposes. Please refer to the [ALTCHA project](https://github.com/altcha-org/altcha) for its license terms.

## 🔗 Links

- [ALTCHA Official Website](https://altcha.org)
- [ALTCHA Documentation](https://altcha.org/docs/v2/)
- [ALTCHA GitHub](https://github.com/altcha-org/altcha)
- [ALTCHA PHP Library](https://github.com/altcha-org/altcha-lib-php)

## 💡 Support

If you find this demo helpful, please consider:
- ⭐ Starring this repository
- 🐛 Reporting issues or bugs
- 💬 Sharing your feedback
- 🤝 Contributing improvements

## ⚖️ Why ALTCHA?

Traditional CAPTCHAs often:
- Frustrate users with difficult challenges
- Exclude users with disabilities
- Track users across websites
- Slow down form submissions

ALTCHA provides a modern alternative that:
- Works invisibly in the background
- Respects user privacy
- Remains accessible to all users
- Prevents automated spam effectively

---

**Note**: This is a demonstration project. For production deployments, ensure you follow security best practices and properly configure all components according to your specific requirements.