<?php
require_once __DIR__ . '/../../auth_check.php';
require_once __DIR__ . '/../../config.php';
require_once app_path('conn.php');
// Simple demo page for testing notification features. Must be logged in.
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: /index.php'); exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Notifications Demo</title>
    <link rel="stylesheet" href="/css/admin.css" />
    <style>
        body{font-family: Arial, Helvetica, sans-serif; padding:20px}
        .btn{display:inline-block;padding:8px 12px;margin:6px;border-radius:4px;background:#007bff;color:#fff;text-decoration:none}
        .btn.secondary{background:#6c757d}
        #output{white-space:pre-wrap;background:#f8f9fa;border:1px solid #e9ecef;padding:12px;margin-top:12px}
    </style>
</head>
<body>
    <h1>Notifications Demo</h1>
    <p>Logged in as user id: <strong><?= htmlspecialchars($userId) ?></strong></p>

    <div>
        <a class="btn" href="/User/Afterlogin/notifications.php">Open View-All (Notifications)</a>
        <a class="btn secondary" href="/User/Afterlogin/notifications_trash.php">Open Trash</a>
        <a class="btn" href="/admin/preview_notification.php">Admin Preview (requires admin)</a>
    </div>

    <h3>AJAX actions</h3>
    <div>
        <button id="btn-unread" class="btn">Get Unread Count</button>
        <button id="btn-mark-all" class="btn">Mark All Read</button>
        <button id="btn-clear-read" class="btn">Clear Read (soft-delete)</button>
        <button id="btn-cleanup" class="btn">Run Duplicate Cleanup (admin)</button>
    </div>

    <h3>Live Dropdown</h3>
    <p>Open the site header (on other pages with the bell) to see the dropdown live; this demo triggers endpoints directly.</p>

    <div id="output">Output will appear here.</div>

    <script>
    const out = (t) => { document.getElementById('output').textContent = (new Date()).toLocaleTimeString() + ' - ' + JSON.stringify(t, null, 2); };

    document.getElementById('btn-unread').addEventListener('click', async ()=>{
        try {
            const r = await fetch('/admin/get_unread_count.php', {credentials:'same-origin'});
            const j = await r.json(); out(j);
        } catch(e){ out({error:e.toString()}); }
    });

    document.getElementById('btn-mark-all').addEventListener('click', async ()=>{
        try {
            const r = await fetch('/admin/mark_all_notifications_read.php', {method:'POST', credentials:'same-origin'});
            const j = await r.json(); out(j);
        } catch(e){ out({error:e.toString()}); }
    });

    document.getElementById('btn-clear-read').addEventListener('click', async ()=>{
        try {
            const r = await fetch('/admin/clear_read_notifications.php', {method:'POST', credentials:'same-origin'});
            const j = await r.json(); out(j);
        } catch(e){ out({error:e.toString()}); }
    });

    document.getElementById('btn-cleanup').addEventListener('click', async ()=>{
        try {
            const r = await fetch('/admin/cleanup_duplicates.php', {method:'POST', credentials:'same-origin'});
            const j = await r.json(); out(j);
        } catch(e){ out({error:e.toString()}); }
    });
    </script>
</body>
</html>
