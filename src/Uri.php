<?php

declare(strict_types=1);

namespace Estasi\Filter;

use Estasi\Utility\{
    Interfaces\Uri as UriHandler,
    Traits\Flags,
    Traits\ParseStr,
    Traits\RemoveDotSegment,
    Traits\Uri as UriTrait,
    UriFactory
};

use function array_merge;
use function array_slice;
use function boolval;
use function compact;
use function count;
use function gethostbyaddr;
use function idn_to_ascii;
use function idn_to_utf8;
use function implode;
use function is_null;
use function is_string;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function preg_replace_callback_array;
use function preg_split;
use function rawurldecode;
use function rawurlencode;
use function sprintf;
use function strcasecmp;
use function substr_count;
use function trim;

use const IDNA_DEFAULT;
use const INTL_IDNA_VARIANT_UTS46;
use const PREG_SPLIT_NO_EMPTY;

/**
 * Class UriNormalization
 *
 * Performs uri normalization using the methods described in RFC 3986:
 * - Converting percent-encoded triplets to uppercase
 *   http://example.com/foo%2a → http://example.com/foo%2A
 *
 * - Converting the scheme and host to lowercase
 *   HTTP://User@Example.COM/Foo → http://User@example.com/Foo
 *
 * - Decoding percent-encoded triplets of unreserved characters
 *   http://example.com/%7Efoo → http://example.com/~foo
 *
 * - Removing dot-segments
 *   http://example.com/foo/./bar/baz/../qux → http://example.com/foo/bar/qux
 *
 * - Converting an empty path to a "/" path
 *   http://example.com → http://example.com/
 *
 * - Removing the default port
 *   http://example.com:80/ → http://example.com/
 *
 * - Normalizations that change semantics
 *
 * @package Estasi\Filter
 */
final class Uri extends Abstracts\Filter
{
    use RemoveDotSegment;
    use ParseStr;
    use UriTrait;
    use Flags;

    // names of constructor parameters to create via the factory
    public const OPT_URI_HANDLER      = 'uriHandler';
    public const OPT_CHANGE_SEMANTICS = 'changeSemantics';
    // default values for constructor parameters
    public const DEFAULT_URI_HANDLER = UriFactory::DEFAULT_URI_HANDLER;
    /**
     * Adding a trailing "/" to a non-empty path.
     * Directories (folders) are indicated with a trailing slash and should be included in URIs
     *
     * @example http://example.com/foo → http://example.com/foo/
     * @var int 1
     */
    public const FLAG_ADD_TRAILING_SLASH = 0b0000000001;
    /**
     * Removing directory index.
     * Default directory indexes are generally not needed in URIs.
     *
     * @example http://example.com/default.asp → http://example.com/
     * @example http://example.com/a/index.html → http://example.com/a/
     *
     * @var int 2
     */
    public const FLAG_REMOVING_DIRECTORY_INDEX = 0b0000000010;
    /**
     * Removing the fragment.
     * The fragment component of a URI is never seen by the server and can sometimes be removed.
     *
     * @example http://example.com/bar.html#section1 → http://example.com/bar.html
     *
     * @var int 4
     */
    public const FLAG_REMOVING_THE_FRAGMENT = 0b0000000100;
    /**
     * Replacing IP with domain name.
     * Check if the IP address maps to a domain name.
     *
     * @example http://208.77.188.166/ → http://example.com/
     *
     * @var int 8
     */
    public const FLAG_REPLACING_IP_WITH_DOMAIN_NAME = 0b0000001000;
    /**
     * Removing duplicate slashes.
     * Paths which include two adjacent slashes could be converted to one.
     *
     * @example http://example.com/foo//bar.html → http://example.com/foo/bar.html
     *
     * @var int 16
     */
    public const FLAG_REMOVING_DUPLICATE_SLASHES = 0b0000010000;
    /**
     * Removing “www” as the first domain label.
     *
     * @example http://www.example.com/ → http://example.com/
     *
     * @var int 32
     */
    public const FLAG_REMOVING_WWW = 0b0000100000;
    /**
     * Adding “www” as the first domain label.
     *
     * @example http://example.com/ → http://www.example.com/
     *
     * @var int 64
     */
    public const FLAG_ADDING_WWW = 0b0001000000;
    /**
     * Sorting the query parameters.
     * Some web pages use more than one query parameter in the URI. A normalizer can sort the parameters into
     * alphabetical order (with their values), and reassemble the URI.
     *
     * @example http://example.com/display?lang=en&article=fred → http://example.com/display?article=fred&lang=en
     *
     * @var int 128
     */
    public const FLAG_SORTING_THE_QUERY_PARAMETERS = 0b0010000000;
    /**
     * Convert domain name to IDNA ASCII form
     *
     * @var int 256
     */
    public const FLAG_CONVERT_HOST_TO_IDNA_ASCII = 0b0100000000;
    /**
     * Convert domain name from IDNA ASCII to Unicode
     *
     * @var int 512
     */
    public const FLAG_CONVERT_HOST_TO_UNICODE = 0b1000000000;
    /**
     * Normalization of the uri with all additional parameters
     *
     * @var int 1023
     */
    public const FLAG_FULL_NORMALIZATION = 0b1111111111;

