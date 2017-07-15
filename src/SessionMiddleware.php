<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Stratigility\Delegate\CallableDelegateDecorator;

class SessionMiddleware implements MiddlewareInterface
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
     * @param SessionHandlerInterface $sessionHandler
     * @param array $options
     */
    public function __construct(SessionHandlerInterface $sessionHandler, array $options)
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

        $request = $request->withAttribute('session', $sessionManager);

        $response = $delegate->process($request);
        $response = $sessionManager->write($response);

        return $response;
    }

    /**
     * Handle a request
     *
     *
     * $delegate may be either a DelegateInterface instance, or a callable
     * accepting at least a request instance (in such cases, the delegate
     * will be decorated using Delegate\CallableDelegateDecorator).
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|DelegateInterface $delegate
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $delegate)
    {
        if (! $delegate instanceof DelegateInterface && is_callable($delegate)) {
            $delegate = new CallableDelegateDecorator($delegate, $response);
        }
        return $this->process($request, $delegate);
    }
}
