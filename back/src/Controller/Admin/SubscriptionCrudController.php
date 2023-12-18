<?php 

namespace App\Controller\Admin;

use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\Security;

class SubscriptionCrudController extends AbstractCrudController
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
        return Subscription::class;
    }

    /**
     * Configure the page values for a specific page
     */
    public function configureCrud(Crud $crud): Crud
    {
        $this->setPageValues($crud, 'Subscription');
        return $crud;
    }
    /**
     * Configure the page values for a specific page
     */
    public function setPageValues(Crud $crud, string $pageName)
    {
        // Change the title of the specified page
        if ($pageName === 'Abonnement') {
            $crud->setPageTitle('index', 'Liste des abonnements');
            $crud->setPageTitle('new', 'Ajouter un abonnement');
            $crud->setPageTitle('edit', 'Modifier un abonnement');
            $crud->setPageTitle('detail', 'Détails de l\'abonnement');
            $crud->setDefaultSort(['id' => 'DESC']);
        }
    }
    public function configureFields( string $pageName ): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name', 'Nom'),
            IntegerField::new('durationMonth', 'Durée en mois'),
            TextField::new('price', 'Prix'),
            TextField::new('description', 'Description'),
            BooleanField::new('status', 'Statut'),
            AssociationField::new('orders', 'Nombre de factures')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('required', false),
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/YY HH:mm')
                ->setTimezone('Europe/Paris')
                ->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')
                ->setFormat('dd/MM/YY HH:mm')
                ->setTimezone('Europe/Paris')
                ->hideOnForm(),
       
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