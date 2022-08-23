<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CreateUsers;

uses(RefreshDatabase::class, CreateUsers::class);

test('cannot register with short password', function () {
    $data = [
        'first_name' => 'firstname',
        'last_name' => 'lastname',
        'email' => 'email@laravel_repository.test',
        'phone' => '08123456789',
        'password' => '12345',
    ];

    $response = $this->postJson($this->baseUrl.'/auth/register', $data);
    $response->assertUnprocessable();
    $responseJson = json_decode($response->content());

    $this->assertEquals('The password must be at least 8 characters.', $responseJson->message);
    $this->assertEquals('The password must be at least 8 characters.', $responseJson->errors->password[0]);
});

test('cannot register if lastname and email address is empty', function () {
    $data = [
        'first_name' => 'firstname',
        'phone' => '08123456789',
        'password' => '1234567',
    ];

    $response = $this->postJson($this->baseUrl.'/auth/register', $data);
    $response->assertUnprocessable();
    $responseJson = json_decode($response->content());

    $this->assertEquals('The last name field is required. (and 2 more errors)', $responseJson->message);
    $this->assertEquals('The last name field is required.', $responseJson->errors->last_name[0]);
    $this->assertEquals('The email field is required.', $responseJson->errors->email[0]);
});

test('cannot register if email address or phone number exist', function () {
    $user = $this->createUser();

    $response = $this->postJson($this->baseUrl.'/auth/register', $user->getAttributes());
    $response->assertUnprocessable();
    $responseJson = json_decode($response->content());

    $this->assertEquals('The email has already been taken. (and 1 more error)', $responseJson->message);
    $this->assertEquals('The phone has already been taken.', $responseJson->errors->phone[0]);
    $this->assertEquals('The email has already been taken.', $responseJson->errors->email[0]);
});

test('can register new user', function () {
    $data = [
        'first_name' => 'firstname',
        'last_name' => 'lastname',
        'email' => 'email@laravel_repository.test',
        'phone' => '08123456789',
        'password' => 'email@laravel_repository.test',
    ];

    $response = $this->postJson($this->baseUrl.'/auth/register', $data);
    $response->assertCreated();
    $responseJson = json_decode($response->content());

    $this->assertEquals('success', $responseJson->status);
    $this->assertEquals('Registration successful.', $responseJson->message);
    $this->assertEquals($data['first_name'], $responseJson->data->first_name);
    $this->assertEquals($data['last_name'], $responseJson->data->last_name);
    $this->assertEquals($data['email'], $responseJson->data->email);
    $this->assertEquals($data['phone'], $responseJson->data->phone);
});
