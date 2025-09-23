<?php

function setupErrorLogging() {
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL);
    ini_set('error_log', __DIR__ . '/../logs/debug.log');
}

function logDebug($message, $data = null) {
    $log = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($data) {
        $log .= "\nData: " . print_r($data, true);
    }
    error_log($log . "\n");
}