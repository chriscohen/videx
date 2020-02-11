<?php

declare(strict_types=1);

namespace ChrisCohen\Model;

class Package
{
    /**
     * To explain: we are looking for a pound symbol, followed by a group. This group must consist of one or more
     * numbers - [0-9]+ - then, optionally, a set of further characters (inner group).
     *
     * If this inner group exists, it must consist of ONE period (full stop) character, followed by exactly two
     * numbers.
     *
     * This covers prices such as £10, but also prices such as £10.99.
     */
    public const PRICE_PATTERN = '/£([0-9]+(\.[0-9]{2})?)/';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var float
     */
    protected $price = 0.0;

    /**
     * @var float
     */
    protected $discount = 0.0;

    /**
     * The number of months for this package. An annual package would have a value of 12, for example.
     *
     * @var int
     */
    protected $months = 1;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $this->replaceBrs($description);

        // Work out if the package is monthly or yearly.
        $description = strtolower($description);

        if (strpos($description, 'per year') !== false) {
            $this->setMonths(12);
        }
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Work out a price for a 12-month period.
     *
     * @return float
     */
    public function getAnnualPrice(): float
    {
        $ratio = $this->getMonths() / 12;
        return $this->getPrice() / $ratio;
    }

    public function formatPrice(): string
    {
        return $this->formatAsCurrency($this->getPrice());
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getPriceFromString(string $price): ?float
    {
        $matches = [];
        preg_match(self::PRICE_PATTERN, $price, $matches);

        // We need to cast to float to avoid TypeError. We avoid the ?? operator since otherwise we can't cast.
        return isset($matches[1]) ? (float) $matches[1] : null;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function formatDiscount(): string
    {
        return $this->formatAsCurrency($this->getDiscount());
    }

    /**
     * @param float $discount
     */
    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function getMonths(): int
    {
        return $this->months;
    }

    public function setMonths(int $months): void
    {
        if ($months < 0) {
            $months = 1;
        }

        $this->months = $months;
    }

    /**
     * Convert to an array suitable for use with json_encode().
     *
     * @return array
     */
    public function toJson(): array
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'price' => $this->formatPrice(),
            'discount' => $this->formatDiscount(),
        ];
    }

    /**
     * Replace HTML newlines with regular space characters.
     *
     * @param string $input
     * @return string
     */
    protected function replaceBrs(string $input): string
    {
        return str_replace(['<br>', '<br/>'], ' ', $input);
    }

    /**
     * Format a float as a currency with 2 decimal places.
     *
     * @param float $input
     * @param string $symbol
     * @return string
     */
    protected function formatAsCurrency(float $input, string $symbol = '£'): string
    {
        if ($input >= 0) {
            return $symbol . number_format($input, 2);
        } else {
            return '-' . $symbol . abs(number_format($input, 2));
        }
    }

}
