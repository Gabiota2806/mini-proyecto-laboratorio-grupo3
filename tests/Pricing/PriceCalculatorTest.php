<?php

declare(strict_types=1);

namespace Tests\Pricing;

use InsuranceStrategy;
use PHPUnit\Framework\TestCase;
use PrepaidStrategy;
use PriceCalculator;
use PricingStrategy;
use RetiredPatientStrategy;

final class PriceCalculatorTest extends TestCase
{
    public function testDefaultStrategyIsPrivatePatient(): void
    {
        $calculator = new PriceCalculator();
        $this->assertSame(1500.0, $calculator->calculate(1500.0));
    }

    public function testSetStrategyDynamicallySwitchesCalculation(): void
    {
        $calculator = new PriceCalculator();
        $this->assertSame(1000.0, $calculator->calculate(1000.0));

        $calculator->setStrategy(new InsuranceStrategy());
        $this->assertEqualsWithDelta(700.0, $calculator->calculate(1000.0), 0.0001);

        $calculator->setStrategy(new PrepaidStrategy());
        $this->assertEqualsWithDelta(800.0, $calculator->calculate(1000.0), 0.0001);
    }

    public function testCalculateWithVat(): void
    {
        $calculator = new PriceCalculator(new InsuranceStrategy());
        // Base 1000 -> 700 con obra social -> + 21% IVA = 847.0
        $this->assertEqualsWithDelta(847.0, $calculator->calculateWithVat(1000.0), 0.0001);
    }

    public function testLegacyBridgeMethodCalcular(): void
    {
        $calculator = new PriceCalculator();

        $this->assertSame(1000.0, $calculator->calcular(1000.0, 'particular'));
        $this->assertEqualsWithDelta(700.0, $calculator->calcular(1000.0, 'obra_social'), 0.0001);
        $this->assertEqualsWithDelta(500.0, $calculator->calcular(1000.0, 'jubilado'), 0.0001);
        $this->assertEqualsWithDelta(800.0, $calculator->calcular(1000.0, 'prepaga'), 0.0001);
        $this->assertSame(1000.0, $calculator->calcular(1000.0, 'desconocido'));
    }

    public function testLegacyBridgeMethodCalcularConIva(): void
    {
        $calculator = new PriceCalculator();
        $this->assertEqualsWithDelta(1210.0, $calculator->calcularConIva(1000.0, 'particular'), 0.0001);
        $this->assertEqualsWithDelta(847.0, $calculator->calcularConIva(1000.0, 'obra_social'), 0.0001);
    }

    public function testOpenClosedPrincipleWithCustomStrategy(): void
    {
        $customStrategy = new class implements PricingStrategy {
            public function calculate(float $amount): float
            {
                return $amount * 0.10;
            }
        };

        $calculator = new PriceCalculator($customStrategy);
        $this->assertEqualsWithDelta(100.0, $calculator->calculate(1000.0), 0.0001);
    }
}