    private UriHandler $uriHandler;
    private Chain      $chainTrimThenLower;

    /**
     * UriNormalization constructor.
     *
     * @param string|\Estasi\Utility\Interfaces\Uri $uriHandler
     * @param int                                   $changeSemantics
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function __construct($uriHandler = self::DEFAULT_URI_HANDLER, int $changeSemantics = 0)
    {
        $this->uriHandler         = UriFactory::make(UriFactory::WITHOUT_URI, $uriHandler);
        $this->chainTrimThenLower = new Chain(Chain::DEFAULT_PLUGIN_MANAGER, 'trim', 'strtolower');
        $this->setFlags($changeSemantics);
    }

    /**
     * @param string|\Estasi\Utility\Interfaces\Uri|mixed $value
     *
     * @return \Estasi\Utility\Interfaces\Uri|mixed
     */
    public function filter($value)
    {
        if (false === (is_string($value) || $value instanceof UriHandler)) {
            return $value;
        }

        $uri = $this->uriHandler->merge($value);
        // normalize scheme
        $scheme = $uri->scheme ? $this->normalizeScheme($uri->scheme) : null;
        // normalize host
        $host = $uri->host ? $this->normalizeHost($uri->host) : null;
        // normalize port
        $port = $uri->port ? $this->normalizePort($uri->port, $scheme) : null;
        // normalize path
        $path = $this->normalizePath($uri->path, $host);
        // normalize query
        $query = $uri->query ? $this->normalizeQuery($uri->query) : null;
        // normalize fragment
        $fragment = $uri->fragment ? $this->normalizeFragment($uri->fragment) : null;

        return $uri->withAll(compact('scheme', 'host', 'port', 'path', 'query', 'fragment'));
    }

    private function normalizeScheme(string $scheme): ?string
    {
        $scheme = $this->chainTrimThenLower->filter($scheme);

        return $this->isValidScheme($scheme) ? $scheme : null;
    }

    private function normalizeHost(string $host): ?string
    {
        // IP-literal
        if ($this->isHostIPLiteral($host)) {
            return $this->normalizeIPLiteral($host) ?: null;
        }
        // IPv4address
        if ($this->isHostIPv4Address($host)) {
            if ($this->replaceIpWithDomainName($host)) {
                $host = $this->removeOrAddWWW($host);
                $host = $this->convertIDNA($host);
            }

            return $host ?: null;
        }
        // reg-name
        if ($this->isHostRegName($host)) {
            $host = $this->chainTrimThenLower->filter($host);
            $host = $this->decodeUnreservedChars($host);
            $host = $this->encodeChars($host);
            $host = $this->removeOrAddWWW($host);
            $host = $this->convertIDNA($host);

            return $host ?: null;
        }

        return null;
    }

    private function normalizePort(string $port, ?string $scheme): ?string
    {
        $isDefaultPortOfTheScheme = fn(int $port): bool => $port === UriHandler::SCHEME_DEFAULTS_PORTS[$scheme];

        if (is_null($scheme)
            || $isDefaultPortOfTheScheme((int)$port)
            || $this->isPortOutsideOfTheAllowedRange((int)$port)) {
            return null;
        }

        return $port;
    }

    private function normalizePath(?string $path, ?string $host): ?string
    {
        if (is_null($path)) {
            goto _return_;
        }
        $path = $this->removeDotSegment($path);
        $path = $this->decodeUnreservedChars($path);
        $path = $this->encodeChars($path, '\x3A\x40\x2F');
        // additional path normalization
        $additional = [];
        if ($this->is(self::FLAG_ADD_TRAILING_SLASH)) {
            $additional['`(?<!\x2F)$`'] = '/';
        }
        if ($this->is(self::FLAG_REMOVING_DUPLICATE_SLASHES)) {
            $additional['`\x2F+`'] = '/';
        }
        if ($this->is(self::FLAG_REMOVING_DIRECTORY_INDEX)) {
            $additional['`(?:index|default)\x2E(?:[[:alnum:]]+)$`i'] = '';
        }
        if (boolval($additional)) {
            $path = preg_replace_callback_array($additional, $path);
        }

        _return_:

        return $path ?: (boolval($host) ? '/' : null);
    }

