<?php

declare(strict_types=1);

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class ExpenseDTO
{
    /**     
    * @Serializer\Expose()     
    * @Serializer\Type("integer")     
    */
    private ?int $id;

    /**     
    * @Serializer\Expose()     
    * @Serializer\Type("string")     
    */
    private ?string $description;

    /**     
    * @Serializer\Expose()     
    * @Serializer\Type("string")     
    */
    private ?string $price;

    /**     
    * @Serializer\Expose()     
    * @Serializer\Type("string")     
    */
    private ?string $currency;

    /**
    * @Serializer\Expose()     
    * @Serializer\Type("integer")     
    */
    private ?int $type;

    public function __construct(
        int $id,
        string $description,
        string $price,
        string $currency,
        int $type
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->price = $price;
        $this->currency = $currency;
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getType(): ?int
    {
        return $this->type;
    }
}
