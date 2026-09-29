<?php
declare(strict_types=1);

$configFile = dirname(__DIR__) . '/config/settings.php';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = trim((string)(isset($_POST['api_key']) ? $_POST['api_key'] : ''));
    $location = trim((string)(isset($_POST['default_location']) ? $_POST['default_location'] : ''));
    $timeout = max(5, min(300, (int)(isset($_POST['timeout']) ? $_POST['timeout'] : 60)));
    $verifySsl = isset($_POST['verify_ssl']);

    $contents = "<?php\nreturn " . var_export([
        'api_key' => $apiKey,
        'base_url' => 'https://api.seoreviewtools.com/v3/',
        'timeout' => $timeout,
        'verify_ssl' => $verifySsl,
        'default_location' => $location,
    ], true) . ";\n";

    if (@file_put_contents($configFile, $contents, LOCK_EX) === false) {
        $error = 'Could not write config/settings.php. Make sure the config directory is writable.';
    } else {
        $message = 'Settings saved.';
    }
}

$settings = is_file($configFile) ? (require $configFile) : [];
$apiKey = (string)(isset($settings['api_key']) ? $settings['api_key'] : '');
$location = (string)(isset($settings['default_location']) ? $settings['default_location'] : '');
$timeout = (int)(isset($settings['timeout']) ? $settings['timeout'] : 60);
$verifySsl = (bool)(isset($settings['verify_ssl']) ? $settings['verify_ssl'] : true);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>AI Visibility Library — Settings</title>
<style>
body{font:16px/1.5 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#f5f6f8;margin:0;padding:40px;color:#222}.wrap{max-width:760px;margin:auto;background:#fff;padding:32px;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,.08)}h1{margin-top:0}label{display:block;font-weight:600;margin:20px 0 7px}input[type=text],input[type=password],input[type=number]{width:100%;box-sizing:border-box;padding:11px;border:1px solid #ccd1d8;border-radius:6px;font-size:15px}button{margin-top:24px;padding:11px 18px;border:0;border-radius:6px;background:#222;color:#fff;cursor:pointer}.notice{padding:12px;border-radius:6px;margin-bottom:20px}.ok{background:#e9f7ed}.err{background:#fdecec}.hint{color:#666;font-size:14px}.warning{margin-top:28px;padding:14px;background:#fff4d6;border-left:4px solid #e2a900}
</style>
</head>
<body><main class="wrap">
<h1>AI Visibility Library</h1>
<p>Configure the shared SEO Review Tools AI Visibility API client.</p>
<?php if ($message): ?><div class="notice ok"><?=htmlspecialchars($message)?></div><?php endif; ?>
<?php if ($error): ?><div class="notice err"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post">
<label for="api_key">API key</label>
<input id="api_key" type="password" name="api_key" value="<?=htmlspecialchars($apiKey)?>" autocomplete="off">
<p class="hint">Stored in config/settings.php. Keep this directory outside public web access when possible.</p>
<label for="default_location">Default country / location</label>
<input id="default_location" type="text" name="default_location" value="<?=htmlspecialchars($location)?>" placeholder="e.g. United States">
<label for="timeout">Request timeout (seconds)</label>
<input id="timeout" type="number" name="timeout" min="5" max="300" value="<?=htmlspecialchars((string)$timeout)?>">
<label><input type="checkbox" name="verify_ssl" value="1" <?=$verifySsl?'checked':''?>> Verify SSL certificates</label>
<button type="submit">Save settings</button>
</form>
<div class="warning"><strong>Security:</strong> protect this settings page and the <code>config</code> directory. Do not commit your API key to a public Git repository.</div>
</main></body></html>
