<?php

namespace App\Form;

use App\Form\Constraint\ValidCsv;
use App\Form\Constraint\ValidUrl;
use App\Messenger\Object\RevampScanRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

class RevampScanRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => "Type d'entrée",
                'mapped' => false,
                'required' => true,
                'choices' => [
                    'URL' => 'url',
                    'Fichier CSV' => 'file',
                ],
            ])
            ->add('url', TextType::class, [
                'label' => "URL",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new ValidUrl(),
                ],
                'help' => "Exemple d'URL valide : https://www.example.com",
                'empty_data' => null
            ])
            ->add('file', FileType::class, [
                'label' => "Fichier d'urls",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new ValidCsv(),
                ],
                'help' => "Le fichier doit être un CSV valide contenant une liste d'URLs.",
                'empty_data' => null
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (empty($data['file']) && empty($data['url'])) {
                $form->addError(new FormError("Veuillez fournir une URL ou un fichier CSV."));
            }
        });
    }
}