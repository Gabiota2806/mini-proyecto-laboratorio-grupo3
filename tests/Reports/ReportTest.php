<?php

declare(strict_types=1);

namespace Tests\Reports;

use PHPUnit\Framework\TestCase;
use ReportGenerator;

/**
 * Tests de caracterizacion: fijan la salida ACTUAL del generador de reportes
 * antes del refactor, para demostrar que el comportamiento no cambia.
 */
final class ReportTest extends TestCase
{
    public function testBasicReportWithoutExtras(): void
    {
        $this->assertSame(
            'Reporte: Pedido 1',
            (new ReportGenerator())->generate('Pedido 1')
        );
    }

    public function testDailyReportUsedByOrderController(): void
    {
        $this->assertSame(
            'Reporte: Pedidos del dia + firma digital + PDF + marca de agua',
            (new ReportGenerator())->generate('Pedidos del dia', true, true, true)
        );
    }

    public function testOrderReportUsedByOrderService(): void
    {
        $this->assertSame(
            'Reporte: Pedido 1 + firma digital + PDF',
            (new ReportGenerator())->generate('Pedido 1', true, true, false)
        );
    }
}
