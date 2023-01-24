<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pest\Expectation;
use SamuelMwangiW\Africastalking\Domain\Airtime;
use SamuelMwangiW\Africastalking\Enum\Currency;
use SamuelMwangiW\Africastalking\Exceptions\AfricastalkingException;
use SamuelMwangiW\Africastalking\Facades\Africastalking;
use SamuelMwangiW\Africastalking\ValueObjects\AirtimeRecipientResponse;
use SamuelMwangiW\Africastalking\ValueObjects\AirtimeResponse;
use SamuelMwangiW\Africastalking\ValueObjects\AirtimeTransaction;
use SamuelMwangiW\Africastalking\ValueObjects\PhoneNumber;

it('resolves the application class')
    ->expect(fn () => Africastalking::airtime())
    ->toBeInstanceOf(Airtime::class);

it('can add a recipient', function (string $phone, string $currency, int $amount): void {
    $service = Africastalking::airtime()
        ->to(
            phoneNumber: $phone,
            currencyCode: $currency,
            amount: $amount
        );

    expect($service)
        ->recipients->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->recipients->first()->phoneNumber->toBeInstanceOf(PhoneNumber::class)
        ->recipients->first()->currencyCode->toBeInstanceOf(Currency::class);
})->with('phone-numbers', 'currencies', 'airtime-amount');

it('can add a recipient from a transaction object', function (AirtimeTransaction $transaction): void {
    $service = Africastalking::airtime()->to($transaction);

    expect($service)
        ->recipients->toHaveCount(1)
        ->recipients->each(
            fn ($recipient) => $recipient
                ->phoneNumber->toBe($transaction->phoneNumber)
                ->currencyCode->toBe($transaction->currencyCode)
                ->amount->toBeInt()
        );
})->with('airtime-transactions');

it('can add multiple recipients', function (string $phone, string $currency, int $amount): void {
    $service = Africastalking::airtime()
        ->add(
            phoneNumber: $phone,
            currencyCode: $currency,
            amount: $amount,
        )
        ->add(
            phoneNumber: '+256706123456',
            currencyCode: $currency,
            amount: $amount
        );

    expect($service)
        ->recipients->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->recipients->each(
            fn ($recipient) => $recipient
                ->phoneNumber->toBeInstanceOf(PhoneNumber::class)
                ->currencyCode->toBeInstanceOf(Currency::class)
                ->amount->toBeInt()
        );
})->with('phone-numbers', 'currencies', 'airtime-amount');

it('throws an exception for invalid currency', function (string $phone, int $amount): void {
    Africastalking::airtime()
        ->to(
            phoneNumber: $phone,
            currencyCode: 'KPW',
            amount: $amount
        );
})->with('phone-numbers', 'airtime-amount')->throws(AfricastalkingException::class);

it('throws an exception for amounts less than 5', function (string $phone): void {
    Africastalking::airtime()
        ->to(
            phoneNumber: $phone,
            amount: 1
        );
})->with('phone-numbers')->throws(AfricastalkingException::class);

it('sends airtime to a single recipient', function (AirtimeTransaction $transaction): void {
    $result = Africastalking::airtime()
        ->idempotent(fake()->uuid())
        ->to($transaction)
        ->send();

    if ($result->hasDuplicate()) {
        $this->markAsRisky();

        return;
    }

    expect($result)
        ->toBeInstanceOf(AirtimeResponse::class)
        ->numSent->toBe(1)
        ->errorMessage->toBe('None')
        ->and($result->responses)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->toBeInstanceOf(AirtimeRecipientResponse::class);
})->with('airtime-transactions');

it('sends airtime to multiple recipients', function (int $amount, string $phone): void {
    $secondPhone = Str::of('+254712345678')
        ->replace('8', (string)random_int(0, 9))
        ->value();

    $result = Africastalking::airtime()
        ->idempotent(fake()->uuid())
        ->to($phone, 'KES', $amount)
        ->to(phoneNumber: $secondPhone, amount: $amount)
        ->send();

    if ($result->hasDuplicate()) {
        $this->markAsRisky();

        return;
    }

    expect($result)
        ->toBeInstanceOf(AirtimeResponse::class)
        ->numSent->toBe(2)
        ->errorMessage->toBe('None')
        ->and($result->responses)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->each(
            fn (Expectation $transaction) => $transaction->toBeInstanceOf(AirtimeRecipientResponse::class)
        );
})->with('airtime-amount', 'phone-numbers')->markAsRisky();
