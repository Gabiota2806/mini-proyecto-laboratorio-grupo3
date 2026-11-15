<?php

declare(strict_types=1);

namespace Tests\Legacy;

use LegacyNotifier;
use LegacyNotifierAdapter;
use Notification;
use PHPUnit\Framework\TestCase;

final class LegacyNotifierAdapterTest extends TestCase
{
    public function testAdapterImplementsNotificationInterface(): void
    {
        $legacy = new LegacyNotifier();
        $adapter = new LegacyNotifierAdapter($legacy);

        $this->assertInstanceOf(Notification::class, $adapter);
    }

    public function testAdapterTranslatesSendToLegacySendMessage(): void
    {
        $legacy = new LegacyNotifier();
        $adapter = new LegacyNotifierAdapter($legacy);

        $this->expectOutputString("[LEGACY] Aviso legado de prueba<br>");
        $adapter->send('Aviso legado de prueba');
    }
}
