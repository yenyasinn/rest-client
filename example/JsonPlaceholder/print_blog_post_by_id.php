<?php

require 'vendor/autoload.php';
require 'Model/BlogPost.php';
require 'Model/Comment.php';
require 'JsonPlaceholderClient.php';

$jsonPlaceholder = new JsonPlaceholderClient();

$blogPostId = 2;

/** @var BlogPost $blogPost */
$blogPost = $jsonPlaceholder->getPost($blogPostId);

print 'ID:     ' . $blogPost->getId() . "\n";
print '----------------------------------' . "\n";
print 'UserID: ' . $blogPost->getUserId() . "\n";
print '----------------------------------' . "\n";
print 'Title:  ' . $blogPost->getTitle() . "\n";
print '----------------------------------' . "\n";
print 'Body:   ' . "\n" . $blogPost->getBody() . "\n";
