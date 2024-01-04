<?php

use Tonysm\ImportmapLaravel\Facades\Importmap;

Importmap::pinAllFrom('resources/js', to: 'js/', preload: true);
Importmap::pin('@hotwired/turbo', to: 'https://ga.jspm.io/npm:@hotwired/turbo@8.0.0-beta.1/dist/turbo.es2017-esm.js');
Importmap::pin('laravel-echo', to: 'https://ga.jspm.io/npm:laravel-echo@1.15.3/dist/echo.js');
Importmap::pin('pusher-js', to: 'https://ga.jspm.io/npm:pusher-js@8.4.0-rc1/dist/web/pusher.js');
Importmap::pin('@hotwired/stimulus', to: 'https://ga.jspm.io/npm:@hotwired/stimulus@3.2.2/dist/stimulus.js');
Importmap::pin('@hotwired/strada', to: 'https://ga.jspm.io/npm:@hotwired/strada@1.0.0-beta1/dist/strada.js');
Importmap::pin('@hotwired/stimulus-loading', to: 'vendor/stimulus-laravel/stimulus-loading.js', preload: true);
Importmap::pin('axios', to: 'https://ga.jspm.io/npm:axios@0.27.2/index.js');
Importmap::pin('el-transition', to: 'https://ga.jspm.io/npm:el-transition@0.0.7/index.js');
Importmap::pin('#lib/adapters/http.js', to: 'https://ga.jspm.io/npm:axios@0.27.2/lib/adapters/xhr.js');
Importmap::pin('#lib/defaults/env/FormData.js', to: 'https://ga.jspm.io/npm:axios@0.27.2/lib/helpers/null.js');
Importmap::pin('buffer', to: 'https://ga.jspm.io/npm:@jspm/core@2.0.1/nodelibs/browser/buffer.js');
Importmap::pin('@formkit/auto-animate', to: 'https://ga.jspm.io/npm:@formkit/auto-animate@0.8.1/index.mjs');
Importmap::pin('current.js', to: 'https://ga.jspm.io/npm:current.js@0.2.0/dist/current.js');
