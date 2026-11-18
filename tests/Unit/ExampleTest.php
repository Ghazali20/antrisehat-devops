<?php

test('that true is true', function () {
    expect(true)->toBeTrue();
});

test('qa environment and application configuration check', function () {
    expect(PHP_VERSION_ID)->toBeGreaterThanOrEqual(80200);
    expect(file_exists(__DIR__ . '/../../composer.json'))->toBeTrue();
    expect(file_exists(__DIR__ . '/../../Dockerfile'))->toBeTrue();
    expect(file_exists(__DIR__ . '/../../compose.yaml'))->toBeTrue();
});
