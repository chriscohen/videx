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
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
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
        return $matches[1] ? (float) $matches[1] : null;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

}
