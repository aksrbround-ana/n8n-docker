<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

class MinimaxEFaktura extends MinimaxResource
{
    protected function getResourceName(): string
    {
        return 'eFaktura';
    }

    /**
     * Отправить счёт в государственную систему eFaktura.
     *
     * POST {organisationId}/eFaktura/{id}/send
     *
     * @throws MinimaxApiException
     */
    public function sendToSystem(int|string $invoiceId): array
    {
        return $this->getClient()->post(
            $this->buildPath($invoiceId, 'send')
        );
    }

    /**
     * Проверить статус счёта в системе eFaktura.
     *
     * GET {organisationId}/eFaktura/{id}/status
     *
     * @throws MinimaxApiException
     */
    public function status(int|string $invoiceId): array
    {
        return $this->getClient()->get(
            $this->buildPath($invoiceId, 'status')
        );
    }
}
