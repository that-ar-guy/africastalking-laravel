<?php

namespace SamuelMwangiW\Africastalking\Domain;

use SamuelMwangiW\Africastalking\Enum\Currency;
use SamuelMwangiW\Africastalking\Transporter\Requests\Payment\MobileCheckoutRequest;
use SamuelMwangiW\Africastalking\ValueObjects\PhoneNumber;

class MobileCheckout
{
    private PhoneNumber $phone;
    private int $amount;
    private array $metadata = [];
    private Currency $currency = Currency::KENYA;
    private string $productName;

    public function to(string|PhoneNumber $phone): static
    {
        $this->phone = is_string($phone) ? PhoneNumber::make($phone) : $phone;

        return $this;
    }

    public function amount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function metadata(array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function currency(string|Currency $currency): static
    {
        $this->currency = is_string($currency) ? Currency::from($currency) : $currency;

        return $this;
    }

    public function product(string $product): static
    {
        $this->productName = $product;

        return $this;
    }

    public function send(): array
    {
        /** @phpstan-ignore-next-line  */
        $response = MobileCheckoutRequest::build()
            ->asJson()
            ->withData($this->data())
            ->fetch();

        return $response;
    }

    private function data(): array
    {
        return [
            "productName" => $this->getProductName(),
            "phoneNumber" => $this->phone->number,
            "currencyCode" => $this->currency->value,
            "amount" => $this->amount,
            "metadata" => (object)$this->getMetadata(),
        ];
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getProductName(): string
    {
        return $this->productName ?? config('africastalking.payment.product-name');
    }
}
