<?php

use Multiapi\Arco\Config\App;
use Multiapi\Arco\Config\Runtime;

// The Arco config of this starter. Arco Cloud rewrites the app name and the region on the two lines below
// when it creates your app from this template. It supplies queues, storage, cache and variables through its
// own config layer, so do not declare them here: the starter runs with no database until you add one in Arco Cloud.

return App::make('arco-starter')
    ->region('us-east-1')
    ->runtime(fn (Runtime $r) => $r->php('8.5'))
    ->console()
    ->deployCommands(['starter:migrate'])
    ->environment('staging');
