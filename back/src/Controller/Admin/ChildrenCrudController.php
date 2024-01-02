<?php

namespace App\Controller\Admin;

use App\Entity\Children;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class ChildrenCrudController extends AbstractCrudController
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
        return Children::class;
    }

    /**
     * Configure the page values for a specific page
     */
    public function configureCrud(Crud $crud): Crud
    {
        $this->setPageValues($crud, 'Children');
        return $crud;
    }

    /**
     * Configure the page values for a specific page
     */
    public function setPageValues(Crud $crud, string $pageName)
    {
        // Change the title of the specified page
        if ($pageName === 'Children') {
            $crud->setPageTitle('index', 'Liste des enfants');
            $crud->setPageTitle('new', 'Ajouter un enfant');
            $crud->setPageTitle('edit', 'Modifier un enfant');
            $crud->setPageTitle('detail', 'Détails de l\'enfant');
            $crud->setDefaultSort(['id' => 'DESC']);
        }
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
            ->setTemplatePath('admin/fields/id_link.html.twig')
            ->hideOnDetail()
            ->hideOnForm(),
            IdField::new('id', 'Identifiant')
                ->hideOnIndex()
                ->hideOnForm(),
            DateField::new('birthday', 'Date de naissance'),
            TextField::new('gender', 'Genre')
                ->setTemplatePath('admin/fields/children_gender.html.twig'),
            TextField::new('firstName', 'Prénom'),
            TextField::new('description', 'Description'),
            BooleanField::new('status', 'Statut'),
            AssociationField::new('user', 'Parent'),
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/YY HH:mm')
                ->setTimezone('Europe/Paris')
                ->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')
                ->setFormat('dd/MM/YY HH:mm')
                ->setTimezone('Europe/Paris')
                ->hideOnIndex()
                ->hideOnForm(),
        ];
    }
    

    // lien vers le detail d'une fiche
    public function configureActions(Actions $actions): Actions
    {   
        $isSuperAdmin = $this->security->isGranted('ROLE_SUPER_ADMIN');
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        if($isSuperAdmin || $isAdmin) {
            $actions
                ->setPermission(Action::NEW, 'ROLE_ADMIN')
                ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN')
                ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
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
