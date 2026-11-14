<?php

/**
 * ============================================================================
 *  GENERACION DE REPORTES
 *  Patron aplicado: DECORATOR (estructural)
 * ============================================================================
 *
 *  Reemplaza al antiguo ReportGenerator::generate(string, bool, bool, bool):
 *  - Report es el contrato comun del reporte base y de cada agregado.
 *  - BasicReport genera el contenido sin agregados.
 *  - Cada agregado (firma, PDF, marca de agua) es un decorador que envuelve
 *    a otro Report y le suma su parte. Agregar uno nuevo (ej. codigo QR) es
 *    crear una clase, sin modificar las existentes (OCP).
 *
 *  ⚠️ EL ORDEN IMPORTA: cada decorador agrega su texto DESPUES del que envuelve.
 *     El orden acordado para el laboratorio es, de adentro hacia afuera:
 *        BasicReport -> DigitalSignatureDecorator -> PdfReportDecorator -> WatermarkDecorator
 *     Ejemplo:
 *        new WatermarkDecorator(
 *            new PdfReportDecorator(
 *                new DigitalSignatureDecorator(new BasicReport('Pedido 1'))
 *            )
 *        )
 * ============================================================================
 */

declare(strict_types=1);

/**
 * Contrato comun: lo cumplen el reporte base y todos los decoradores.
 */
interface Report
{
    public function generate(): string;
}

/**
 * Reporte base, sin agregados. Es el objeto que envuelven los decoradores.
 */
final class BasicReport implements Report
{
    public function __construct(private string $contenido)
    {
    }

    public function generate(): string
    {
        return "Reporte: {$this->contenido}";
    }
}

/**
 * Agrega la firma digital al reporte que envuelve.
 */
final class DigitalSignatureDecorator implements Report
{
    public function __construct(private Report $report)
    {
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + firma digital';
    }
}

/**
 * Agrega la salida en PDF al reporte que envuelve.
 */
final class PdfReportDecorator implements Report
{
    public function __construct(private Report $report)
    {
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + PDF';
    }
}

/**
 * Agrega la marca de agua al reporte que envuelve.
 */
final class WatermarkDecorator implements Report
{
    public function __construct(private Report $report)
    {
    }

    public function generate(): string
    {
        return $this->report->generate() . ' + marca de agua';
    }
}
