<?php

namespace RestClient\HttpHeaders;

use RestClient\Util\MultiValueMap;

class HttpHeaders
{
    /**
     * The HTTP  Accept header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.2 Section 5.3.2 of RFC 7231
     */
    public const ACCEPT = 'Accept';
	/**
     * The HTTP  Accept-Charset header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.3 Section 5.3.3 of RFC 7231
     */
	public const ACCEPT_CHARSET = 'Accept-Charset';
	/**
     * The HTTP  Accept-Encoding header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.4 Section 5.3.4 of RFC 7231
     */
	public const ACCEPT_ENCODING = 'Accept-Encoding';
	/**
     * The HTTP  Accept-Language header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.5 Section 5.3.5 of RFC 7231
     */
	public const ACCEPT_LANGUAGE = 'Accept-Language';
	/**
     * The HTTP  Accept-Patch header field name.
     * @since 5.3.6
     * @see https://tools.ietf.org/html/rfc5789#section-3.1 Section 3.1 of RFC 5789
     */
	public const ACCEPT_PATCH = 'Accept-Patch';
	/**
     * The HTTP  Accept-Ranges header field name.
     * @see https://tools.ietf.org/html/rfc7233#section-2.3 Section 5.3.5 of RFC 7233
     */
	public const ACCEPT_RANGES = 'Accept-Ranges';
	/**
     * The CORS  Access-Control-Allow-Credentials response header field name.
     * @see https://www.w3.org/TR/cors/ CORS W3C recommendation
     */
	public const ACCESS_CONTROL_ALLOW_CREDENTIALS = 'Access-Control-Allow-Credentials';
	/**
     * The CORS  Access-Control-Allow-Headers response header field name.
     * @see https://www.w3.org/TR/cors/ CORS W3C recommendation
     */
	public const ACCESS_CONTROL_ALLOW_HEADERS = 'Access-Control-Allow-Headers';
	/**
     * The CORS  Access-Control-Allow-Methods response header field name.
     * @see https://www.w3.org/TR/cors/ CORS W3C recommendation
     */
	public const ACCESS_CONTROL_ALLOW_METHODS = 'Access-Control-Allow-Methods';
	/**
     * The CORS  Access-Control-Allow-Origin response header field name.
     * @see https://www.w3.org/TR/cors/ CORS W3C recommendation
     */
	public const ACCESS_CONTROL_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';
	/**
     * The CORS  Access-Control-Expose-Headers response header field name.
     * @see https://www.w3.org/TR/cors/ CORS W3C recommendation
     */
	public const ACCESS_CONTROL_EXPOSE_HEADERS = 'Access-Control-Expose-Headers';
	/**
     * The CORS  Access-Control-Max-Age response header field name.
     * @see https://www.w3.org/TR/cors/ CORS W3C recommendation
     */
	public const ACCESS_CONTROL_MAX_AGE = 'Access-Control-Max-Age';
	/**
     * The CORS  Access-Control-Request-Headers request header field name.
     * @see https://www.w3.org/TR/cors/ CORS W3C recommendation
     */
	public const ACCESS_CONTROL_REQUEST_HEADERS = 'Access-Control-Request-Headers';
	/**
     * The CORS  Access-Control-Request-Method request header field name.
     * @see https://www.w3.org/TR/cors/ CORS W3C recommendation
     */
	public const ACCESS_CONTROL_REQUEST_METHOD = 'Access-Control-Request-Method';
	/**
     * The HTTP  Age header field name.
     * @see https://tools.ietf.org/html/rfc7234#section-5.1 Section 5.1 of RFC 7234
     */
	public const AGE = 'Age';
	/**
     * The HTTP  Allow header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-7.4.1 Section 7.4.1 of RFC 7231
     */
	public const ALLOW = 'Allow';
	/**
     * The HTTP  Authorization header field name.
     * @see https://tools.ietf.org/html/rfc7235#section-4.2 Section 4.2 of RFC 7235
     */
	public const AUTHORIZATION = 'Authorization';
	/**
     * The HTTP  Cache-Control header field name.
     * @see https://tools.ietf.org/html/rfc7234#section-5.2 Section 5.2 of RFC 7234
     */
	public const CACHE_CONTROL = 'Cache-Control';
	/**
     * The HTTP  Connection header field name.
     * @see https://tools.ietf.org/html/rfc7230#section-6.1 Section 6.1 of RFC 7230
     */
	public const CONNECTION = 'Connection';
	/**
     * The HTTP  Content-Encoding header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.2.2 Section 3.1.2.2 of RFC 7231
     */
	public const CONTENT_ENCODING = 'Content-Encoding';
	/**
     * The HTTP  Content-Disposition header field name.
     * @see https://tools.ietf.org/html/rfc6266 RFC 6266
     */
	public const CONTENT_DISPOSITION = 'Content-Disposition';
	/**
     * The HTTP  Content-Language header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.3.2 Section 3.1.3.2 of RFC 7231
     */
	public const CONTENT_LANGUAGE = 'Content-Language';
	/**
     * The HTTP  Content-Length header field name.
     * @see https://tools.ietf.org/html/rfc7230#section-3.3.2 Section 3.3.2 of RFC 7230
     */
	public const CONTENT_LENGTH = 'Content-Length';
	/**
     * The HTTP  Content-Location header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.4.2 Section 3.1.4.2 of RFC 7231
     */
	public const CONTENT_LOCATION = 'Content-Location';
	/**
     * The HTTP  Content-Range header field name.
     * @see https://tools.ietf.org/html/rfc7233#section-4.2 Section 4.2 of RFC 7233
     */
	public const CONTENT_RANGE = 'Content-Range';
	/**
     * The HTTP  Content-Type header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-3.1.1.5 Section 3.1.1.5 of RFC 7231
     */
	public const CONTENT_TYPE = 'Content-Type';
	/**
     * The HTTP  Cookie header field name.
     * @see https://tools.ietf.org/html/rfc2109#section-4.3.4 Section 4.3.4 of RFC 2109
     */
	public const COOKIE = 'Cookie';
	/**
     * The HTTP  Date header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.1.2 Section 7.1.1.2 of RFC 7231
     */
	public const DATE = 'Date';
	/**
     * The HTTP  ETag header field name.
     * @see https://tools.ietf.org/html/rfc7232#section-2.3 Section 2.3 of RFC 7232
     */
	public const ETAG = 'ETag';
	/**
     * The HTTP  Expect header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.1.1 Section 5.1.1 of RFC 7231
     */
	public const EXPECT = 'Expect';
	/**
     * The HTTP  Expires header field name.
     * @see https://tools.ietf.org/html/rfc7234#section-5.3 Section 5.3 of RFC 7234
     */
	public const EXPIRES = 'Expires';
	/**
     * The HTTP  From header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.1 Section 5.5.1 of RFC 7231
     */
	public const FROM = 'From';
	/**
     * The HTTP  Host header field name.
     * @see https://tools.ietf.org/html/rfc7230#section-5.4 Section 5.4 of RFC 7230
     */
	public const HOST = 'Host';
	/**
     * The HTTP  If-Match header field name.
     * @see https://tools.ietf.org/html/rfc7232#section-3.1 Section 3.1 of RFC 7232
     */
	public const IF_MATCH = 'If-Match';
	/**
     * The HTTP  If-Modified-Since header field name.
     * @see https://tools.ietf.org/html/rfc7232#section-3.3 Section 3.3 of RFC 7232
     */
	public const IF_MODIFIED_SINCE = 'If-Modified-Since';
	/**
     * The HTTP  If-None-Match header field name.
     * @see https://tools.ietf.org/html/rfc7232#section-3.2 Section 3.2 of RFC 7232
     */
	public const IF_NONE_MATCH = 'If-None-Match';
	/**
     * The HTTP  If-Range header field name.
     * @see https://tools.ietf.org/html/rfc7233#section-3.2 Section 3.2 of RFC 7233
     */
	public const IF_RANGE = 'If-Range';
	/**
     * The HTTP  If-Unmodified-Since header field name.
     * @see https://tools.ietf.org/html/rfc7232#section-3.4 Section 3.4 of RFC 7232
     */
	public const IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
	/**
     * The HTTP  Last-Modified header field name.
     * @see https://tools.ietf.org/html/rfc7232#section-2.2 Section 2.2 of RFC 7232
     */
	public const LAST_MODIFIED = 'Last-Modified';
	/**
     * The HTTP  Link header field name.
     * @see https://tools.ietf.org/html/rfc5988 RFC 5988
     */
	public const LINK = 'Link';
	/**
     * The HTTP  Location header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.2 Section 7.1.2 of RFC 7231
     */
	public const LOCATION = 'Location';
	/**
     * The HTTP  Max-Forwards header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.1.2 Section 5.1.2 of RFC 7231
     */
	public const MAX_FORWARDS = 'Max-Forwards';
	/**
     * The HTTP  Origin header field name.
     * @see https://tools.ietf.org/html/rfc6454 RFC 6454
     */
	public const ORIGIN = 'Origin';
	/**
     * The HTTP  Pragma header field name.
     * @see https://tools.ietf.org/html/rfc7234#section-5.4 Section 5.4 of RFC 7234
     */
	public const PRAGMA = 'Pragma';
	/**
     * The HTTP  Proxy-Authenticate header field name.
     * @see https://tools.ietf.org/html/rfc7235#section-4.3 Section 4.3 of RFC 7235
     */
	public const PROXY_AUTHENTICATE = 'Proxy-Authenticate';
	/**
     * The HTTP  Proxy-Authorization header field name.
     * @see https://tools.ietf.org/html/rfc7235#section-4.4 Section 4.4 of RFC 7235
     */
	public const PROXY_AUTHORIZATION = 'Proxy-Authorization';
	/**
     * The HTTP  Range header field name.
     * @see https://tools.ietf.org/html/rfc7233#section-3.1 Section 3.1 of RFC 7233
     */
	public const RANGE = 'Range';
	/**
     * The HTTP  Referer header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.2 Section 5.5.2 of RFC 7231
     */
	public const REFERER = 'Referer';
	/**
     * The HTTP  Retry-After header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.3 Section 7.1.3 of RFC 7231
     */
	public const RETRY_AFTER = 'Retry-After';
	/**
     * The HTTP  Server header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-7.4.2 Section 7.4.2 of RFC 7231
     */
	public const SERVER = 'Server';
	/**
     * The HTTP  Set-Cookie header field name.
     * @see https://tools.ietf.org/html/rfc2109#section-4.2.2 Section 4.2.2 of RFC 2109
     */
	public const SET_COOKIE = 'Set-Cookie';
	/**
     * The HTTP  Set-Cookie2 header field name.
     * @see https://tools.ietf.org/html/rfc2965 RFC 2965
     */
	public const SET_COOKIE2 = 'Set-Cookie2';
	/**
     * The HTTP  TE header field name.
     * @see https://tools.ietf.org/html/rfc7230#section-4.3 Section 4.3 of RFC 7230
     */
	public const TE = 'TE';
	/**
     * The HTTP  Trailer header field name.
     * @see https://tools.ietf.org/html/rfc7230#section-4.4 Section 4.4 of RFC 7230
     */
	public const TRAILER = 'Trailer';
	/**
     * The HTTP  Transfer-Encoding header field name.
     * @see https://tools.ietf.org/html/rfc7230#section-3.3.1 Section 3.3.1 of RFC 7230
     */
	public const TRANSFER_ENCODING = 'Transfer-Encoding';
	/**
     * The HTTP  Upgrade header field name.
     * @see https://tools.ietf.org/html/rfc7230#section-6.7 Section 6.7 of RFC 7230
     */
	public const UPGRADE = 'Upgrade';
	/**
     * The HTTP  User-Agent header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-5.5.3 Section 5.5.3 of RFC 7231
     */
	public const USER_AGENT = 'User-Agent';
	/**
     * The HTTP  Vary header field name.
     * @see https://tools.ietf.org/html/rfc7231#section-7.1.4 Section 7.1.4 of RFC 7231
     */
	public const VARY = 'Vary';
	/**
     * The HTTP  Via header field name.
     * @see https://tools.ietf.org/html/rfc7230#section-5.7.1 Section 5.7.1 of RFC 7230
     */
	public const VIA = 'Via';
	/**
     * The HTTP  Warning header field name.
     * @see https://tools.ietf.org/html/rfc7234#section-5.5 Section 5.5 of RFC 7234
     */
	public const WARNING = 'Warning';
	/**
     * The HTTP  WWW-Authenticate header field name.
     * @see https://tools.ietf.org/html/rfc7235#section-4.1 Section 4.1 of RFC 7235
     */
	public const WWW_AUTHENTICATE = 'WWW-Authenticate';


