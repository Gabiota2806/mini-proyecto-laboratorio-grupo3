<?php

declare(strict_types=1);

namespace Tests\Reports;

use BasicReport;
use DigitalSignatureDecorator;
use PdfReportDecorator;
use PHPUnit\Framework\TestCase;
use Report;
use WatermarkDecorator;

/**
 * Los textos esperados son EXACTAMENTE los mismos que producia el antiguo
 * ReportGenerator::generate() con banderas booleanas (ver commit anterior):
 * el refactor no cambia el comportamiento.
 */
final class ReportTest extends TestCase
{
    public function testBasicReportWithoutExtras(): void
    {
        $this->assertSame('Reporte: Pedido 1', (new BasicReport('Pedido 1'))->generate());
    }

    public function testDailyReportUsedByOrderController(): void
    {
        $reporte = new WatermarkDecorator(
            new PdfReportDecorator(
                new DigitalSignatureDecorator(new BasicReport('Pedidos del dia'))
            )
        );

        $this->assertSame(
            'Reporte: Pedidos del dia + firma digital + PDF + marca de agua',
            $reporte->generate()
        );
    }

    public function testOrderReportUsedByOrderService(): void
    {
        $reporte = new PdfReportDecorator(
            new DigitalSignatureDecorator(new BasicReport('Pedido 1'))
        );

        $this->assertSame('Reporte: Pedido 1 + firma digital + PDF', $reporte->generate());
    }

    public function testEachDecoratorIsAlsoAReport(): void
    {
        $reporte = new WatermarkDecorator(new BasicReport('Pedido 1'));

        $this->assertInstanceOf(Report::class, $reporte);
        $this->assertSame('Reporte: Pedido 1 + marca de agua', $reporte->generate());
    }

    /**
     * Documenta la consecuencia negativa del patron: el orden de los
     * decoradores cambia el resultado. Si este test falla, alguien cambio
     * el orden acordado sin avisar.
     */
    public function testDecoratorOrderChangesTheResult(): void
    {
        $firmaYPdf = new PdfReportDecorator(new DigitalSignatureDecorator(new BasicReport('X')));
        $pdfYFirma = new DigitalSignatureDecorator(new PdfReportDecorator(new BasicReport('X')));

        $this->assertNotSame($firmaYPdf->generate(), $pdfYFirma->generate());
    }
}
