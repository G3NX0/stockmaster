<?php

it('shows landing page for guests', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('StockMaster');
});
