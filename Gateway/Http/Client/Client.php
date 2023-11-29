<?php

namespace Mike\PaymentProcessor\Gateway\Http\Client;

use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\Framework\HTTP\LaminasClient;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Laminas\Http\Request;
use Mike\Core\Util\TypeCast;

class Client implements ClientInterface
{
    /**
     * @var LaminasClientFactory
     */
    private $clientFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array<string>
     */
    private $sensitiveData = [
        'ccnumber',
        'ccexp',
        'cvv',
        'security_key'
    ];

    /**
     * @param LaminasClientFactory $clientFactory
     * @param Logger $logger
     */
    public function __construct(
        LaminasClientFactory $clientFactory,
        Logger $logger
    ) {
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array<string, mixed>|array<mixed>
     * @throws ClientException
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        $body = $transferObject->getBody();
        foreach ($this->sensitiveData as $value) {
            if (isset($body[$value])) {
                unset($body[$value]);
            }
        }

        $log = [
            'request' => $body,
            'request_uri' => $transferObject->getUri()
        ];
        $result = [];

        /** @var LaminasClient $client */
        $client = $this->clientFactory->create();
        $client->setOptions($transferObject->getClientConfig());
        $client->setMethod((string) $transferObject->getMethod());

        switch ($transferObject->getMethod()) {
            case Request::METHOD_GET:
                $client->setParameterGet(TypeCast::asArr($transferObject->getBody()));
                break;
            case Request::METHOD_POST:
                $client->setParameterPost(TypeCast::asArr($transferObject->getBody()));
                break;
            default:
                throw new \LogicException(
                    sprintf(
                        'Unsupported HTTP method %s',
                        $transferObject->getMethod()
                    )
                );
        }
        $client->setHeaders($transferObject->getHeaders());
        $client->setUrlEncodeBody($transferObject->shouldEncode());
        $client->setUri($transferObject->getUri());
        try {
            $response = $client->send();
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            parse_str($response->getBody(), $result);
            $log['response'] = $result;
        } catch (ClientException $e) {
            throw new \Magento\Payment\Gateway\Http\ClientException(
                __($e->getMessage())
            );
        } finally {
            $this->logger->debug($log);
        }

        return $result;
    }
}
