<?php
/**
 * ============================================================================
 *  GENERACION DE REPORTES
 *  Patron aplicado: DECORATOR (estructural)
 * ============================================================================
 *
 *  Refactorizado segun metodologia Refactoring.Guru:
 *  - Interfaz comun Report para el componente base y los decoradores.
 *  - Componente concreto BasicReport que define el contenido primordial.
 *  - Clase abstracta ReportDecorator y decoradores concretos:
 *      * DigitalSignatureDecorator
 *      * PdfReportDecorator (Ejercicio 4)
 *      * WatermarkDecorator (Ejercicio 4)
 *  - Se elimina el "boolean trap" permitiendo componer agregados dinamicamente
 *    en tiempo de ejecucion en cualquier combinacion.
 * ============================================================================
 */

declare(strict_types=1);

/**
 * Contrato base para la generacion de reportes.
 */
interface Report
{
    public function generate(): string;
}

/**
 * Componente base concreto que genera el cuerpo principal del reporte.
 */
final class BasicReport implements Report
{
    public function __construct(private string $contenido) {}

    public function generate(): string
    {
        return "Reporte: {$this->contenido}";
    }
}

/**
 * Decorador base que delega la ejecucion en el objeto Report envuelto.
 */
abstract class ReportDecorator implements Report
{
    public function __construct(protected Report $report) {}

    public function generate(): string
    {
        return $this->report->generate();
    }
}

/**
 * Agrega firma digital criptografica al reporte.
 */
final class DigitalSignatureDecorator extends ReportDecorator
{
    public function generate(): string
    {
        return parent::generate() . ' + firma digital';
    }
}

/**
 * Transforma o exporta el reporte en formato PDF.
 * Resolucion del Ejercicio 4 del TP.
 */
final class PdfReportDecorator extends ReportDecorator
{
    public function generate(): string
    {
        return parent::generate() . ' + PDF';
    }
}

/**
 * Incorpora una marca de agua de seguridad institucional al reporte.
 * Resolucion del Ejercicio 4 del TP.
 */
final class WatermarkDecorator extends ReportDecorator
{
    public function generate(): string
    {
        return parent::generate() . ' + marca de agua';
    }
}

/**
 * Generador y facade auxiliar de reportes.
 * Mantenido con retrocompatibilidad para codigo cliente legacy,
 * delegando la composicion en la cadena de decoradores.
 */
class ReportGenerator
{
    /**
     * Construye y ejecuta dinamicamente la cadena de decoradores segun las opciones activas.
     */
    public function generate(
        string $contenido,
        bool $conFirmaDigital = false,
        bool $conPdf = false,
        bool $conMarcaDeAgua = false
    ): string {
        $report = new BasicReport($contenido);

        if ($conFirmaDigital) {
            $report = new DigitalSignatureDecorator($report);
        }

        if ($conPdf) {
            $report = new PdfReportDecorator($report);
        }

        if ($conMarcaDeAgua) {
            $report = new WatermarkDecorator($report);
        }

        return $report->generate();
    }
}
