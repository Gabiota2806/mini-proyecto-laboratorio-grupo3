<?php

declare(strict_types=1);

namespace Tests\Reports;

use BasicReport;
use DigitalSignatureDecorator;
use PdfReportDecorator;
use PHPUnit\Framework\TestCase;
use Report;
use TypeError;
use WatermarkDecorator;

/**
 * Los textos esperados son EXACTAMENTE los mismos que producia el antiguo
 * ReportGenerator::generate() con banderas booleanas (ver commit e7add9b):
 * el refactor no cambia el comportamiento.
 *
 * Cada test indica el identificador del caso documentado en docs/CASOS-DE-PRUEBA.md.
 */
final class ReportTest extends TestCase
{
    /**
     * CP-DEC-01 (positivo): reporte base sin agregados.
     */
    public function testBasicReportWithoutExtras(): void
    {
        $this->assertSame('Reporte: Pedido 1', (new BasicReport('Pedido 1'))->generate());
    }

    /**
     * CP-DEC-03 (positivo): reporte del dia de OrderController con los tres agregados.
     */
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

    /**
     * CP-DEC-04 (positivo): reporte de pedido de OrderService con firma y PDF.
     */
    public function testOrderReportUsedByOrderService(): void
    {
        $reporte = new PdfReportDecorator(
            new DigitalSignatureDecorator(new BasicReport('Pedido 1'))
        );

        $this->assertSame('Reporte: Pedido 1 + firma digital + PDF', $reporte->generate());
    }

    /**
     * CP-DEC-01 (positivo): un decorador tambien es un Report y se puede usar solo.
     */
    public function testEachDecoratorIsAlsoAReport(): void
    {
        $reporte = new WatermarkDecorator(new BasicReport('Pedido 1'));

        $this->assertInstanceOf(Report::class, $reporte);
        $this->assertSame('Reporte: Pedido 1 + marca de agua', $reporte->generate());
    }

    /**
     * CP-DEC-02 (negativo): un decorador no acepta algo que no sea un Report.
     */
    public function testDecoratorRejectsSomethingThatIsNotAReport(): void
    {
        $this->expectException(TypeError::class);

        new PdfReportDecorator('Pedido 1');
    }

    /**
     * CP-DEC-05 (negativo): documenta la consecuencia negativa del patron:
     * el orden de los decoradores cambia el resultado. Si este test falla,
     * alguien cambio el orden acordado sin avisar.
     */
    public function testDecoratorOrderChangesTheResult(): void
    {
        $firmaYPdf = new PdfReportDecorator(new DigitalSignatureDecorator(new BasicReport('X')));
        $pdfYFirma = new DigitalSignatureDecorator(new PdfReportDecorator(new BasicReport('X')));

        $this->assertSame('Reporte: X + firma digital + PDF', $firmaYPdf->generate());
        $this->assertSame('Reporte: X + PDF + firma digital', $pdfYFirma->generate());
    }

    /**
     * CP-DEC-06 (negativo): limitacion conocida, nada impide aplicar dos veces
     * el mismo decorador. El test la deja registrada.
     */
    public function testSameDecoratorTwiceDuplicatesTheExtra(): void
    {
        $reporte = new PdfReportDecorator(new PdfReportDecorator(new BasicReport('X')));

        $this->assertSame('Reporte: X + PDF + PDF', $reporte->generate());
    }
}
