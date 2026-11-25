<?php

it('renders the public landing page', function () {
    $this->get('/')->assertOk();
});

it('redirects guests away from the admin dashboard', function () {
    $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
});
