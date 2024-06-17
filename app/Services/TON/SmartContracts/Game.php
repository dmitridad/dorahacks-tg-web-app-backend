<?php

namespace App\Services\TON\SmartContracts;

use App\Services\TON\TonHttpGatewayInterface;
use Olifanton\Interop\Address;
use Olifanton\Ton\Exceptions\TransportException;

class Game implements GameInterface
{
    protected Address $address;
    protected TonHttpGatewayInterface $tonHttpGateway;

    public function __construct(string $address, TonHttpGatewayInterface $tonHttpGateway)
    {
        $this->address = new Address($address);
        $this->tonHttpGateway = $tonHttpGateway;
    }

    /**
     * @throws TransportException
     */
    public function getLastNumber(): int
    {
        // TODO try/catch and retries
        $response = $this->tonHttpGateway
            ->getTransport()
            ->runGetMethod($this->address, 'lastNumber');

        $lastNumber = $response->currentBigInteger()->toInt();
        if (!$lastNumber) {
            // TODO error
        }

        return $lastNumber;
    }
}
