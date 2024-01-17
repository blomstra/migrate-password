<?php

namespace Blomstra\MigratePassword;

use Flarum\Extend as Flarum;

return [
    (new Flarum\Frontend('admin'))->js(__DIR__ . '/js/dist/admin.js'),
    (new Flarum\Middleware('forum'))->add(Middleware::class),
    (new Flarum\Locales(__DIR__ . '/resources/locale'))
];
