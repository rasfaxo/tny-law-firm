<?php

namespace Tests\Feature;

use Tests\TestCase;

class ValidationLocalizationTest extends TestCase
{
    public function test_every_validation_message_used_by_the_application_has_an_indonesian_translation(): void
    {
        $keys = [
            'validation.accepted',
            'validation.after',
            'validation.after_or_equal',
            'validation.confirmed',
            'validation.current_password',
            'validation.date',
            'validation.date_format',
            'validation.email',
            'validation.exists',
            'validation.file',
            'validation.image',
            'validation.in',
            'validation.integer',
            'validation.lowercase',
            'validation.max.string',
            'validation.mimes',
            'validation.mimetypes',
            'validation.password.mixed',
            'validation.required',
            'validation.string',
            'validation.unique',
            'validation.uploaded',
            'validation.url',
            'auth.failed',
            'auth.throttle',
            'passwords.token',
        ];

        foreach ($keys as $key) {
            $message = __($key);

            $this->assertNotSame($key, $message);
            $this->assertStringNotContainsString('validation.', $message);
        }
    }
}
