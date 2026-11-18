<?php

test("application health endpoint returns ok status", function () {
    $response = $this->get("/health");
    $response->assertStatus(200);
});

test("landing page loads without server error", function () {
    $response = $this->get("/");
    expect($response->status())->toBeLessThan(500);
});
