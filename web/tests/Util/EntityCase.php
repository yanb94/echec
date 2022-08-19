<?php

namespace App\Tests\Util;

use Symfony\Component\Validator\Validation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;

class EntityCase extends KernelTestCase
{
    public function assertHasErrors(object $entity, int $number = 0)
    {
        self::bootKernel();

        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping(true)
            ->addDefaultDoctrineAnnotationReader()
            ->setConstraintValidatorFactory(new ContainerConstraintValidatorFactory(self::$container))
            ->getValidator();

        $errors = $validator->validate($entity);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}
