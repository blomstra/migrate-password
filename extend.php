<?php

namespace Blomstra\MigratePassword;

use Flarum\Extend as Flarum;
use Flarum\Http\Middleware\StartSession;

return [
    (new Flarum\Frontend('admin'))->js(__DIR__ . '/js/dist/admin.js'),
    (new Flarum\Middleware('forum'))->insertAfter(StartSession::class, Middleware::class),
    (new Flarum\Locales(__DIR__ . '/resources/locale'))
];
