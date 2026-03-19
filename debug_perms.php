<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = $argv[1] ?? 'admin@greenpoint.com';
$mode = $argv[2] ?? 'perms';
$user = User::query()->where('email', $email)->first();

if (!$user) {
    fwrite(STDOUT, "NO_USER {$email}\n");
    exit(0);
}

$roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
$perms = method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name')->toArray() : [];

fwrite(STDOUT, "USER_ID={$user->id}\n");
fwrite(STDOUT, 'EMAIL=' . $user->email . "\n");
fwrite(STDOUT, 'ROLES=' . json_encode($roles) . "\n");
fwrite(STDOUT, 'PERM_COUNT=' . count($perms) . "\n");
fwrite(STDOUT, 'ALL=' . (in_array('Administrador', $roles, true) ? 'true' : 'false') . "\n");

if ($mode === 'token') {
    $token = $user->createToken('debug')->plainTextToken;
    fwrite(STDOUT, "TOKEN={$token}\n");
}

