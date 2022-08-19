<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Post;
use App\Entity\Message;
use App\Trait\PaginationTrait;
use App\Repository\MessageRepository;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Controller\Admin\Field\MessageStartField;
use App\Controller\Admin\Field\MessageAnswerField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\JsonResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Controller\Admin\Field\MessageCollectionField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostCrudController extends AbstractCrudController
{
    use PaginationTrait;

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private EntityManagerInterface $em
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort([
                "lastMsgAt" => "DESC"
            ])
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', "Titre")
                ->hideOnForm()
            ,
            NumberField::new('nbComments', "Commentaires")
                ->hideOnForm()
            ,
            DateTimeField::new('createdAt', "Créer le")
                ->hideOnForm()
            ,
            DateTimeField::new('lastMsgAt', "Dernier message le")
                ->hideOnForm()
            ,
            BooleanField::new('isRequestAnswer', "Attend une réponse")
                ->renderAsSwitch(false)
                ->hideOnForm()
            ,
            BooleanField::new('isModerate', "Modéré")
                ->renderAsSwitch(false)
                ->hideOnForm()
            ,
            TextField::new('author.username', "Auteur")
                ->hideOnForm()
            ,
            MessageStartField::new('startMsg', "Message d'origine")
                ->onlyOnDetail()
            ,
            MessageAnswerField::new('answersMessages', "Réponse")
                ->onlyOnDetail()
            ,
            MessageCollectionField::new('emptyList', "Messages")
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

        $retablishAction = Action::new("retablish", "Rétablir", "fas fa-undo-alt")
            ->linkToCrudAction("retablishPost")
            ->displayIf(static function ($entity) {
                return $entity->getIsModerate();
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
            ->add(Crud::PAGE_DETAIL, $retablishAction)
        ;
    }

    public function moderatePost(AdminContext $context): Response
    {
        return $this->editModerateStatutOfAPost($context, true);
    }

    public function retablishPost(AdminContext $context): Response
    {
        return $this->editModerateStatutOfAPost($context, false);
    }

    private function editModerateStatutOfAPost(AdminContext $context, bool $isModerate): Response
    {
        /** @var Post */
        $post = $context->getEntity()->getInstance();

        $post->setIsModerate($isModerate);

        $this->em->persist($post);
        $this->em->flush();

        return $this->redirect($context->getReferrer());
    }

    #[Route("/admin/list_message/{id}/{page}")]
    public function getMessagePaginated(
        Post $post,
        MessageRepository $messageRepository,
        DateTimeFormatter $dateTimeFormatter,
        int $page = 1
    ): Response {
        $pagination = $this->getParameter("paginationPost");

        $nb = $messageRepository->getMessageOfAPostNb($post);

        $messages = $messageRepository->getMessageOfAPostPaginated(
            post: $post,
            pagination: $pagination,
            page: $page
        );

        /** @var array */
        $messages = array_map([$this, 'messageToJson'], $messages);

        foreach ($messages as $key => $msg) {
            $messages[$key]['createdAt'] = $dateTimeFormatter->formatDiff(
                $msg['createdAt'],
                new DateTime()
            );

            $messages[$key]['urlDetail'] = $this->adminUrlGenerator
                ->setController(MessageCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($msg['id'])
                ->generateUrl()
            ;
        }

        [$isPreviousPage, $previousPageNb, $isNextPage, $nextPageNb] = $this->generatePaginationValues(
            nb: $nb,
            pagination: $pagination,
            page: $page
        );

        $responseData = [
            "list" => $messages,
            "pagination" => [
                "isPrev" => $isPreviousPage,
                "prev" => $previousPageNb,
                "isNext" => $isNextPage,
                "next" => $nextPageNb
            ]
        ];

        return new JsonResponse($responseData);
    }

    private function messageToJson(Message $msg): array
    {
        return [
            "id" => $msg->getId(),
            "content" => $msg->getContent(),
            "createdAt" => $msg->getCreatedAt(),
            "author" => $msg->getAuthor()->getUsername()
        ];
    }
}
