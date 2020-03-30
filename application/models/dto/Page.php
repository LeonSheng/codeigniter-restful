<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page
{
    /**
     * Total elements count
     *
     * @var int
     */
    private $totalElements;

    /**
     * Total pages count
     *
     * @var int
     */
    private $totalPages;

    /**
     * Page size
     *
     * @var int
     */
    private $pageSize;

    /**
     * Current page index
     *
     * @var int
     */
    private $pageIndex;

    /**
     * @return int
     */
    public function getTotalElements(): int
    {
        return $this->totalElements;
    }

    /**
     * @param int $totalElements
     * @return Page
     */
    public function setTotalElements(int $totalElements): Page
    {
        $this->totalElements = $totalElements;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     * @return Page
     */
    public function setTotalPages(int $totalPages): Page
    {
        $this->totalPages = $totalPages;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     * @return Page
     */
    public function setPageSize(int $pageSize): Page
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageIndex(): int
    {
        return $this->pageIndex;
    }

    /**
     * @param int $pageIndex
     * @return Page
     */
    public function setPageIndex(int $pageIndex): Page
    {
        $this->pageIndex = $pageIndex;
        return $this;
    }

}
