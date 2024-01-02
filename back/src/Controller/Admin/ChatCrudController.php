<?php

namespace App\Controller\Admin;

use App\Entity\Chat;

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

class ChatCrudController extends AbstractCrudController
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
        return Chat::class;
    }

    /**
     * Configure the page values for a specific page
     */
    public function configureCrud(Crud $crud): Crud
    {
        $this->setPageValues($crud, 'Chat');
        return $crud;
    }

    /**
     * Configure the page values for a specific page
     */
    public function setPageValues(Crud $crud, string $pageName)
    {
        // Change the title of the specified page
        if ($pageName === 'Chat') {
            $crud->setPageTitle('index', 'Liste des chats');
            $crud->setPageTitle('new', 'Ajouter un chat');
            $crud->setPageTitle('edit', 'Modifier un chat');
            $crud->setPageTitle('detail', 'Détails du chat');
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
                ->hideOnIndex(),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Actif' => 1,
                    'Inactif' => 0,
                ]),
            AssociationField::new('chatMessages', 'Messages')
                ->setTemplatePath('admin/fields/chat_message_count.html.twig')
                ->hideOnForm(),  
            AssociationField::new('user', 'Participants')
                ->setFormTypeOption('choice_label', 'firstName')
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('attr', ['data-limit' => 2]) // limit to 2 users
                ->setTemplatePath('admin/fields/user_link.html.twig'),
            
            AssociationField::new('chatMessages', 'Messages')
                ->setTemplatePath('admin/fields/chat_message_content.html.twig')
                ->hideOnIndex() 
                ->hideOnForm(),
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
