# PHP Mortage Calculator

I went by train one day and I wanted to make some cool library, so I did this. Now you can **calculate mortage easily in PHP.**

## Install

`composer require dfridrich/mortage`

## Usage

```php
$mortage = new Defr\MortageRequest(1000000, 1.89, 20);
/** @var \Defr\MortageResult $result */
$result = $mortage->calculate();
```

You can use `MortageResult`\`s getters for access values:

```php
object(Defr\MortageResult)[2]
  private 'mortageRequest' =>
    object(Defr\MortageRequest)[3]
      private 'salePrice' => float 1000000
      private 'mortgageInterestPercent' => float 1.89
      private 'yearTerm' => int 20
      private 'monthTerm' => int 240
  private 'annualInterestRate' => float 0.0189
  private 'monthlyInterestRate' => float 0.001575
  private 'monthlyPayment' => float 5006.9030574862
  private 'totalPayed' => float 1201656.7337967
```

## Credits

Orinally based on library from [Dave Tutfs](http://www.davetufts.com/).