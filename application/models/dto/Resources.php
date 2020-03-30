<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resources
{
    /**
     * Entity list content
     *
     * @var array
     */
    private $list;

    /**
     * Page information
     *
     * @var Page | null
     */
    private $page;

    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * @param array $list
     * @return Resources
     */
    public function setList(array $list): Resources
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @return Page|null
     */
    public function getPage(): ?Page
    {
        return $this->page;
    }

    /**
     * @param Page|null $page
     * @return Resources
     */
    public function setPage(?Page $page): Resources
    {
        $this->page = $page;
        return $this;
    }

}
