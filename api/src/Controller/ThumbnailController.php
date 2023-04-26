<?php

namespace App\Controller;

use App\Entity\Content;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[AsController]
class ThumbnailController extends AbstractController
{
    public function __construct(private readonly DownloadHandler $downloadHandler)
    {
    }
    
    public function __invoke(string $thumbnail)
    {
        $course = new Content();
        $course->setThumbnail($thumbnail);
        return $this->downloadHandler->downloadObject($course, 'thumbnailFile');
    }
}