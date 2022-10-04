<?php

use RestClient\Exception\RestClientResponseException;

require 'vendor/autoload.php';
require 'Model/BlogPost.php';
require 'Model/Comment.php';
require 'Model/CommentNormalizer.php';
require 'JsonPlaceholderClient.php';

$jsonPlaceholder = new JsonPlaceholderClient();

$blogPostId = 2;

try {
    /** @var BlogPost $blogPost */
    $blogPost = $jsonPlaceholder->getPost($blogPostId);

    print 'ID:     ' . $blogPost->getId() . "\n";
    print '----------------------------------' . "\n";
    print 'UserID: ' . $blogPost->getUserId() . "\n";
    print '----------------------------------' . "\n";
    print 'Title:  ' . $blogPost->getTitle() . "\n";
    print '----------------------------------' . "\n";
    print 'Body:   ' . "\n" . $blogPost->getBody() . "\n";

} catch (RestClientResponseException $exception) {
    var_dump($exception->getData());
}
