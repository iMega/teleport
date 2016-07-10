<?php

namespace iMega\Teleport\Service;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use iMega\Teleport\Service\Exception;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class CurlService
{
    protected $client;
    protected $logger;

    public function __construct($options = [], LoggerInterface $logger = null, GuzzleClient $client = null)
    {
        $options = array_replace(array(
            'base_uri' => 'http://127.0.0.1:80',
            'http_errors' => false
        ), $options);

        $this->client = $client ?: new GuzzleClient($options);
        $this->logger = $logger ?: new NullLogger();
    }

    public function download($url = null, array $options = [])
    {
        return $this->send($this->buildRequest('GET', $url, $options));
    }

    private function send(RequestInterface $request)
    {
        $this->logger->info(sprintf('%s "%s"', $request->getMethod(), $request->getUri()));
        $this->logger->debug(sprintf(
            "Request:\n%s\n%s\n%s",
            $request->getUri(),
            $request->getMethod(),
            json_encode($request->getHeaders()))
        );

        try {
            $response = $this->client->send($request);
        } catch (TransferException $e) {
            $message = sprintf('Something went wrong when calling storage (%s).', $e->getMessage());
            $this->logger->error($message);

            throw new Exception\ServerException($message);
        }

        $this->logger->debug(sprintf(
            "Response:\n%s\n%s\n%s",
            $response->getStatusCode(),
            json_encode($response->getHeaders()),
            $response->getBody()->getContents())
        );

        if (400 <= $response->getStatusCode()) {
            $message = sprintf(
                'Something went wrong when calling storage statusCode=[%s] reasonPhrase=[%s] uri=[%s]).',
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $request->getUri()
            );
            $this->logger->error($message);

            $message .= "\n" . $response->getBody()->__toString();
            if (500 <= $response->getStatusCode()) {
                throw new Exception\ServerException($message);
            }

            throw new Exception\ClientException($message);
        }

        return $response;
    }

    /**
     * @param $method
     * @param $url
     * @param array $options
     * @return RequestInterface
     */
    private function buildRequest($method, $url, array $options = [])
    {
        $uri = new Uri($url);
        $request = new Request($method, $uri);
        foreach ($options as $key => $optValue) {
            switch ($key) {
                case 'query':
                    if (is_array($optValue)) {
                        $optValue = \GuzzleHttp\Psr7\build_query($optValue);
                    }
                    $uri = $uri->withQuery($optValue);
                    $request = $request->withUri($uri);
                    break;
                case 'headers':
                    foreach ($optValue as $headerName => $headerValue) {
                        $request = $request->withHeader($headerName, $headerValue);
                    }
                    break;
                case 'body':
                    $request = $request->withBody(\GuzzleHttp\Psr7\stream_for($optValue));
                    break;
            }
        }
        return $request;
    }
}
