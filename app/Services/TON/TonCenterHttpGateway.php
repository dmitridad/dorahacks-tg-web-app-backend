<?php

namespace App\Services\TON;

use Http\Client\Common\HttpMethodsClientInterface;
use Olifanton\Ton\Transport;
use Olifanton\Ton\Transports\Toncenter\ClientOptions;
use Olifanton\Ton\Transports\Toncenter\ToncenterHttpV2Client;
use Olifanton\Ton\Transports\Toncenter\ToncenterTransport;
use Olifanton\Ton\Transports\Toncenter\ToncenterV2Client;

class TonCenterHttpGateway implements TonHttpGatewayInterface
{
    protected ToncenterV2Client $client;
    protected Transport $transport;

    public function __construct(HttpMethodsClientInterface $client, string $tonCenterApiKey, bool $isMainNet)
    {
        // Toncenter API client initialization
        $this->client = new ToncenterHttpV2Client(
            $client,
            new ClientOptions(
                $isMainNet ? "https://toncenter.com/api/v2" : "https://testnet.toncenter.com/api/v2",
                $tonCenterApiKey,
            )
        );

        // Transport initialization
        $this->transport = new ToncenterTransport($this->client);
    }

    public function getClient(): ToncenterV2Client
    {
        return $this->client;
    }

    public function getTransport(): Transport
    {
        return $this->transport;
    }
}