    private MultiValueMap $headers;


    public function __construct(array $headers = [])
    {
        $this->headers = new MultiValueMap(true);

        foreach ($headers as $headerName => $headerValue) {
            $this->setHeader($headerName, $headerValue);
        }
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return $this
     */
    public function setHeader(string $name, $value): self
    {
        $this->set($name, $value);
        return $this;
    }

    public function getAll(): array
    {
        return \iterator_to_array($this->headers->getIterator());
    }

    /**
     * @param iterable<string> $mediaTypeList
     * @return $this
     */
    public function setAccept(iterable $mediaTypeList): self
    {
        return $this->setHeader(self::ACCEPT, $mediaTypeList);
    }

    /**
     * @return iterable<string>
     */
    public function getAccept(): iterable
    {
        return $this->get(self::ACCEPT);
    }

    /**
     * @param iterable<string> $charsetList
     * @return $this
     */
    public function setAcceptCharset(iterable $charsetList): self
    {
        return $this->setHeader(self::ACCEPT_CHARSET, $charsetList);
    }

    /**
     * @return iterable<string>
     */
    public function getAcceptCharset(): iterable
    {
        return $this->get(self::ACCEPT_CHARSET);
    }

    /**
     * @param iterable<string> $languages
     * @return $this
     */
    public function setAcceptLanguage(iterable $languages): self
    {
        return $this->setHeader(self::ACCEPT_LANGUAGE, $languages);
    }

    public function getAcceptLanguage(): iterable
    {
        return $this->get(self::ACCEPT_LANGUAGE);
    }

    /**
     * @param iterable<string> $mediaTypeList
     * @return $this
     */
    public function setAcceptPatch(iterable $mediaTypeList): self
    {
        return $this->setHeader(self::ACCEPT_PATCH, $mediaTypeList);
    }

    /**
     * @param bool $allowCredentials
     * @return $this
     */
    public function setAccessControlAllowCredentials(bool $allowCredentials): self
    {
        if ($allowCredentials) {
            return $this->setHeader(self::ACCESS_CONTROL_ALLOW_CREDENTIALS, 'true');
        }

        $this->headers->remove(self::ACCESS_CONTROL_ALLOW_CREDENTIALS);

        return $this;
    }

    public function getAccessControlAllowCredentials(): bool
    {
        return 'true' === $this->getValue(self::ACCESS_CONTROL_ALLOW_CREDENTIALS);
    }

    /**
     * @param iterable<string> $allowedHeaderList
     * @return $this
     */
    public function setAccessControlAllowHeaders(iterable $allowedHeaderList): self
    {
        return $this->setHeader(self::ACCESS_CONTROL_ALLOW_HEADERS, $allowedHeaderList);
    }

    /**
     * @return iterable<string>
     */
    public function getAccessControlAllowHeaders(): iterable
    {
        return $this->get(self::ACCESS_CONTROL_ALLOW_HEADERS);
    }

    /**
     * @param iterable<string> $allowedMethodList
     * @return $this
     */
    public function setAccessControlAllowMethods(iterable $allowedMethodList): self
    {
        return $this->setHeader(self::ACCESS_CONTROL_ALLOW_METHODS, $allowedMethodList);
    }

    /**
     * @return iterable<string>
     */
    public function getAccessControlAllowMethods(): iterable
    {
        return $this->get(self::ACCESS_CONTROL_ALLOW_METHODS);
    }

    public function setAccessControlAllowOrigin(string $allowedOrigin): self
    {
        return $this->setHeader(self::ACCESS_CONTROL_ALLOW_ORIGIN, $allowedOrigin);
    }

    public function getAccessControlAllowOrigin(): string
    {
        return $this->getValue(self::ACCESS_CONTROL_ALLOW_ORIGIN);
    }

    /**
     * @param iterable<string> $exposedHeaderList
     * @return $this
     */
    public function setAccessControlExposeHeaders(iterable $exposedHeaderList): self
    {
        return $this->setHeader(self::ACCESS_CONTROL_EXPOSE_HEADERS, $exposedHeaderList);
    }

    /**
     * @return iterable<string>
     */
    public function getAccessControlExposeHeaders(): iterable
    {
        return $this->get(self::ACCESS_CONTROL_EXPOSE_HEADERS);
    }

    /**
     * @return iterable<string>
     */
    public function getAcceptPatch(): iterable
    {
        return $this->get(self::ACCEPT_PATCH);
    }

    public function setContentType(string $mediaType): self
    {
        $this->setHeader(self::CONTENT_TYPE, $mediaType);
        return $this;
    }

    public function getContentType(): string
    {
        return $this->getValue(self::CONTENT_TYPE);
    }

    // COMMON

    private function get(string $headerName): iterable
    {
        $headerLine = $this->headers->getFirst($headerName);
        if (null === $headerLine) {
            return [];
        }
        $headerValues = \explode(',', $headerLine);
        return \array_map(static fn(string $headerValue) => \trim($headerValue), $headerValues);
    }

    private function getValue(string $headerName): string
    {
        return $this->headers->getFirst($headerName) ?? '';
    }

    /**
     * @param string $headerName
     * @param string|string[] $value
     * @return void
     */
    private function set(string $headerName, $value): void
    {
        if (\is_array($value)) {
            $this->headers->put($headerName, \implode(', ', $value));
        } else {
            $this->headers->put($headerName, $value);
        }
    }
}


