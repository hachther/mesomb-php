<?php

namespace MeSomb\Model;

/**
 * Class Contribution
 *
 * @extends ATransaction
 *
 * @property Customer contributor - The contributor
 */
class Contribution extends ATransaction
{
    /**
     * @var Customer | null
     */
    public $contributor = null;

    public function __construct($data)
    {
        parent::__construct($data);
        if (isset($data['contributor'])) {
            $this->contributor = new Customer($data['contributor']);
        }
    }
}