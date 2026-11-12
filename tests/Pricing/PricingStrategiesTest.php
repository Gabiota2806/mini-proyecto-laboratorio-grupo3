<?php

declare(strict_types=1);

namespace Tests\Pricing;

use InsuranceStrategy;
use PHPUnit\Framework\TestCase;
use PrepaidStrategy;
use PrivatePatientStrategy;
use RetiredPatientStrategy;

final class PricingStrategiesTest extends TestCase
{
    public function testPrivatePatientHasNoDiscount(): void
    {
        $strategy = new PrivatePatientStrategy();
        $this->assertSame(1000.0, $strategy->calculate(1000.0));
    }

    public function testInsuranceStrategyAppliesDefaultThirtyPercentDiscount(): void
    {
        $strategy = new InsuranceStrategy();
        // 30% de descuento => 70% del valor base
        $this->assertEqualsWithDelta(700.0, $strategy->calculate(1000.0), 0.0001);
    }

    public function testInsuranceStrategyAppliesCustomDiscount(): void
    {
        $strategy = new InsuranceStrategy(0.40);
        // 40% de descuento => 60% del valor base
        $this->assertEqualsWithDelta(600.0, $strategy->calculate(1000.0), 0.0001);
    }

    public function testRetiredPatientAppliesDefaultFiftyPercentDiscount(): void
    {
        $strategy = new RetiredPatientStrategy();
        // 50% de descuento => 50% del valor base
        $this->assertEqualsWithDelta(500.0, $strategy->calculate(1000.0), 0.0001);
    }

    public function testRetiredPatientAppliesCustomDiscount(): void
    {
        $strategy = new RetiredPatientStrategy(0.60);
        // 60% de descuento => 40% del valor base
        $this->assertEqualsWithDelta(400.0, $strategy->calculate(1000.0), 0.0001);
    }

    public function testPrepaidStrategyAppliesDefaultTwentyPercentDiscount(): void
    {
        $strategy = new PrepaidStrategy();
        // 20% de descuento => 80% del valor base
        $this->assertEqualsWithDelta(800.0, $strategy->calculate(1000.0), 0.0001);
    }

    public function testPrepaidStrategyAppliesCustomDiscount(): void
    {
        $strategy = new PrepaidStrategy(0.25);
        // 25% de descuento => 75% del valor base
        $this->assertEqualsWithDelta(750.0, $strategy->calculate(1000.0), 0.0001);
    }

    public function testZeroAmountReturnsZeroForAnyStrategy(): void
    {
        $strategies = [
            new PrivatePatientStrategy(),
            new InsuranceStrategy(),
            new RetiredPatientStrategy(),
            new PrepaidStrategy(),
        ];

        foreach ($strategies as $strategy) {
            $this->assertSame(0.0, $strategy->calculate(0.0));
        }
    }
}
