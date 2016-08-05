<?php
/**
 * Copyright (C) 2014 iMega ltd Dmitry Gavriloff (email: info@imega.ru),
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace iMega\Teleport\Cloud;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use iMega\Teleport\Service\Exception;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ApiCloud
{
    protected $client;

    public function __construct(array $options = [], LoggerInterface $logger = null, ClientInterface $client = null)
    {
        $options = array_replace(array(
            'base_uri' => 'http://127.0.0.1:80',
            'http_errors' => false
        ), $options);

        $this->client = $client ?: new GuzzleClient($options);
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Регистрация плагина
     *
     * @param string $login Идентификатор учетной записи
     * @param string $url   Адрес сайта
     */
    public function registered($login, $url)
    {
        $data = [
            'url' => $url,
        ];
        //$response = $this->send($this->buildRequest('POST', '/activate/register-plugin' . $login), ['json' => $data]);
        //$response = $this->client->post('/activate/register-plugin/' . $login, ['json' => $data]);
        //var_dump($response->getBody()->__toString());
    }

    /**
     * Загрузить файл
     *
     * @param null|string $url
     * @param array       $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function download($url = null, array $options = [])
    {
        return $this->send($this->buildRequest('GET', $url, $options), $options);
    }

    /**
     * Загрузка файла завершена
     *
     * @param array $data
     */
    public function downloadFileComplete(array $data = [])
    {
        $this->send($this->buildRequest('POST', '/storage/status-file'), ['json' => $data]);
    }

    /**
     * Отправить статус о завершении загрузок
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function downloadComplete()
    {
        return $this->send($this->buildRequest('POST', '/storage/download-complete'));
    }

    /**
     * Отправить статус о завершении импорта
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function importComplete()
    {
        return $this->send($this->buildRequest('POST', '/'));
    }

    /**
     * Отправить журнал
     *
     * @param array $log
     */
    public function reportLog(array $log = [])
    {
        $this->send($this->buildRequest('POST', '/'), ['json' => $log]);
    }

    /**
     * Отправить настройки
     *
     * @param array $data
     */
    public function settings(array $data = [])
    {
        $this->send($this->buildRequest('POST', '/'), ['json' => $data]);
    }

    private function send(RequestInterface $request, array $options = [])
    {
        $this->logger->info(sprintf('%s "%s"', $request->getMethod(), $request->getUri()));
        $this->logger->debug(sprintf(
                "Request:\n%s\n%s\n%s",
                $request->getUri(),
                $request->getMethod(),
                json_encode($request->getHeaders()))
        );

        try {
            $response = $this->client->send($request, $options);
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
        $uri     = new Uri($url);
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
