@props([
    'action' => null,
    'method' => 'post',
    'defaults' => [],
    'input' => [],
    'result' => null,
    'error' => null,
    'heading' => 'PF Calculator',
    'tagline' => 'EPF employee, employer and EPS shares with the wage ceiling applied.',
    'showWorking' => true,
])

<section class="crmleaf-tool crmleaf-tool--pf-calculator" data-crmleaf-tool="pf-calculator">
    <header class="crmleaf-tool__header">
        <h2 class="crmleaf-tool__heading">{{ $heading }}</h2>
        <p class="crmleaf-tool__tagline">{{ $tagline }}</p>
    </header>

    @if ($error)
        <p class="crmleaf-tool__error" role="alert">{{ $error }}</p>
    @endif

    <form class="crmleaf-tool__form"
          method="{{ strtolower($method) === 'get' ? 'get' : 'post' }}"
          action="{{ $action }}"
          data-crmleaf-form>
        @if (strtolower($method) !== 'get')
            @csrf
        @endif

        <label class="crmleaf-field">
            <span>Monthly basic (basic + DA)</span>
            <input type="number" step="0.01" min="0" inputmode="decimal" name="basic_salary" value="{{ old('basic_salary', $input['basic_salary'] ?? ($defaults['basic_salary'] ?? '')) }}" required>
        </label>

        <label class="crmleaf-field crmleaf-field--bool">
            <input type="hidden" name="employer_restricts_to_ceiling" value="0">
            <input type="checkbox" name="employer_restricts_to_ceiling" value="1" @checked(old('employer_restricts_to_ceiling', $input['employer_restricts_to_ceiling'] ?? ($defaults['employer_restricts_to_ceiling'] ?? false)))>
            <span>Employer restricts its share to ₹15,000</span>
        </label>

        <label class="crmleaf-field crmleaf-field--bool">
            <input type="hidden" name="eps_eligible" value="0">
            <input type="checkbox" name="eps_eligible" value="1" @checked(old('eps_eligible', $input['eps_eligible'] ?? ($defaults['eps_eligible'] ?? false)))>
            <span>Member is eligible for EPS</span>
            <small>A member who joined after 1 September 2014 above the ceiling is not.</small>
        </label>

        <label class="crmleaf-field">
            <span>Age</span>
            <input type="number" step="1" inputmode="numeric" name="age" value="{{ old('age', $input['age'] ?? ($defaults['age'] ?? '')) }}">
            <small>Past 58, the EPS share stops and the whole employer contribution goes to EPF.</small>
        </label>

        <label class="crmleaf-field crmleaf-field--bool">
            <input type="hidden" name="reduced_rate" value="0">
            <input type="checkbox" name="reduced_rate" value="1" @checked(old('reduced_rate', $input['reduced_rate'] ?? ($defaults['reduced_rate'] ?? false)))>
            <span>Reduced 10% rate applies</span>
        </label>

        <label class="crmleaf-field crmleaf-field--bool">
            <input type="hidden" name="include_admin_charges" value="0">
            <input type="checkbox" name="include_admin_charges" value="1" @checked(old('include_admin_charges', $input['include_admin_charges'] ?? ($defaults['include_admin_charges'] ?? false)))>
            <span>Include EDLI and administration charges</span>
        </label>

        <label class="crmleaf-field">
            <span>Rates as on</span>
            <input type="date" name="as_of" value="{{ old('as_of', $input['as_of'] ?? ($defaults['as_of'] ?? '')) }}">
        </label>

        <input type="hidden" name="tool" value="pf-calculator">

        <div class="crmleaf-tool__actions">
            <button type="submit" class="crmleaf-tool__submit">Calculate</button>
        </div>
    </form>

    {{-- The client-side path writes its answer here; the server-side path fills it below. --}}
    <div class="crmleaf-tool__output" data-crmleaf-output hidden></div>

    @if ($result)
        <div class="crmleaf-tool__result">
            <p class="crmleaf-tool__explain"><code>{{ $result->explain() }}</code></p>

            <table class="crmleaf-tool__figures">
                <tbody>
                @foreach ($result->toArray() as $key => $value)
                    @continue(is_array($value) || str_ends_with((string) $key, '_formatted'))
                    <tr>
                        <th scope="row">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}</th>
                        <td>{{ $result->toArray()[$key.'_formatted'] ?? (is_bool($value) ? ($value ? 'Yes' : 'No') : $value) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if ($showWorking && count($result->steps()))
                <details class="crmleaf-tool__working" open>
                    <summary>How this was worked out</summary>
                    <ol>
                        @foreach ($result->steps() as $step)
                            <li>
                                <span class="crmleaf-step__label">{{ $step->label }}</span>
                                @if ($step->amount)
                                    <span class="crmleaf-step__amount">{{ $step->amount->format() }}</span>
                                @endif
                                @if ($step->formula)
                                    <code class="crmleaf-step__formula">{{ $step->formula }}</code>
                                @endif
                                @if ($step->citation)
                                    <small class="crmleaf-step__citation">{{ $step->citation }}</small>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </details>
            @endif

            @if (count($result->citations()))
                <ul class="crmleaf-tool__citations">
                    @foreach ($result->citations() as $citation)
                        <li>{{ $citation }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</section>
