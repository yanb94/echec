<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\MessageSignalByTypeField;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Admin\PostCrudController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MessageCrudController extends AbstractCrudController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private EntityManagerInterface $em
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Message::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort([
                "isChecked" => "ASC",
                "nbSignals" => "DESC",
                "createdAt" => "DESC"
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('attachPost.title', "Post d'origine"),
            DateTimeField::new('createdAt', "Créer le"),
            TextField::new('author.username', "Auteur"),
            BooleanField::new('isModerate', "Modéré")
                ->renderAsSwitch(false)
                ->hideOnForm()
            ,
            BooleanField::new('isStartMsg', 'Premier message')
                ->renderAsSwitch(false)
            ,
            BooleanField::new('isChecked', 'Vérifié')
                ->renderAsSwitch(false)
            ,
            NumberField::new('nbSignals', "Nombre de signalement"),
            MessageSignalByTypeField::new('nbSignalsByType', "Signalements par type")
                ->onlyOnDetail()
            ,
            TextEditorField::new('content', "Contenu")
                ->onlyOnDetail()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $moderateAction = Action::new("moderate", "Modérer", "fas fa-ban")
            ->linkToCrudAction("moderatePost")
            ->displayIf(static function ($entity) {
                return !$entity->getIsModerate();
            })
        ;

        $restablishAction = Action::new("retablish", "Rétablir", "fas fa-undo-alt")
        ->linkToCrudAction("restablishPost")
        ->displayIf(static function ($entity) {
            return $entity->getIsModerate();
        });

        $seePostAction = Action::new("seePost", "Voir le post", "fas fa-eye")
            ->linkToCrudAction("seePost")
            ->setHtmlAttributes([
                "style" => "margin-right:10px;color:black"
            ])
        ;

        $validMessageAction = Action::new("validMessage", "Valider le message", "fas fa-check-circle")
            ->linkToCrudAction("validMessage")
            ->setHtmlAttributes([
                "style" => "margin-right:10px;color: darkgreen;"
            ])
            ->displayIf(static function ($entity) {
                return !$entity->getIsChecked();
            });
        ;

        $unValidMessageAction = Action::new("unValidMessage", "Dévalider le message", "fas fa-times-circle")
            ->linkToCrudAction("unValidMessage")
            ->setHtmlAttributes([
                "style" => "margin-right:10px;color: darkgreen;"
            ])
            ->displayIf(static function ($entity) {
                return $entity->getIsChecked();
            })
        ;

        return $actions
            ->disable(
                Action::NEW,
                Action::EDIT,
                Action::DELETE
            )
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $moderateAction)
            ->add(Crud::PAGE_DETAIL, $restablishAction)
            ->add(Crud::PAGE_DETAIL, $seePostAction)
            ->add(Crud::PAGE_DETAIL, $validMessageAction)
            ->add(Crud::PAGE_DETAIL, $unValidMessageAction)
        ;
    }

    public function moderatePost(AdminContext $context): Response
    {
        return $this->editModerateStatutOfAMessage($context, true);
    }

    public function restablishPost(AdminContext $context): Response
    {
        return $this->editModerateStatutOfAMessage($context, false);
    }

    private function editModerateStatutOfAMessage(AdminContext $context, bool $isModerate): Response
    {
        /** @var Message */
        $post = $context->getEntity()->getInstance();

        $post->setIsModerate($isModerate);

        $this->em->persist($post);
        $this->em->flush();

        return $this->redirect($context->getReferrer());
    }

    public function seePost(AdminContext $context) : Response
    {
        /** @var Message */
        $msg = $context->getEntity()->getInstance();

        $post = $msg->getAttachPost();

        $urlPostInCrud = $this->adminUrlGenerator
            ->setController(PostCrudController::class)
            ->setEntityId($post->getId())
            ->setAction(Action::DETAIL)
            ->generateUrl();

        return $this->redirect($urlPostInCrud);
    }

    private function changeValidMessage(AdminContext $context, bool $isChecked)
    {
        /** @var Message */
        $msg = $context->getEntity()->getInstance();

        $msg->setIsChecked($isChecked);

        $this->em->persist($msg);
        $this->em->flush();

        return $this->redirect($context->getReferrer());
    }

    public function validMessage(AdminContext $context): Response
    {
        return $this->changeValidMessage($context, true);
    }

    public function unValidMessage(AdminContext $context): Response
    {
        return $this->changeValidMessage($context, false);
    }
}
