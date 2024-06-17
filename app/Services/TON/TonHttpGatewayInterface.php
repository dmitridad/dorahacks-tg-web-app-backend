<?php

namespace App\Services\TON;

use Olifanton\Ton\Transport;
use Olifanton\Ton\Transports\Toncenter\ToncenterV2Client;

interface TonHttpGatewayInterface
{
    public function getClient(): ToncenterV2Client;

    public function getTransport(): Transport;
}
