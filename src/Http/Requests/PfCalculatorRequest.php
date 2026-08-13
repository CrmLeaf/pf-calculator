<?php

declare(strict_types=1);

namespace Crmleaf\Payroll\Tools\PfCalculator\Http\Requests;

use Crmleaf\Payroll\Money;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the wire input for PF Calculator and turns it into named arguments
 * for Crmleaf\Payroll\Calculators\PfCalculator::calculate().
 *
 * Optional fields that were not sent are left out of the payload entirely
 * rather than passed as null, so the calculator's own documented defaults apply
 * and there is exactly one place each default is written down.
 */
final class PfCalculatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        if (!$this->submitted()) {
            return [];
        }

        return [
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'employer_restricts_to_ceiling' => ['nullable', 'boolean'],
            'eps_eligible' => ['nullable', 'boolean'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'reduced_rate' => ['nullable', 'boolean'],
            'include_admin_charges' => ['nullable', 'boolean'],
            'as_of' => ['nullable', 'date'],
        ];
    }

    /**
     * Named arguments for PfCalculator::calculate().
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        /** @var array<string, mixed> $input */
        $input = $this->validated();

        $payload = [
            'basicSalary' => Money::fromRupees((float) $input['basic_salary']),
        ];

        if (array_key_exists('employer_restricts_to_ceiling', $input) && $input['employer_restricts_to_ceiling'] !== null) {
            $payload['employerRestrictsToCeiling'] = (bool) $input['employer_restricts_to_ceiling'];
        }

        if (array_key_exists('eps_eligible', $input) && $input['eps_eligible'] !== null) {
            $payload['epsEligible'] = (bool) $input['eps_eligible'];
        }

        if (array_key_exists('age', $input) && $input['age'] !== null) {
            $payload['age'] = (int) $input['age'];
        }

        if (array_key_exists('reduced_rate', $input) && $input['reduced_rate'] !== null) {
            $payload['reducedRate'] = (bool) $input['reduced_rate'];
        }

        if (array_key_exists('include_admin_charges', $input) && $input['include_admin_charges'] !== null) {
            $payload['includeAdminCharges'] = (bool) $input['include_admin_charges'];
        }

        if (array_key_exists('as_of', $input) && $input['as_of'] !== null) {
            $payload['asOf'] = new \DateTimeImmutable((string) $input['as_of']);
        }

        return $payload;
    }

    /**
     * A bare GET renders an empty form; everything else is a submission.
     */
    public function submitted(): bool
    {
        return $this->isMethod('post') || $this->expectsJson() || $this->query->count() > 0;
    }
}
