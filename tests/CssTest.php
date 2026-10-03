<?php

namespace EduLazaro\Wiretables\Tests;

use EduLazaro\Wiretables\Support\Css;
use PHPUnit\Framework\TestCase;

class CssTest extends TestCase
{
    public function test_the_bundle_is_the_core_and_every_theme(): void
    {
        $this->assertSame(
            Css::bundle(),
            file_get_contents(__DIR__.'/../resources/css/wiretables.css'),
            'wiretables.css is out of date: run `php bin/build-css.php`.',
        );
    }

    public function test_the_core_carries_no_theme_and_each_theme_has_its_file(): void
    {
        $core = file_get_contents(__DIR__.'/../resources/css/wiretables-core.css');

        $this->assertStringNotContainsString('[data-wire-theme="', $core);

        foreach (Css::THEMES as $theme) {
            $this->assertStringContainsString("[data-wire-theme=\"{$theme}\"]", file_get_contents(__DIR__."/../resources/css/themes/{$theme}.css"));
        }

        $this->assertCount(count(Css::THEMES), glob(__DIR__.'/../resources/css/themes/*.css'));
    }
}
