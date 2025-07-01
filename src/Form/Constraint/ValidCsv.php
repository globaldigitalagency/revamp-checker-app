<?php

namespace App\Form\Constraint;

use App\Form\Constraint\Validator\ValidCsvValidator;
use Symfony\Component\Validator\Constraint;

class ValidCsv extends Constraint
{
    public string $message = 'The file is not a valid CSV.';

    public function validatedBy()
    {
        return ValidCsvValidator::class;
    }
}
