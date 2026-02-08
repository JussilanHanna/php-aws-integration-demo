<?php
declare(strict_types=1);

use App\Controllers\BookingController;
use App\Support\Router;

return function (Router $router): void {
    $c = new BookingController();

    $router->get('/health', fn() => $c->health());
    $router->get('/bookings', fn() => $c->listBookings());
    $router->post('/bookings', fn() => $c->createBooking());
    $router->post('/bookings/upload', fn() => $c->uploadFile());
};
