<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;

class ResetPasswordAction
{
    private $validator;

    private $userPasswordEncoder;

    private $entityManager;

    private $tokenManager;

    public function __construct(
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $tokenManager
    ) 
    {
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }

    // klasa jako metoda
    /*
        np.:
        $reset = new ResetPasswordAction();
        $reset();
    */
    public function __invoke(User $data)
    {
        /*
         var_dump(
            $data->getNewPassword(),
            $data->getOldPassword()
        );
        die; 
        try {
            $this->validator->validate($data);
        } catch(ValidationException $e) {
            echo $e->getMessage();
        }
        echo 'ok';
        die;
        */
        $this->validator->validate($data);
 
        //return $data; //tylko tak dzia³a waalidacja!
        /*
        $data->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $data, $data->getNewPassword()
            )
        );

        //Old token i still valid
        $data->setPasswordChangeDate(time());

        $this->entityManager->flush();

        $token = $this->tokenManager->create($data);
        */
        
        //return new JsonResponse(['token' => $token]); 
        return $data; 
        // validator is only called after we return data

        //entity is persisted automatically, only when validation pass
    }
}