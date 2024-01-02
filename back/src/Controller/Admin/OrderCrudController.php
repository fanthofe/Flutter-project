<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Subscription;
use App\Service\GetSubscriptionService;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var GetSubscriptionService
     */

    /**
     * @param Security $security
     */
    public function __construct(Security $security, GetSubscriptionService $GetSubscriptionService)
    {
        $this->security = $security;
        $this->GetSubscriptionService = $GetSubscriptionService;
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
            IdField::new('id')
                ->setTemplatePath('admin/fields/id_link.html.twig')
                ->hideOnDetail()
                ->hideOnForm(),
            IdField::new('id', 'Identifiant')
                ->hideOnForm()  
                ->hideOnIndex(),
            ChoiceField::new('paymentType', 'Type de payement')
                ->setChoices([
                    'CB' => "CB",
                    'PayPal' => "Paypal",
                    "Chèque" => "Chèque",
                    "Espèce" => "Espèce",
                ]),
            BooleanField::new('status', 'Statut du paiement'),
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
                ->setFormTypeOption('by_reference', true)
                ->setFormTypeOption('multiple', false)
                ->setFormTypeOption('required', false),
            AssociationField::new('subscription', 'Abonnement')
                ->setTemplatePath('admin/fields/subscription_name.html.twig')
                ->hideOnForm(),
            AssociationField::new('subscription', 'Abonnement')
                ->setFormTypeOption('by_reference', true)
                ->setFormTypeOption('multiple', false)
                ->setFormTypeOption('required', false)
                ->setFormTypeOption('choice_label', function ($subscription) {
                    return $subscription->getName();
                })
                ->hideOnIndex(),
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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('user', 'Utilisateur')
            ->add(EntityFilter::new('subscription', 'Abonnement'))
            ->add(ChoiceFilter::new('paymentType', 'Type de payement')
                ->setChoices([
                    'CB' => "CB",
                    'PayPal' => "Paypal",
                    "Chèque" => "Chèque",
                    "Espèce" => "Espèce",
                ])
            )
             ->add(ChoiceFilter::new('status', 'Statut')
               ->setChoices([
                   'En attante de paiement' => 0,
                   'Payée' => 1, 
               ])
           )
            ->add(ChoiceFilter::new('isRecurrent', 'Est récurrent')
                ->setChoices([
                    'Oui' => 1,
                    'Non' => 0,
                ]));
    }

    private function getAllSubscriptionName()
    {   
        return $this->GetSubscriptionService->getAllSubscriptionName();
    }
}