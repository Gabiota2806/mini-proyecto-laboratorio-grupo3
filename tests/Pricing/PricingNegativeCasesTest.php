<?php

declare(strict_types=1);

namespace Tests\Pricing;

use InsuranceStrategy;
use PHPUnit\Framework\TestCase;
use PriceCalculator;
use TypeError;

/**
 * Casos NEGATIVOS del calculo de precios (Strategy).
 * Cada test indica el identificador del caso documentado en docs/CASOS-DE-PRUEBA.md.
 */
final class PricingNegativeCasesTest extends TestCase
{
    /**
     * CP-STR-02: un monto que no es numerico se rechaza.
     */
    public function testNonNumericAmountIsRejected(): void
    {
        $this->expectException(TypeError::class);

        (new InsuranceStrategy())->calculate('abc');
    }

    /**
     * CP-STR-04: el contexto no acepta un objeto que no cumple PricingStrategy.
     */
    public function testCalculatorRejectsObjectThatIsNotAStrategy(): void
    {
        $this->expectException(TypeError::class);

        (new PriceCalculator())->setStrategy(new \stdClass());
    }

    /**
     * CP-STR-06: con tasa de IVA 0 se devuelve el monto sin recargo.
     */
    public function testZeroVatRateReturnsAmountWithoutSurcharge(): void
    {
        $calculator = new PriceCalculator(new InsuranceStrategy());

        $this->assertEqualsWithDelta(700.0, $calculator->calculateWithVat(1000.0, 0.0), 0.0001);
    }

    /**
     * CP-STR-08: un tipo de paciente desconocido se cobra como particular (sin descuento).
     */
    public function testUnknownPatientTypeIsChargedAsPrivate(): void
    {
        $this->assertSame(1000.0, (new PriceCalculator())->calcular(1000.0, 'desconocido'));
    }
}
