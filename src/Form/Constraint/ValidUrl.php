<?php

namespace App\Form\Constraint;

use App\Form\Constraint\Validator\ValidUrlValidator;
use Symfony\Component\Validator\Constraint;

class ValidUrl extends Constraint
{
    public $message = 'This is not a valid URL.';

    public function validatedBy()
    {
        return ValidUrlValidator::class;
    }
}
