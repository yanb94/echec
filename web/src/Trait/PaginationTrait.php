<?php

namespace App\Trait;

trait PaginationTrait
{
    private function generatePaginationValues($nb, $pagination, $page): array
    {
        $maxPage = ceil($nb/$pagination);

        $isPreviousPage = $page > 1;
        $previousPageNb = $page - 1;
        $isNextPage = $page < $maxPage;
        $nextPageNb = $page + 1;

        return [
            $isPreviousPage,
            $previousPageNb,
            $isNextPage,
            $nextPageNb
        ];
    }
}
