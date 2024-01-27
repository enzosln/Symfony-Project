<?php
// src/Dto/PaginationDto.php

namespace App\DTO;

class PaginationDTO
{
    private $page;
    private $limit;

    public function __construct(int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
