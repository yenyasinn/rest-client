<?php declare(strict_types=1);

namespace RestClient\Interceptor;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestClient\ContextInterface;
use RestClient\IdGenerator\IdGeneratorInterface;
use RestClient\RequestExecutionInterface;

class RequestIdInterceptor implements RequestInterceptorInterface
{
    private string $headerName;
    private IdGeneratorInterface $idGenerator;

    public function __construct(IdGeneratorInterface $idGenerator, string $headerName = 'Request-ID')
    {
        $this->idGenerator = $idGenerator;
        $this->headerName = $headerName;
    }

    public function intercept(RequestInterface $request, ContextInterface $context, RequestExecutionInterface $execution): ResponseInterface
    {
        return $execution->execute(
            $request->withHeader(
                $this->headerName,
                $this->idGenerator->generate()
            ),
            $context
        );
    }
}
