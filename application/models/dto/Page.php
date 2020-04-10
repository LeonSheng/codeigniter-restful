<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page
{
    /**
     * Total elements count
     *
     * @var int|null
     */
    private $totalElements;

    /**
     * Total pages count
     *
     * @var int|null
     */
    private $totalPages;

    /**
     * Page size
     *
     * @var int|null
     */
    private $pageSize;

    /**
     * Current page index
     *
     * @var int|null
     */
    private $pageIndex;

    /**
     * @return int|null
     */
    public function getTotalElements(): ?int
    {
        return $this->totalElements;
    }

    /**
     * @param int|null $totalElements
     * @return Page
     */
    public function setTotalElements(?int $totalElements): Page
    {
        $this->totalElements = $totalElements;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalPages(): ?int
    {
        return $this->totalPages;
    }

    /**
     * @param int|null $totalPages
     * @return Page
     */
    public function setTotalPages(?int $totalPages): Page
    {
        $this->totalPages = $totalPages;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    /**
     * @param int|null $pageSize
     * @return Page
     */
    public function setPageSize(?int $pageSize): Page
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPageIndex(): ?int
    {
        return $this->pageIndex;
    }

    /**
     * @param int|null $pageIndex
     * @return Page
     */
    public function setPageIndex(?int $pageIndex): Page
    {
        $this->pageIndex = $pageIndex;
        return $this;
    }

}
