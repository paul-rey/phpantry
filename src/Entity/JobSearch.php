<?php


namespace App\Entity;


class JobSearch
{

    /**
     * @var string|null
     */

    private $title;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return JobSearch
     */
    public function setTitle(string $title): JobSearch
    {
        $this->title = $title;
        return $this;
    }


}