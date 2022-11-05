<?php

namespace RestClient\Testing;

use Psr\Http\Message\RequestInterface;

class PredicateRequestHandler extends RequestHandler
{
    /**
     * @var callable Example: fn(RequestInterface $request) => true|false
     */
    private $predicate;

    public function __construct(callable $predicate, callable $handler)
    {
        parent::__construct($handler);
        $this->predicate = $predicate;
    }

    public function canHandle(RequestInterface $request): bool
    {
        $p = $this->predicate;
        return $p($request);
    }
}
