# PF Calculator

EPF employee, employer and EPS shares with the wage ceiling applied.

Splits the 12% employer share into EPS and EPF the way EPFO does it, applies the wage ceiling only where the statute applies it, and adds EDLI and administration charges.

One of the [CRMLeaf payroll tools](https://github.com/crmleaf). The arithmetic
and the dated statutory rate tables live in
[`crmleaf/payroll-core`](https://github.com/crmleaf/payroll-core); this package is
the thin skin that makes one calculator installable, mountable and embeddable on
its own.

> [!TIP]
> **Nothing to install to try it.** [PF Calculator on INDPayroll](https://www.indpayroll.com/free-tools/pf-calculator?utm_source=github&utm_medium=referral&utm_campaign=payroll-tools)
> is this calculator, hosted and free, and
> [all fifteen tools](https://www.indpayroll.com/free-tools?utm_source=github&utm_medium=referral&utm_campaign=payroll-tools) are there. Install the package when you want
> it inside your own application.

> [!NOTE]
> A wrong figure or an out-of-date rate is almost always a
> [`payroll-core`](https://github.com/crmleaf/payroll-core/issues) matter, since
> that is where the tables live. Anything about this tool's routes, views or
> browser asset belongs here.

## Install

**Composer** - Laravel auto-discovers the service provider, so this is the whole
setup:

```bash
composer require crmleaf/pf-calculator
```

> [!NOTE]
> Not on Packagist yet. Until it is, point Composer at the two repositories in
> **your own project's** `composer.json` and the same `require` works, because
> Composer reads the tags:
>
> ```json
> "repositories": [
>     { "type": "vcs", "url": "https://github.com/crmleaf/pf-calculator.git" },
>     { "type": "vcs", "url": "https://github.com/crmleaf/payroll-core.git" }
> ]
> ```
>
> Both entries are needed, and they have to be in the root project: Composer
> ignores a `repositories` block inside an installed dependency, so listing only
> this package will not resolve `crmleaf/payroll-core`.

**npm** - the same calculation, re-exported from `@crmleaf/payroll-js` so you can
install this one tool and nothing else:

```bash
npm install @crmleaf/pf-calculator
```

> [!NOTE]
> Not on npm yet either. The script-tag route below needs no registry and works
> today. Installing this package straight from git will not resolve
> `@crmleaf/payroll-js`, for the same reason as above.

**A plain script tag** - no build step, no bundler, no server. Build the browser
bundle once and serve the file yourself:

```html
<script src="/js/payroll.min.js"></script>
<script>
const result = CrmleafPayroll.pf({ basicSalary: 30000, employerRestrictsToCeiling: true });
console.log(result.explain);
</script>
```

`payroll.min.js` is the single-file browser build. Get it by running
`npm run build` in [`@crmleaf/payroll-js`][js] and copying `dist/payroll.min.js`
into whatever your site serves as static assets.

> A hosted CDN build is coming soon, which will reduce this to a single URL.
> Serving the file yourself works today and keeps working afterwards - it is the
> only option that needs no third-party request, so plenty of projects will want
> to stay on it.

### See it working first

`demo/index.html` in this repository is a working copy of PF Calculator in one file:
the form, the calculation and the working, with no build step and no server. Drop
`payroll.min.js` beside it and open it from disk.

```bash
cp /path/to/payroll-js/dist/payroll.min.js demo/
open demo/index.html
```

Nothing on that page reaches the network, which is the point: it is a calculator
people paste salary figures into.

## Use it

**Plain PHP**, no framework and no container:

```php
use Crmleaf\Payroll\Calculators\PfCalculator;
use Crmleaf\Payroll\Money;

$result = (new PfCalculator())->calculate(
    basicSalary: Money::fromRupees(30_000),
    employerRestrictsToCeiling: true,
);

echo $result->explain();      // the formula with the real operands in it
echo $result->workings();     // every step, one per line, with its citation
print_r($result->toArray());  // snake_case, ready for JSON
```

**Laravel** - resolve it from the container, or type-hint it anywhere:

```php
use Crmleaf\Payroll\Calculators\PfCalculator;

public function show(PfCalculator $calculator)
{
    return $calculator->calculate(
        basicSalary: Money::fromRupees(30_000),
        employerRestrictsToCeiling: true,
    )->toArray();
}
```

**Blade** - one component, no controller:

```blade
<x-crmleaf::pf-calculator />
```

**HTTP** - off by default. Publish the config and turn the route on:

```bash
php artisan vendor:publish --tag=pf-calculator-config
```

```php
// config/pf-calculator.php
'route' => ['enabled' => true, 'prefix' => 'tools'],
```

```bash
curl -X POST https://example.test/tools/pf-calculator \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"basic_salary":30000,"employer_restricts_to_ceiling":true}'
```

The JSON response carries the figures, the working and the statutory citations:

```json
{
  "tool": "pf-calculator",
  "data": { "…": "every figure, snake_case, with a *_formatted twin" },
  "explain": "the formula with the real operands substituted",
  "working": [{ "label": "…", "amount": 0, "formula": "…", "citation": "…" }],
  "citations": ["…"]
}
```

**JavaScript**:

```js
import { pf } from '@crmleaf/pf-calculator';

const result = pf({ basicSalary: 30000, employerRestrictsToCeiling: true });
```

## No server needed

The maths here is arithmetic over versioned rate tables, so it runs anywhere.
The published asset binds the markup and computes in the browser:

```bash
php artisan vendor:publish --tag=pf-calculator-assets
```

```html
<section data-crmleaf-tool="pf-calculator">
  <form data-crmleaf-form> … </form>
  <div data-crmleaf-output hidden></div>
</section>

<script src="/js/payroll.min.js"></script>
<script src="/vendor/pf-calculator/pf-calculator.js"></script>
```

If the browser build is absent the script does nothing and the form posts to the
server instead, so the page works either way.

## Inputs

| Field | Type | Required | Default | Notes |
|-------|------|----------|---------|-------|
| `basic_salary` | money (₹) | Yes | `30000` |  |
| `employer_restricts_to_ceiling` | boolean | No | `true` |  |
| `eps_eligible` | boolean | No | `true` | A member who joined after 1 September 2014 above the ceiling is not. |
| `age` | integer | No | - | Past 58, the EPS share stops and the whole employer contribution goes to EPF. |
| `reduced_rate` | boolean | No | `false` |  |
| `include_admin_charges` | boolean | No | `true` |  |
| `as_of` | date (YYYY-MM-DD) | No | - |  |

Optional fields you leave out are omitted from the call entirely, so the
calculator's own documented defaults apply.

Every figure here rests on a statutory rate, so the call takes `as_of`. Set it
and the calculation runs on the rates in force on that date, which is what makes
a prior year recomputable rather than merely rememberable.

## Statutory basis

Employees' Provident Funds and Miscellaneous Provisions Act 1952, with the EPS share capped at the ₹15,000 wage ceiling under paragraph 11(3) of the Employees' Pension Scheme 1995 even where EPF is contributed on the full basic.

Rates are data, not code: they live in dated tables with a cited source in
`crmleaf/payroll-core`, so a rate change is a new dated entry rather than an edit
to a constant.

> [!IMPORTANT]
> This package implements our reading of the applicable statutes and is provided
> without warranty. It is a calculation library, not tax advice. Verify against
> your own compliance obligations before relying on the output for statutory
> filing.

## Publishing

| Tag | Publishes |
|-----|-----------|
| `pf-calculator-config` | `config/pf-calculator.php` |
| `pf-calculator-views` | `resources/views/vendor/pf-calculator` |
| `pf-calculator-assets` | `public/vendor/pf-calculator` |

## Licence

[MIT](LICENSE) © CRMLeaf. Use it commercially, embed it, fork it.

[js]: https://github.com/crmleaf/payroll-js
