<?php

class CommentNormalizer implements \Symfony\Component\Serializer\Normalizer\NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var Comment $comment */
        $comment = $object;
        return [
            'ID' => $comment->getId(),
            'PostID' => $comment->getPostId(),
            'Name' => $comment->getName(),
            'Email' => $comment->getEmail(),
            'Body' => $comment->getBody(),
        ];
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Comment;
    }
}
