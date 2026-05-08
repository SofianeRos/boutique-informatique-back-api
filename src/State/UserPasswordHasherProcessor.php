<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasherProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof User && $data->getPassword()) {
            $currentPassword = $data->getPassword();
            // Check if it's already a hash (Argon2 / Bcrypt usually start with $)
            if (!str_starts_with($currentPassword, '$')) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $data,
                    $currentPassword
                );
                $data->setPassword($hashedPassword);
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
