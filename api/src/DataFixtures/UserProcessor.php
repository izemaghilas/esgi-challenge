<?php

namespace App\DataFixtures\Processor;

use App\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(readonly UserPasswordHasherInterface $passwordHasher)
    {

    }
	/**
	 * Processes an object before it is persisted to DB.
	 *
	 * @param string $id Fixture ID
	 * @param object $object
	 */
	public function preProcess(string $id, object $object): void {
        if (false === $object instanceof User)
        {
            return;
        }
        
        $object->setPassword($this->passwordHasher->hashPassword($object, $object->getPlainPassword()));
	}
	
	/**
	 * Processes an object after it is persisted to DB.
	 *
	 * @param string $id Fixture ID
	 * @param object $object
	 */
	public function postProcess(string $id, object $object): void {
	}
}