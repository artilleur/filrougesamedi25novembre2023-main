<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
        
            TextField::new('nom'),
            TextField::new('imageFile')->setFormType(VichImageType::class),
            ImageField::new('image')->setBasePath('/images')->onlyOnIndex(),
            // this would format 3 as 3.00 and 5.123 as 5.12
             NumberField::new('prix')->setNumDecimals(2),
            
             TextField::new('description'),
             IntegerField::new('stock'),
             //DateTimeField::new('updateAt'),
             AssociationField::new('sousCategorie') // Utilisez le nom de la relation
            //->setFielId('/home/SousCategorie.html.twig') // Remplacez par le chemin du template pour afficher les détails de sous_categorie
            
            
        ];
    }

    
   
    

   
}