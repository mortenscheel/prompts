<?php

use Laravel\Prompts\Key;
use function Laravel\Prompts\password;
use Laravel\Prompts\PasswordPrompt;
use Laravel\Prompts\Prompt;

it('returns the input', function () {
    Prompt::fake(['p', 'a', 's', 's', Key::ENTER]);

    $result = password(label: 'What is the password?');

    expect($result)->toBe('pass');
});

it('validates', function () {
    Prompt::fake(['p', 'a', 's', Key::ENTER, 's', Key::ENTER])
        ->shouldReceive('write')
        ->with(Mockery::on(fn ($value) => str_contains($value, 'Password must be at least 4 characters.')));

    $result = password(
        label: 'What is the password',
        validate: fn ($value) => strlen($value) < 4 ? 'Invalid name.' : '',
    );

    expect($result)->toBe('pass');
});

it('cancels', function () {
    Prompt::fake([Key::CTRL_C])
        ->expects('write')
        ->with(Mockery::on(fn ($value) => str_contains($value, 'Cancelled.')));

    password(label: 'What is the password');
});

test('the backspace key removes a character', function () {
    Prompt::fake(['p', 'a', 'z', Key::BACKSPACE, 's', 's', Key::ENTER]);

    $result = password(label: 'What is the password?');

    expect($result)->toBe('pass');
});

test('the delete key removes a character', function () {
    Prompt::fake(['p', 'a', 'z', Key::LEFT, Key::DELETE, 's', 's', Key::ENTER]);

    $result = password(label: 'What is the password?');

    expect($result)->toBe('pass');
});

it('can fall back', function () {
    Prompt::fallbackWhen(true);

    PasswordPrompt::fallbackUsing(function (PasswordPrompt $prompt) {
        expect($prompt->label)->toBe('What is the password?');

        return 'result';
    });

    $result = password('What is the password?');

    expect($result)->toBe('result');
});
