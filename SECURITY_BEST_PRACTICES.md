# Security Best Practices for Laravel ERP

## 1. Login Page Protection

### A. Rate Limiting (Laravel)
- **5 login attempts per minute** per IP address
- Configured in `routes/web.php` with `throttle:5,1`
- Returns 429 (Too Many Requests) after limit exceeded

### B. Server-Level Protection (Fail2Ban)
- Monitors failed login attempts in Nginx logs
- **Auto-bans IPs** after 5 failed attempts within 10 minutes
- Ban duration: 1 hour (configurable)

### C. Multi-Layer Defense
```
User Request
    ↓
Nginx (Rate Limiting)
    ↓
Fail2Ban (Monitor & Ban)
    ↓
Laravel (Throttle Middleware)
    ↓
Login Controller
```

---

## 2. Bot/Spam Prevention

### A. CAPTCHA (Future Enhancement)
Consider adding for production:
```bash
# Install Laravel Captcha
composer require mews/captcha

# Add to login form
{!! captcha_img() !!}
```

### B. Content Security Policy (CSP)
Already configured in Nginx with security headers:
```nginx
add_header Strict-Transport-Security "max-age=31536000";
add_header X-Frame-Options "SAMEORIGIN";
add_header X-Content-Type-Options "nosniff";
add_header X-XSS-Protection "1; mode=block";
```

### C. Block Known Bots
Update Nginx config to block common bots:
```nginx
# Add to server block in /etc/nginx/sites-available/muhaseb-pro
if ($http_user_agent ~* (bot|crawler|spider|scraper)) {
    return 403;
}
```

---

## 3. Brute Force Attack Mitigation

### A. Failed Login Logging
Laravel logs failed attempts. Check:
```bash
tail -f /var/www/muhaseb-pro/storage/logs/laravel.log
```

### B. Account Lockout (Optional Future Feature)
```php
// In LoginController
protected $maxLoginAttempts = 5;
protected $lockoutTime = 3600; // 1 hour
```

### C. Monitor Attempts
```bash
# Check blocked IPs
sudo fail2ban-client status nginx-http-auth

# View recent login attempts
grep "POST /login" /var/log/nginx/muhaseb-pro_access.log | tail -20
```

---

## 4. Database Security

### A. Connection Security
- Database only listens on `127.0.0.1` (localhost)
- Not exposed to internet ✅
- Connection uses socket or local IP only

### B. Credentials Protection
- `.env` file not committed to git ✅
- Database password configured securely
- App runs with `www-data` user (limited privileges)

### C. SQL Injection Prevention
- Uses Laravel Eloquent ORM (parameterized queries)
- Never use raw SQL without binding parameters
- All user input is escaped

---

## 5. Application Security

### A. Environment Configuration
```env
APP_DEBUG=false          # Disable debug mode in production
APP_ENV=production       # Set to production
HTTPS_ONLY=true         # Force HTTPS
```

### B. SSL/TLS Certificate
- Let's Encrypt certificate (auto-renewing)
- Enforces HTTPS redirect from HTTP
- Strong cipher suites configured

### C. Session Security
```php
// In config/session.php (default settings are secure)
'secure' => true,        // Only send over HTTPS
'http_only' => true,     // No JavaScript access
'same_site' => 'lax',    // CSRF protection
```

---

## 6. Recommended Additional Security Measures

### A. Web Application Firewall (WAF)
```bash
# Option 1: ModSecurity with Nginx
sudo apt install -y libnginx-mod-http-modsecurity

# Option 2: Use Cloudflare (DNS level)
# - DDoS protection
# - Bot management
# - WAF rules
```

### B. Two-Factor Authentication (2FA)
```bash
composer require laravel/fortify
php artisan fortify:install
```

### C. IP Whitelist (for admin access)
```nginx
# In Nginx config for /login
location ~* ^/(login|admin) {
    allow 192.168.1.0/24;   # Your office network
    allow YOUR_HOME_IP;      # Your home IP
    deny all;
}
```

### D. Security Headers
Already configured in Nginx:
```
✅ Strict-Transport-Security (HSTS)
✅ X-Frame-Options
✅ X-Content-Type-Options
✅ X-XSS-Protection
✅ Referrer-Policy
✅ Permissions-Policy
```

---

## 7. Monitoring & Alerts

### A. Log Monitoring
```bash
# Monitor failed logins
grep "failed" /var/www/muhaseb-pro/storage/logs/laravel.log

# Monitor Nginx errors
tail -f /var/log/nginx/muhaseb-pro_error.log

# Monitor Fail2Ban
sudo journalctl -u fail2ban -f
```

### B. Set Up Alerts (Optional)
```bash
# Email alerts for failed login attempts
# Use tools like: logwatch, mtail, or custom scripts
```

### C. Regular Security Audits
```bash
# Check for vulnerabilities
composer audit

# Check for outdated packages
composer outdated

# Scan for security issues
npm audit
```

---

## 8. Deployment Security Checklist

- [x] Registration disabled (admin-only user creation)
- [x] Rate limiting enabled on login (5 attempts/min)
- [x] Fail2Ban configured (auto-ban after 5 failures)
- [x] Database isolated (localhost only)
- [x] SSL/TLS enabled (HTTPS only)
- [x] Security headers configured
- [x] `.env` file protected
- [x] Debug mode disabled
- [x] Logs monitored
- [ ] Two-factor authentication (optional)
- [ ] CAPTCHA (optional)
- [ ] IP whitelist (optional for admin)

---

## 9. Incident Response

### If Your Site Gets Attacked:

1. **Check Fail2Ban logs:**
   ```bash
   sudo fail2ban-client status
   tail -f /var/log/fail2ban.log
   ```

2. **Identify malicious IPs:**
   ```bash
   grep "POST /login" /var/log/nginx/muhaseb-pro_access.log | awk '{print $1}' | sort | uniq -c | sort -rn
   ```

3. **Manually ban an IP:**
   ```bash
   sudo fail2ban-client set nginx-http-auth banip <IP>
   ```

4. **Review application logs:**
   ```bash
   tail -f /var/www/muhaseb-pro/storage/logs/laravel.log
   ```

5. **Whitelist legitimate traffic (if blocked):**
   ```bash
   sudo fail2ban-client set nginx-http-auth unbanip <IP>
   ```

---

## 10. References

- [Laravel Security](https://laravel.com/docs/security)
- [Fail2Ban Documentation](https://www.fail2ban.org/)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Nginx Security Headers](https://www.nginx.com/resources/wiki/start/topics/tutorials/config_pitfalls/)

---

**Last Updated:** November 10, 2025
**Version:** 1.0
