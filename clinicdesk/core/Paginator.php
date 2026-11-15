<?php

class Paginator
{
    private int $totalItems;
    private int $perPage;
    private int $currentPage;

    public function __construct(int $totalItems, int $perPage, int $currentPage)
    {
        $this->totalItems  = $totalItems;
        $this->perPage     = $perPage > 0 ? $perPage : 1;
        $this->currentPage = $currentPage > 0 ? $currentPage : 1;
    }

    /**
     * SQL OFFSET value for the current page
     */
    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
     * Total number of pages
     */
    public function totalPages(): int
    {
        return (int) ceil($this->totalItems / $this->perPage);
    }

    /**
     * Is there a previous page?
     */
    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Is there a next page?
     */
    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages();
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }
}