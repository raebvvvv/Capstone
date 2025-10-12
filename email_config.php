<?php
// Centralized email configuration sourced from environment variables.
// This file contains no secrets and is safe to commit if .env is ignored.

require_once __DIR__ . '/env_config.php';

return [
  'smtp' => [
    'host' => Environment::get('SMTP_HOST', 'smtp.gmail.com'),
    'port' => (int) Environment::get('SMTP_PORT', 587),
    'username' => Environment::get('SMTP_USERNAME', ''),
    'password' => Environment::get('SMTP_PASSWORD', ''),
    'encryption' => Environment::get('SMTP_ENCRYPTION', 'tls'),
    'from_email' => Environment::get('SMTP_FROM_EMAIL', ''),
    'from_name' => Environment::get('SMTP_FROM_NAME', 'PUP e-IPMO'),
  ],
];