    private function normalizeQuery(string $query): ?string
    {
        $query = $this->decodeUnreservedChars(trim($query));
        $query = $this->encodeChars($query, '\x3A\x40\x2F\x3F');
        if ($this->is(self::FLAG_SORTING_THE_QUERY_PARAMETERS)) {
            $query = $this->parseStr($query);
            $query->ksort();
            $query = $query->map(fn($key, $val): string => sprintf("%s=%s", $key, $val))
                           ->values()
                           ->join('&');
        }

        return $query ?: null;
    }

    private function normalizeFragment(string $fragment): ?string
    {
        if ($this->is(self::FLAG_REMOVING_THE_FRAGMENT)) {
            return null;
        }
        $fragment = $this->decodeUnreservedChars(trim($fragment));
        $fragment = $this->encodeChars($fragment, '\x3A\x40\x2F\x3F');

        return $fragment ?: null;
    }

    private function normalizeIPLiteral(string $host): ?string
    {
        if ($this->isIPvFuture($host)) {
            goto _return_host_as_ip_;
        }

        if ($this->isIPv6Address($host, $matches)) {
            $host = empty($matches[1]) ? $host : $this->compressZerosIPv6Address($matches[0]);
            if ($this->replaceIpWithDomainName($host)) {
                $host = $this->removeOrAddWWW($host);

                return $this->convertIDNA($host);
            }
            goto _return_host_as_ip_;
        }

        return null;

        _return_host_as_ip_:

        return sprintf('[%s]', $host);
    }

    private function compressZerosIPv6Address(string $ip): string
    {
        if (!preg_match('`(?<=^|\x3A)0{1,4}(?=\x3A|$)`', $ip)) {
            return $ip;
        }
        // dividing the ip string into blocks
        $ip = preg_split('`\x3A`', $ip, -1, PREG_SPLIT_NO_EMPTY);
        // deleting consecutive blocks equal to 0
        $start = 0;
        $end   = $count = count($ip);
        $done  = false;
        foreach ($ip as $key => $segment) {
            if (preg_match('`^0+$`S', $segment)) {
                $start = $done ? $start : $key;
                $done  = true;
                continue;
            }
            if ($done) {
                $end = $key;
                break;
            }
        }
        $empty  = 0 === $start || $count === $end ? ['', ''] : [''];
        $output = array_merge(array_slice($ip, 0, $start), $empty, array_slice($ip, $end));

        return implode(':', $output);
    }

    private function replaceIpWithDomainName(string &$ipAddress): bool
    {
        if ($this->is(self::FLAG_REPLACING_IP_WITH_DOMAIN_NAME)) {
            $host = gethostbyaddr($ipAddress) ?: $ipAddress;
            if (strcasecmp($ipAddress, $host)) {
                $ipAddress = $host;

                return true;
            }
        }

        return false;
    }

    private function removeOrAddWWW(string $host): string
    {
        if ($this->is(self::FLAG_REMOVING_WWW)) {
            $host = preg_replace('`^w{3}\x2E`', '', $host, -1, $count);
            // if the www was deleted, the www is not added
            if ($count) {
                return $host;
            }
        }
        if ($this->is(self::FLAG_ADDING_WWW) && substr_count($host, '.') < 2) {
            $host = sprintf("www.%s", $host);
        }

        return $host;
    }

    private function convertIDNA(string $host): string
    {
        if ($this->is(self::FLAG_CONVERT_HOST_TO_IDNA_ASCII)) {
            idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46, $info);
            if (strcasecmp($host, $info['result'])) {
                return $info['result'];
            }
        }
        if ($this->is(self::FLAG_CONVERT_HOST_TO_UNICODE)) {
            idn_to_utf8($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46, $info);
            if (strcasecmp($host, $info['result'])) {
                return $info['result'];
            }
        }

        return $host;
    }

    private function decodeUnreservedChars(string $string): string
    {
        $unreserved = sprintf('`[%s]`', UriHandler::UNRESERVED_RFC3986);
        $uppercase  = new Uppercase();
        $decoded    = function (array $match) use ($unreserved, $uppercase) {
            $char = rawurldecode($match[0]);

            return preg_match($unreserved, $char) ? $char : $uppercase($match[0]);
        };

        return preg_replace_callback(sprintf('`%s`uS', UriHandler::PCT_ENCODED_RFC3986), $decoded, $string);
    }

    private function encodeChars(string $string, string $extAllowedChars = ''): string
    {
        $patternCharsNotAllowedForEncoding = sprintf(
            '`(?:[^%s%s\x25%s]+|\x25(?![A-Fa-f0-9]{2}))`u',
            UriHandler::UNRESERVED_RFC3986,
            UriHandler::SUB_DELIMS_RFC3986,
            $extAllowedChars
        );

        return preg_replace_callback(
            $patternCharsNotAllowedForEncoding,
            fn(array $matches): string => rawurlencode($matches[0]),
            $string
        );
    }
}
