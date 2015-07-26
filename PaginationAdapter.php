<?php

namespace AppBundle\Service;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PaginationAdapter WIP
 *
 * Wraps the paginator and transforms returned pagination slice
 * to custom format.
 *
 * @author strackovski
 * @package AppBundle\Service
 */
class PaginationAdapter
{
    /** @var PaginatorInterface $paginator */
    protected $paginator;

    /**
     * @param PaginatorInterface $paginator The paginator component
     */
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Paginate
     *
     * @param Query $query
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function paginateQuery(Query $query, $page = 1, $limit = 20)
    {
        if (!$page or $page < 1) {
            $page = 1;
        }

        if (!$limit or $limit < 1) {
            $limit = 20;
        }

        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $slice */
        $slice = $this->paginator->paginate($query, $page, $limit);

        return $this->adapt($slice);
    }

    /**
     * Adapt pagination output
     *
     * @param AbstractPagination $pagination
     * @return array
     */
    public function adapt(AbstractPagination $pagination)
    {
        $current = $pagination->getCurrentPageNumber();
        $limit = $pagination->getItemNumberPerPage();
        
        $current == 1 ? $prev = false : $prev = $current - 1;
        $current >= $pagination->getPageCount() ? $next = false : $next = $current + 1;

        return [
            'items' => $pagination->getItems(),
            'page' => $pagination->getCurrentPageNumber(),
            'pages' => $pagination->getPageCount(),
            'limit' => $pagination->getItemNumberPerPage(),
            'total_items' => $pagination->getTotalItemCount(),
            '_links' => [
                'self' => [
                    'href' => $current ? "?page={$current}&per_page={$limit}" : '',
                ],
                'next' => [
                    'href' => $next ? "?page={$next}&per_page={$limit}" : '',
                ],
                'prev' => [
                    'href' => $prev ? "?page={$prev}&per_page={$limit}" : ''
                ]
            ]
        ];
    }
}