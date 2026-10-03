<?php

/**
 * Builds resources/css/wiretables.css: the core plus every theme, in one file.
 *
 * Generated and committed, not stitched with @import: an app that links the published CSS
 * with <link> would otherwise make one request per theme, one after the other. Apps that want
 * less import wiretables-core.css and the one theme they use. Run after editing either:
 *
 *     php bin/build-css.php
 *
 * CssTest fails while the committed file differs from what this builds.
 */

require __DIR__.'/../src/Support/Css.php';

file_put_contents(__DIR__.'/../resources/css/wiretables.css', EduLazaro\Wiretables\Support\Css::bundle());

echo "resources/css/wiretables.css built\n";
