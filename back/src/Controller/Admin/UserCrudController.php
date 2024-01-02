<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\GetUsersCityService;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class UserCrudController extends AbstractCrudController
{   
    /**
     * @var Security
     */
    private $security;

    /**
     * @var GetUsersCityService
     */
    private $getUsersCityService;

    /**
     * @param Security $security
     */
    public function __construct(Security $security, GetUsersCityService $getUsersCityService)
    {
        $this->security = $security;
        $this->getUsersCityService = $getUsersCityService;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Configure the page values for a specific page
     */
    public function configureCrud(Crud $crud): Crud
    {
        $this->setPageValues($crud, 'User');
        return $crud;
    }

    /**
     * Configure the page values for a specific page
     */
    public function setPageValues(Crud $crud, string $pageName)
    {
        // Change the title of the specified page
        if ($pageName === 'User') {
            $crud->setPageTitle('index', 'Liste des utilisateurs');
            $crud->setPageTitle('new', 'Ajouter un utilisateur');
            $crud->setPageTitle('edit', 'Modifier un utilisateur');
            $crud->setPageTitle('detail', 'Détails de l\'utilisateur');
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
                ->hideOnForm()
                ->hideOnIndex(),
            TextField::new('lastName', 'Nom de famille'),
            TextField::new('firstName', 'Prénom'),
            TextField::new('email', 'Email'),
            TextField::new('password', 'Mot de passe')
                ->hideOnDetail()
                ->hideOnIndex(),
            TextField::new('job', 'Profession'),
            NumberField::new('experienceDuration', 'Durée d\'expérience')
                ->hideOnIndex(),
            ChoiceField::new('parent', 'Type de profil')
                ->setChoices([
                    'Gardien' => 0,
                    'Parent' => 1,
                ]),
            BooleanField::new('subscriber', 'Abonné'),
            BooleanField::new('isProfessional', 'Professionnel')
                ->hideOnIndex(),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Profil actif' => 1,
                    'Profil suspendu' => 0,
                ]),
            TextField::new('street', 'Adresse'),
            TextField::new('city', 'Ville'),
            TextField::new('zip', 'Code postal'),
            TextField::new('longitude', 'Longitude')
                ->hideOnDetail()
                ->hideOnForm()
                ->hideOnIndex(),
            TextField::new('latitude', 'Latitude')
                ->hideOnDetail()
                ->hideOnForm()
                ->hideOnIndex(),
            ImageField::new('profilPicture')
                ->setBasePath('uploads/images/avatars/')
                ->setUploadDir('public/uploads/images/avatars/')
                ->setSortable(false)
                ->setFormTypeOptions([
                    'multiple' => false, 
                    'required' => false, 
                    'mapped' => false
                ]),
            NumberField::new('hourPrice', 'Taux Horaire')
                ->hideOnIndex(),
            BooleanField::new('vehicle', 'Véhiculé')
                ->hideOnIndex(),
            NumberField::new('maxArea', 'Zone de déplacement (km)')
                ->hideOnIndex(),
            DateField::new('birthday', 'Anniversaire')
                ->hideOnIndex(),
            AssociationField::new('availabilities', 'Disponibilité(s)')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('required', false)
                ->setFormTypeOption('choice_label', 'id')
                ->hideOnIndex(),
            AssociationField::new('chats', 'Chat(s)')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('required', false)
                ->setFormTypeOption('choice_label', 'id')
                ->hideOnIndex(),
            AssociationField::new('childrens', 'Enfants')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('required', false)
                ->setFormTypeOption('choice_label', 'firstName')
                ->hideOnIndex(),
            AssociationField::new('orders', 'Factures(s)')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('required', false)
                ->setFormTypeOption('choice_label', 'id')
                ->hideOnDetail()
                ->hideOnIndex(),
            AssociationField::new('authorComments', 'Auteur de commentaire')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('required', false)
                ->setFormTypeOption('choice_label', 'author')
                ->hideOnDetail()
                ->hideOnIndex(),
            AssociationField::new('subjectComments', 'sujet du commentaire')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('required', false)
                 ->setFormTypeOption('choice_label', 'subject')
                ->hideOnDetail()
                ->hideOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/YY HH:mm')
                ->setTimezone('Europe/Paris')
                ->hideOnIndex()
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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('email', 'Email')
            ->add('parent', 'Type de profil')
            ->add('subscriber', 'Abonné')
            ->add(ChoiceFilter::new('status', 'Statut')
               ->setChoices([
                   'Actif' => 0,
                   'Archivé' => 1,
               ])
           )
            ->add(ChoiceFilter::new('city', 'Ville')
               ->setChoices($this->GetUsersCities())
            )        
            ->add(ChoiceFilter::new('zip', 'Code postal')
               ->setChoices($this->GetUsersCitiesZip())
            )
        ;

    }

    /**
     * Get all cities from users
     * @return array
     */
    private function GetUsersCities()
    {   
        return $this->getUsersCityService->getAllCities();
    }

    /**
     * Get all cities zip from users
     * @return array
     */
    private function GetUsersCitiesZip()
    {
        return $this->getUsersCityService->getAllCitiesZip();
    }

}
