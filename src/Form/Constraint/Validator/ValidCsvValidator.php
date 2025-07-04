<?php

namespace App\Form\Constraint\Validator;

use App\Form\Constraint\ValidCsv;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidCsvValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        $allowedMimeTypes = [
            'text/csv',
            'application/csv',
            'text/plain',
        ];
        if ($value instanceof UploadedFile) {
            $handle = fopen($value->getPathname(), 'r');
            if (
                $handle === false ||
                fgetcsv($handle) === false ||
                $value->getClientOriginalExtension() !== 'csv' ||
                !in_array($value->getMimeType(), $allowedMimeTypes)
            ) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
            fclose($handle);
        }
    }
}
