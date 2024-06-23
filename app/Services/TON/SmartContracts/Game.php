<?php

namespace App\Services\TON\SmartContracts;

use App\Services\TON\TonHttpGatewayInterface;
use App\Traits\RequestRetry;
use Olifanton\Interop\Address;
use Olifanton\Ton\Exceptions\TransportException;

class Game implements GameInterface
{
    use RequestRetry;

    protected Address $address;
    protected TonHttpGatewayInterface $tonHttpGateway;

    public function __construct(string $address, TonHttpGatewayInterface $tonHttpGateway)
    {
        $this->address = new Address($address);
        $this->tonHttpGateway = $tonHttpGateway;
    }

    /**
     * @throws TransportException
     * @throws \Exception
     */
    public function getLastNumber(): int
    {
        $response = $this->sendRequestWithRetry(
            function() {
                return $this->tonHttpGateway
                    ->getTransport()
                    ->runGetMethod($this->address, 'lastNumber');
            },
            function($response) {
                return $response->currentBigInteger()->toInt() === -1;
            },
            8
        );

        return $response->currentBigInteger()->toInt();
    }
}
