<?php

namespace MeSomb\Model;

/**
 * Class APaginated
 *
 * This class is a base class for all paginated responses.
 *
 * @package MeSomb\Model
 *
 * @property int $count Total number of items
 * @property int $next URL of the next page
 * @property int $previous URL of the previous page
 * @property array $results Results of the current page
 */
abstract class APaginated
{
    /**
     * @var int total number of items
     */
    public $count;

    /**
     * @var int url of the next page
     */
    public $next;

    /**
     * @var int url of the previous page
     */
    public $previous;

    /**
     * @var array results
     */
    public $results;

    public function __construct($data)
    {
        $this->count = $data['count'];
        $this->next = $data['next'];
        $this->previous = $data['previous'];
        $this->results = $data['results'];
    }
}