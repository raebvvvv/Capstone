<?php
// Example email configuration file. Copy to project root as email_config.php and fill in your SMTP details.
// DO NOT COMMIT real credentials to source control.
return [
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => 'your_smtp_username',
        'password' => 'your_smtp_password',
        'encryption' => 'tls', // 'tls' or 'ssl'
        'from_email' => 'no-reply@example.com',
        'from_name' => 'PUP e-IPMO',
    ],
];
