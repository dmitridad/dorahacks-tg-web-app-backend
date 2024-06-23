<?php

namespace App\Traits;

trait RequestRetry
{
    /**
     * @throws \Exception
     */
    protected function sendRequestWithRetry(callable $request, callable $shouldRetry, int $maxRetries = 3)
    {
        $retries = 0;
        $response = null;
        $retryDelay = 100; // initial retry delay in milliseconds (0.1 seconds)

        while ($retries < $maxRetries) {
            try {
                $response = $request();

                if ($shouldRetry($response)) {
                    throw new \Exception("Retrying due to response content");
                }

                return $response;
            } catch (\Exception $e) {
                $retries++;
                if ($retries >= $maxRetries) {
                    throw new \Exception("Maximum retries reached", 0, $e);
                }

                usleep($retryDelay * 1000); // convert to microseconds

                // increase retry delay exponentially
                $retryDelay *= 2;
            }
        }

        return $response;
    }
}
