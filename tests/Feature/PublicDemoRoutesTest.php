<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

final class PublicDemoRoutesTest extends TestCase
{
    public function test_public_demo_controls_remain_declared_without_auth_middleware(): void
    {
        $routes = file_get_contents(__DIR__.'/../../routes/api.php');
        $this->assertNotFalse($routes);

        foreach (['/start', '/stop', '/reset', '/manual', '/auto'] as $path) {
            $this->assertStringContainsString("Route::post('".$path."'", $routes);
        }

        $this->assertStringContainsString("throttle:30,1", $routes);
    }

    public function test_health_route_is_present(): void
    {
        $routes = file_get_contents(__DIR__.'/../../routes/api.php');
        $this->assertNotFalse($routes);
        $this->assertStringContainsString("Route::get('/health'", $routes);
    }
}
