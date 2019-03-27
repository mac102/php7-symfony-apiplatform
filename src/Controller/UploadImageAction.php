<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Image;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Form\ImageType;


class UploadImageAction 
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManagerInterface,
        ValidatorInterface $validator
    )
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManagerInterface;
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        // Create a new Image instance
        $image = new Image();
        
        // validate the form
        $form = $this->formFactory->create(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // persist new image
            $this->entityManager->persist($image);
            $this->entityManager->flush();
            
            $image->setFile(null);

            return $image;
        }
        
        // uploading done

        throw new ValidationException(
            $this->validator->validate($image)
        );
    }
}
