<?php

namespace MeSomb\Model;

class ContactInfo
{
    public string $firstName;
    public string $lastName;

    public static function fromArray(array $data): self
    {
        $contact = new self();
        $contact->firstName = $data['first_name'];
        $contact->lastName = $data['last_name'];
        return $contact;
    }
}