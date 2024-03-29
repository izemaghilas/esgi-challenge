<?php

namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\BeReviewerApplication;
use App\Entity\Content;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ApiNormalizer implements DenormalizerInterface
{

    public function __construct(
        private readonly DenormalizerInterface $decorator,
        private readonly Security $security,
        private readonly IriConverterInterface $iriConverter
    ) {
    }


    public function denormalize(mixed $data, string $type, string $format = null, array $context = array())
    {
        if ($type === Content::class && $format === 'multipart') {
            $user = $this->security->getUser();
            if ($user !== null) {
                $data['creatorId'] = $this->iriConverter->getIriFromResource($user);
            }

            // don't denormalize the uploaded file
            $thumbnailFile = null;
            $mediaLinkFile = null;
            if (isset($data['thumbnailFile'])) {
                $thumbnailFile = $data['thumbnailFile'];
                unset($data['thumbnailFile']);
            }
            if (isset($data['mediaLinkFile'])) {
                $mediaLinkFile = $data['mediaLinkFile'];
                unset($data['mediaLinkFile']);
            }

            $course = $this->decorator->denormalize($data, $type, $format);
            if (null !== $thumbnailFile) {
                $course->setThumbnailFile($thumbnailFile);
            }
            if (null !== $mediaLinkFile) {
                $course->setMediaLinkFile($mediaLinkFile);
            }

            return $course;

        } else if ($type === BeReviewerApplication::class) {
            $user = $this->security->getUser();
            if ($user !== null) {
                $data['contributor'] = $this->iriConverter->getIriFromResource($user);
            }
        }
        return $this->decorator->denormalize($data, $type, $format);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        if ($type === Content::class) {
            return $this->decorator->supportsDenormalization($data, $type, $format === 'multipart' ? 'jsonld' : $format);
        }

        return $this->decorator->supportsDenormalization($data, $type, $format);
    }
}