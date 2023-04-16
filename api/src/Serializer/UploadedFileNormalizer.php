<?php

namespace App\Serializer;

use App\Entity\Content;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

final class UploadedFileNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private string $apiDefaultUri,
        private readonly StorageInterface $storage,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;

        $thumbnailUrl = $this->storage->resolveUri($object, 'thumbnailFile');
        $object->setThumbnailUrl($this->apiDefaultUri . $thumbnailUrl);
        if ($this->authorizationChecker->isGranted('CONTENT_VIEW_VIDEO', $object)) {
            $mediaLinkUrl = $this->storage->resolveUri($object, 'mediaLinkFile');
            $object->setMediaLinkUrl($this->apiDefaultUri . '/contents/' . $object->getId() . $mediaLinkUrl);
            if (isset($context['groups'])) {
                $context['groups'][] = 'content:video:read';
            }
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Content;
    }
}