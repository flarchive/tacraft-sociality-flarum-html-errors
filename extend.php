<?php

namespace TacraftSociality\HtmlErrors;

use Flarum\Extend;
use Flarum\Http\Middleware\ResolveRoute;

return [
    (new Extend\Middleware('forum'))
        ->insertBefore(ResolveRoute::class, RedirectErrorMiddleware::class),
];