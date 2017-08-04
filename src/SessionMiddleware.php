<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Ojhaujjwal\Session\Handler\HandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Stratigility\Delegate\CallableDelegateDecorator;

final class SessionMiddleware implements MiddlewareInterface
{
    /**
     * @var SessionManager
     */
    private $sessionHandler;
    /**
     * @var array
     */
    private $options;

    /**
     * SessionMiddleware constructor.
     * @param HandlerInterface $sessionHandler
     * @param array $options
     */
    public function __construct(HandlerInterface $sessionHandler, array $options)
    {

        $this->sessionHandler = $sessionHandler;
        $this->options = $options;
    }

    /**
     * Initiates the session at the start
     * and ends the session after the delegate stack is finished
     *
     * TODO: maybe, make request attribute configurable
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        DelegateInterface $delegate
    ) : ResponseInterface
    {
        $sessionManager = new SessionManager($this->sessionHandler, $request, $this->options);
        $sessionManager->start();

        $request = $request->withAttribute('sessionManager', $sessionManager);
        $request = $request->withAttribute('session', $sessionManager->getStorage());

        $response = $delegate->process($request);
        $response = $sessionManager->close($response);

        return $response;
    }

    /**
     * Proxy to process()
     *
     * Proxies to process, after first wrapping the `$next` argument using the
     * CallableDelegateDecorator.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        return $this->process($request, new CallableDelegateDecorator($next, $response));
    }
}
