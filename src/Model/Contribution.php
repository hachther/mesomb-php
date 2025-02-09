<?php

namespace MeSomb\Model;

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