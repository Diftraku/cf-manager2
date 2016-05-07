<?php
// Routes

use CFM2\Utilities\FormatResponse;
use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->group('/ticket', function () {
    $this->get('', '\CFM2\Controllers\Ticket:getTickets');
    $this->get('/{id:[0-9]+}', '\CFM2\Controllers\Ticket:getTicket');
    $this->get('/{id:[0-9]+}.pdf', '\CFM2\Controllers\Ticket:getTicketPDF');
    $this->get('/{id:[0-9]+}.qr', '\CFM2\Controllers\Ticket:getTicketQR');
    $this->put('/{id:[0-9]+}', '\CFM2\Controllers\Ticket:updateTicket');
    $this->post('', '\CFM2\Controllers\Ticket:createTicket');
});
$app->group('/user', function () {
    $this->get('', '\CFM2\Controllers\User:getUsers');
    $this->get('/{id:[0-9]+}', '\CFM2\Controllers\User:getUser');
    $this->put('/{id:[0-9]+}', '\CFM2\Controllers\User:updateUser');
    $this->post('', '\CFM2\Controllers\User:createUser');
});

/*$app->group('/event', function () {
    $this->get('', '\CFM2\Controllers\Event:getEvents');
    $this->get('/{id:[0-9]+}', '\CFM2\Controllers\Event:getEvent');
    $this->put('/{id:[0-9]+}', '\CFM2\Controllers\Event:updateEvent');
    $this->post('', '\CFM2\Controllers\Event:createEvent');
});*/
$app->any('/event', function (Request $request, Response $response, $args = []) {
    $response->withStatus(501)->withJson(new FormatResponse([], 501, 'Not implemented'));
});
