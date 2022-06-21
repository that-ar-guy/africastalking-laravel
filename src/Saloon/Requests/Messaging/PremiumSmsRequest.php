<?php

namespace SamuelMwangiW\Africastalking\Saloon\Requests\Messaging;

use Sammyjo20\Saloon\Traits\Plugins\HasFormParams;
use SamuelMwangiW\Africastalking\Enum\Service;
use SamuelMwangiW\Africastalking\Saloon\Requests\BaseRequest;

class PremiumSmsRequest extends BaseRequest
{
    use HasFormParams;

    public Service $service = Service::CONTENT;

    public function __construct(private readonly array $data)
    {
    }

    public function defineEndpoint(): string
    {
        return 'messaging';
    }

    public function defaultData(): array
    {
        return array_merge(
            $this->data,
            ['username' => $this->username()]
        );
    }
}
