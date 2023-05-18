<?php

namespace App\Controller;

use App\Entity\Content;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[AsController]
class VideoController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly DownloadHandler $downloadHandler
    ) {
    }
    public function __invoke($id, string $video)
    {
        $course = $this->entityManager->getRepository(Content::class)->findOneBy(['id' => $id]);
        if (null === $course || $course->getMediaLink() !== $video) {
            throw new NotFoundHttpException();
        }

        if ($this->authorizationChecker->isGranted('CONTENT_VIEW_VIDEO', $course)) {
            return $this->downloadHandler->downloadObject($course, 'mediaLinkFile');
        }

        throw new AccessDeniedHttpException();
    }
}