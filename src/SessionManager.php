<?php
declare(strict_types=1);

namespace Ojhaujjwal\Session;

use ParagonIE\Cookie\Cookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7SessionEncodeDecode\Decoder;
use PSR7SessionEncodeDecode\Encoder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zend\Math\Rand;

final class SessionManager implements SessionManagerInterface
{
    /**
     * @var OptionsResolver
     */
    private static $optionsResolver;

    /**
     * @var OptionsResolver
     */
    private static $cookieOptionsResolver;

    /**
     * @var Encoder
     */
    private static $sessionEncoder;

    /**
     * @var Decoder
     */
    private static $sessionDecoder;
    /**
     * @var SessionHandlerInterface
     */
    private $handler;
    /**
     * @var array
     */
    private $options;
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var bool
     */
    private $started;

    public function __construct(
        SessionHandlerInterface $handler,
        ServerRequestInterface $request,
        array $options
    )
    {
        $this->handler = $handler;
        $this->request = $request;

        if (null === self::$optionsResolver) {
            $resolver = new OptionsResolver();
            $resolver->setDefaults(['cookie' => [], 'sid_length' => 40]);
            $resolver->setRequired(['name']);

            self::$optionsResolver = $resolver;
        }

        if (null === self::$cookieOptionsResolver) {
            $resolver = new OptionsResolver();
            $resolver->setDefaults(['cookie' => [
                'domain' => $this->request->getHeaderLine('Host'),
                'path' => '/',
                'http_only' => true,
                'secure_only' => $this->request->getUri()->getScheme() === 'https',
                'lifetime' => 0,
                'same_site' => Cookie::SAME_SITE_RESTRICTION_LAX,
            ]]);

            self::$cookieOptionsResolver = $resolver;
        }

        $this->options = self::$optionsResolver->resolve($options);
        $this->options['cookie'] = self::$cookieOptionsResolver->resolve($this->options['cookie']);
    }

    /**
     * {@inheritdoc}
     */
    public function start(): void
    {
        if ($this->isStarted()) {
            return;
        }

        $name = $this->getName();
        $this->sessionId = $this->request->getCookieParams()[$name] ?? $this->generateId();
        $this->attributes = $this->readFromHandler();
        $this->started = true;
    }

    private function sessionEncode(array $attributes): string
    {
        if (null === self::$sessionEncoder) {
            self::$sessionEncoder = new Encoder();
        }
        $sessionEncoder = self::$sessionEncoder;

        return $sessionEncoder($attributes);
    }

    private function sessionDecode(string $decoded): array
    {
        if (null === self::$sessionDecoder) {
            self::$sessionDecoder = new Decoder();
        }

        $sessionDecoder = self::$sessionDecoder;

        return self::$sessionDecoder($decoded);
    }

    /**
     * Save the session data to storage.
     *
     * @return void
     */
    private function save(): void
    {
        $this->handler->write($this->getId(), $this->prepareForStorage(
            $this->sessionEncode($this->attributes)
        ));
        $this->started = false;
    }


    /**
     * Read the session data from the handler.
     *
     * @return array
     */
    private function readFromHandler(): array
    {
        if ($data = $this->handler->read($this->getId())) {
            $data = $this->sessionDecode($this->prepareForUnserialize($data));
            if (false !== $data && null !== $data && is_array($data)) {
                return $data;
            }
        }
        return [];
    }

    /**
     * Prepare the raw string data from the session for unserialization.
     *
     * @param  string  $data
     * @return string
     */
    private function prepareForUnserialize(string $data): string
    {
        return $data;
    }


    /**
     * Prepare the serialized session data for storage.
     *
     * @param  string  $data
     * @return string
     */
    private function prepareForStorage(string $data): string
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->sessionId;
    }

    private function generateId(): string
    {
        return Rand::getString($this->options['sid_length']);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): void
    {
        unset($this->attributes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        $this->attributes = [];
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($destroy = false): void
    {
        if ($destroy) {
            $this->handler->destroy($this->getId());
        }
        $this->sessionId = $this->generateId();
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    private function buildCookie(): string
    {
        $cookie = new Cookie($this->getName(), $this->options['cookie']['domain']);
        $cookie->setValue($this->sessionId);
        $cookie->setPath($this->options['cookie']['path']);
        $cookie->setHttpOnly($this->options['cookie']['http_only']);
        $cookie->setSecureOnly($this->options['cookie']['secure_only']);
        $cookie->setMaxAge($this->options['cookie']['lifetime']);
        $cookie->setSameSiteRestriction($this->options['cookie']['same_site']);
        return substr((string) $cookie, strlen('Set-Cookie: '));
    }

    /**
     * {@inheritdoc}
     */
    public function write(ResponseInterface $response): ResponseInterface
    {
        $this->save();
        return $response->withHeader(
            'Set-Cookie',
            $this->buildCookie()
        );
    }

    private function getName(): string
    {
        return $this->options['name'];
    }
}
