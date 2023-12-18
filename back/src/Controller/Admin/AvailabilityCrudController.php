<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Availability;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AvailabilityCrudController extends AbstractCrudController
{   
    /**
     * @var Security
     */
    private $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public static function getEntityFqcn(): string
    {
        return Availability::class;
    }

    /**
     * Configure the page values for a specific page
     */
    public function configureCrud(Crud $crud): Crud
    {
        $this->setPageValues($crud, 'Availability');
        return $crud;
    }

    /**
     * Configure the page values for a specific page
     */
    public function setPageValues(Crud $crud, string $pageName)
    {
        // Change the title of the specified page
        if ($pageName === 'Availability') {
            $crud->setPageTitle('index', 'Liste des disponibilités');
            $crud->setPageTitle('new', 'Ajouter une disponibilité');
            $crud->setPageTitle('edit', 'Modifier une disponibilité');
            $crud->setPageTitle('detail', 'Détails de la disponibilité');
            $crud->setDefaultSort(['id' => 'DESC']);
        }
    }

    /**
     * Configure the fields of the Crud
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name', 'Nom'),
            DateTimeField::new('startDate', 'Début'),
            DateTimeField::new('endDate', 'Début'),
            BooleanField::new('isRecurrent', 'Récurrent'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Active' => 1,
                    'Inactive' => 0,
                ]),
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/YY HH:mm')
                ->setTimezone('Europe/Paris')
                ->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')
                ->setFormat('dd/MM/YY HH:mm')
                ->setTimezone('Europe/Paris')
                ->hideOnIndex()
                ->hideOnForm(),
            AssociationField::new('user', 'Utilisateur')
                ->setFormTypeOption('choice_label', 'firstName'),
        ];
    }


    // lien vers le detail d'une fiche
    public function configureActions(Actions $actions): Actions
    {   
        $isSuperAdmin = $this->security->isGranted('ROLE_ADMIN');
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        if($isSuperAdmin || $isAdmin) {
            $actions
                ->setPermission(Action::NEW, 'ROLE_ADMIN')
                ->setPermission(Action::EDIT, 'ROLE_ADMIN')
                ->setPermission(Action::DELETE, 'ROLE_ADMIN')
                ->setPermission(Action::DETAIL, 'ROLE_ADMIN');
        } else {
            $actions
                ->disable(Action::NEW, Action::EDIT, Action::DELETE, Action::DETAIL);
        }
        
        $detailView = Action::new('detailView', 'Détails')
            ->linkToCrudAction('detail')
            ->setCssClass('btn btn-info')
            ->setHtmlAttributes(['target' => '_self']);

        return $actions
            ->add(Crud::PAGE_INDEX, $detailView);
    }

}
