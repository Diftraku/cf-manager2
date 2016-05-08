<?php
/**
 * cf-manager2
 * @package cf-manager2
 * @copyright Copyright (c) 2016, Diftraku
 * @author diftraku
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CFM2\Controllers;

use PHPUnit_Framework_TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use RedBeanPHP\RedException\SQL as RedExceptionSQL;
use \R;

class TicketTest extends PHPUnit_Framework_TestCase
{
    private $sample_data = [
        ['first_name'=>'First','last_name'=>'Test','email'=>'example@example.com','type'=>1,'status'=>1,'event_id'=>1],
        ['first_name'=>'Second','last_name'=>'Test','email'=>'example@example.com','type'=>1,'status'=>1,'event_id'=>1],
        ['first_name'=>'Third','last_name'=>'Test','email'=>'example@example.com','type'=>1,'status'=>1,'event_id'=>1]
    ];
    protected function setUp() {
        // Note: This is only available in the mock config
        // This MAY NOT WORK
        unlink(CF_DB_FILENAME);
        foreach ($this->sample_data as $data) {
            $ticket = R::dispense('ticket');
            $ticket->import($data);
            R::store($ticket);
        }
    }

    public function testListingTickets()
    {
        global $app;
        // instantiate controller
        $action = new Ticket($app->getContainer());

        // We need a request and response object to invoke the action
        $environment = Environment::mock([
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/ticket'
        ]);
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // run the controller action and test it
        $response = $action->getTickets($request, $response, []);
        //var_dump($response->getBody());
        //$this->assertSame((string)$response->getBody(), '{"foo":"bar"}');
    }

    public function tearDown()
    {
        R::close();
        unlink(CF_DB_FILENAME);
    }
}
