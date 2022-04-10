<?php

declare(strict_types=1);

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class PaginatedExpenseDTO
{
    /**     
    * @Serializer\Expose()     
    * @Serializer\Type("integer")     
    */
    private int $page;

    /**     
    * @Serializer\Expose()     
    * @Serializer\Type("integer")     
    */
    private int $countPerPage;

    /**     
    * @Serializer\Expose()     
    * @Serializer\Type("integer")     
    */
    private int $totalCount;

    /**
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private int $totalPages;

    /**
    * @var ExpenseDTO[] $expenses
    * 
    * @Serializer\Expose()     
    * @Serializer\Type("array<App\DTO\ExpenseDTO>")     
    */
    private array $expenses;

    public function __construct(int $page, int $countPerPage, int $totalCount, int $totalPages, array $expenses)
    {
        $this->page = $page;
        $this->countPerPage = $countPerPage;
        $this->totalCount = $totalCount;
        $this->totalPages = $totalPages;
        $this->expenses = $expenses;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getCountPerPage()
    {
        return $this->countPerPage;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    public function getTotalPages()
    {
        return $this->totalPages;
    }

    public function getExpenses()
    {
        return $this->expenses;
    }
}
