<?php
/**
 * ============================================================================
 *  CALCULO DE PRECIOS
 *  Patron aplicado: STRATEGY (comportamiento)
 * ============================================================================
 *
 *  Refactorizado segun metodologia Refactoring.Guru:
 *  - Interfaz PricingStrategy para desacoplar el algoritmo de calculo.
 *  - Clases concretas por cada politica comercial (Private, Insurance, Retired, Prepaid).
 *  - PriceCalculator actua como Contexto que utiliza composicion y polimorfismo.
 *  - Abierto a extension y cerrado a modificacion (OCP) y principio de responsabilidad unica (SRP).
 * ============================================================================
 */

declare(strict_types=1);

/**
 * Contrato que define el algoritmo de calculo de precio.
 */
interface PricingStrategy
{
    public function calculate(float $amount): float;
}

/**
 * Estrategia para paciente particular (sin descuento).
 */
final class PrivatePatientStrategy implements PricingStrategy
{
    public function calculate(float $amount): float
    {
        return $amount;
    }
}

/**
 * Estrategia para paciente con obra social (30% de descuento por defecto, factor 0.7).
 */
final class InsuranceStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.30) {}

    public function calculate(float $amount): float
    {
        return $amount * (1.0 - $this->discount);
    }
}

/**
 * Estrategia para paciente jubilado (50% de descuento por defecto, factor 0.5).
 */
final class RetiredPatientStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.50) {}

    public function calculate(float $amount): float
    {
        return $amount * (1.0 - $this->discount);
    }
}

/**
 * Estrategia para paciente con prepaga (ej. 20% de descuento, factor 0.8).
 * Resuelve el Ejercicio 1 del TP: incorporar nuevo tipo sin modificar PriceCalculator.
 */
final class PrepaidStrategy implements PricingStrategy
{
    public function __construct(private float $discount = 0.20) {}

    public function calculate(float $amount): float
    {
        return $amount * (1.0 - $this->discount);
    }
}

/**
 * Contexto de calculo de precios.
 * COMPOSICION: PriceCalculator TIENE una estrategia (no hereda de ella).
 */
final class PriceCalculator
{
    public function __construct(private ?PricingStrategy $strategy = null)
    {
        if ($this->strategy === null) {
            $this->strategy = new PrivatePatientStrategy();
        }
    }

    public function setStrategy(PricingStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function getStrategy(): PricingStrategy
    {
        return $this->strategy;
    }

    /**
     * Delega polimorficamente el calculo a la estrategia activa.
     */
    public function calculate(float $amount): float
    {
        return $this->strategy->calculate($amount);
    }

    /**
     * Calculo con IVA sin duplicar logica algoritmica.
     */
    public function calculateWithVat(float $amount, float $vatRate = 0.21): float
    {
        return $this->calculate($amount) * (1.0 + $vatRate);
    }

    /**
     * Metodo puente para mantener compatibilidad hacia atras con codigo cliente no refactorizado.
     */
    public function calcular(float $monto, string $tipoPaciente = 'particular'): float
    {
        $strategy = match ($tipoPaciente) {
            'obra_social' => new InsuranceStrategy(),
            'jubilado'    => new RetiredPatientStrategy(),
            'prepaga'     => new PrepaidStrategy(),
            default       => new PrivatePatientStrategy(),
        };

        return $strategy->calculate($monto);
    }

    /**
     * Metodo puente para calculo con IVA con firma original.
     */
    public function calcularConIva(float $monto, string $tipoPaciente = 'particular'): float
    {
        return $this->calcular($monto, $tipoPaciente) * 1.21;
    }
}
