<?php

return [
    // Guest routes
    'books' => 'library/guest/book/index',
    'books/<id:\d+>' => 'library/guest/book/view',

    'authors' => 'library/guest/author/index',
    'authors/<id:\d+>' => 'library/guest/author/view',
    'authors/<id:\d+>/subscribe' => 'library/guest/author/subscribe',

    'report/top-authors' => 'library/guest/report/top-authors',

    // User routes
    'user/books' => 'library/user/book/index',
    'user/books/create' => 'library/user/book/create',
    'user/books/<id:\d+>' => 'library/user/book/view',
    'user/books/<id:\d+>/update' => 'library/user/book/update',
    'user/books/<id:\d+>/delete' => 'library/user/book/delete',

    'user/authors' => 'library/user/author/index',
    'user/authors/create' => 'library/user/author/create',
    'user/authors/<id:\d+>' => 'library/user/author/view',
    'user/authors/<id:\d+>/update' => 'library/user/author/update',
    'user/authors/<id:\d+>/delete' => 'library/user/author/delete',
];