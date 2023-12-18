<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
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
        return Order::class;
    }

    /**
     * Configure the page values for a specific page
     */
    public function configureCrud(Crud $crud): Crud
    {
        $this->setPageValues($crud, 'Order');
        return $crud;
    }
    /**
     * Configure the page values for a specific page
     */
    public function setPageValues(Crud $crud, string $pageName)
    {
        // Change the title of the specified page
        if ($pageName === 'Factures') {
            $crud->setPageTitle('index', 'Liste des factures');
            $crud->setPageTitle('new', 'Ajouter une facture');
            $crud->setPageTitle('edit', 'Modifier une facture');
            $crud->setPageTitle('detail', 'Détails de la facture');
            $crud->setDefaultSort(['id' => 'DESC']);
        }
    }
    public function configureFields(string $pageName ): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('paymentType', 'Type de payement')
                ->setChoices([
                    'CB' => "CB",
                    'PayPal' => "Paypal",
                    "Chèque" => "Chèque",
                    "Espèce" => "Espèce",
                ]),
            BooleanField::new('status', 'Statut'),
            BooleanField::new('isRecurrent', 'Est récurrent'),
            DateTimeField::new('createdAt', 'Créé le')
            ->setFormat('dd/MM/YY HH:mm')
            ->setTimezone('Europe/Paris')
            ->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')
            ->setFormat('dd/MM/YY HH:mm')
            ->setTimezone('Europe/Paris')
            ->hideOnForm(),
            AssociationField::new('user', 'Utilisateur')
            ->setFormTypeOption('by_reference', false)
            ->setFormTypeOption('multiple', true)
            ->setFormTypeOption('required', false),
            AssociationField::new('subscription', 'Abonnement')
            ->setFormTypeOption('by_reference', false)
            ->setFormTypeOption('multiple', true)
            ->setFormTypeOption('required', false),
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