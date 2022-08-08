<?php

require 'vendor/autoload.php';
require 'Model/BlogPost.php';
require 'Model/Comment.php';
require 'JsonPlaceholderClient.php';

$jsonPlaceholder = new JsonPlaceholderClient();


// Print: user_id + title

/** @var array<BlogPost> $blogPosts */
$blogPosts = $jsonPlaceholder->getPosts();

foreach ($blogPosts as $blogPost) {
    print '[userId=' . $blogPost->getUserId() . '] > ' . $blogPost->getTitle() . "\n";
}
