<?php

namespace App\Controller\Admin;

use App\Entity\SousCategorie;
use Doctrine\ORM\EntityRepository;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SousCategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SousCategorie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

        
            TextField::new('nom'),
            //TextField::new('image')->setFormType(VichImageType::class)->onlyWhenCreating(),
            TextField::new('imageFile')->setFormType(VichImageType::class),
            ImageField::new('image')->setBasePath('/images')->onlyOnIndex(),
            AssociationField::new('categorie')
            // ->setFormTypeOption([
            //     'query_builder' => function (EntityRepository $er) {
            //         return $er->createQueryBuilder('c')
            //         ->orderBy('c.nom', 'ASC');
            //     },
            //  ])
            //  ->setRequired(true),
            
            ];
        }
    
}

