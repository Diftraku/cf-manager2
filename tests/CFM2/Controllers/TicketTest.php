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
use Slim\Http\Cookies;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use RedBeanPHP\RedException\SQL as RedExceptionSQL;
use \R;

class TicketTest extends PHPUnit_Framework_TestCase
{
    private $sample_data = [
        ['first_name'=>'First','last_name'=>'Test','email'=>'example@example.com','type'=>1,'status'=>1,'event_id'=>1],
        ['first_name'=>'Second','last_name'=>'Test','email'=>'example@example.com','type'=>1,'status'=>1,'event_id'=>1],
        ['first_name'=>'Third','last_name'=>'Test','email'=>'example@example.com','type'=>1,'status'=>1,'event_id'=>1],
        ['first_name'=>'Fourth','last_name'=>'Test','email'=>'example@example.com','type'=>1,'status'=>1,'event_id'=>1]
    ];
    public function setUp() {
        echo 'Create test database and insert sample data' . "\n";
        // Note: This is only available in the mock config
        // This MAY NOT WORK
        if (file_exists(CF_DB_FILENAME)) {
            unlink(CF_DB_FILENAME);
        }

        require_once PROJECT_ROOT . '/tests/run.php';
        foreach ($this->sample_data as $data) {
            $ticket = R::dispense('ticket');
            $ticket->import($data);
            R::store($ticket);
        }
    }

    public function testGetTickets()
    {
        global $app;
        echo 'Testing getTickets:' . "\n";
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
        $data = (string)$response->getBody();
        $data = json_decode($data, true);

        $this->assertSame($data['status'], 'success', 'Status should be success');
        $this->assertSame($data['code'], 200, 'Code should be 200 (int)');
        $this->assertArrayHasKey('data', $data, 'JSON response should have `data`-key');
        $this->assertCount(2, $data['data'], 'Data should have 2 elements');
        $this->assertEquals(4, $data['data']['count'], 'Ticket count should be 4');

        $random = rand(0,3);
        echo '- entry #' . ($random + 1) . "\n";
        $data = $data['data']['tickets'][$random];
        $this->assertArrayHasKey('hash', $data, 'Ticket must have hash');
        $this->assertArrayHasKey('id', $data, 'Ticket must have id');
        $this->assertArrayHasKey('metadata', $data, 'Ticket must have metadata');
        $data = json_decode($data['metadata'], true);
        echo '- entry #' . ($random + 1) . ' for valid metadata' . "\n";
        $this->assertArrayHasKey('__version', $data, 'Metadata must have version');
        $this->assertGreaterThanOrEqual(1, $data['__version'], 'Version must be 1 or higher');
    }

    public function testGetTicket()
    {
        global $app;
        echo 'Testing getTicket:' . "\n";
        // instantiate controller
        $action = new Ticket($app->getContainer());

        $random = rand(1,4);
        echo '- entry #' . ($random) . "\n";

        // We need a request and response object to invoke the action
        $environment = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/ticket/' . $random
        ]);
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // run the controller action and test it
        $response = $action->getTicket($request, $response, ['id' => $random]);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertSame($data['status'], 'success', 'Status should be success');
        $this->assertSame($data['code'], 200, 'Code should be 200 (int)');
        $this->assertArrayHasKey('data', $data, 'JSON response should have `data`-key');
        $this->assertCount(13, $data['data'], 'Data should have 13 elements');

        $ticket = $data['data'];
        $this->assertArrayHasKey('id', $ticket, 'Ticket must have id');
        $this->assertArrayHasKey('first_name', $ticket, 'Ticket must have first_name');
        $this->assertArrayHasKey('last_name', $ticket, 'Ticket must have last_name');
        $this->assertArrayHasKey('email', $ticket, 'Ticket must have email');
        $this->assertArrayHasKey('status', $ticket, 'Ticket must have status');
        $this->assertArrayHasKey('type', $ticket, 'Ticket must have type');
        $this->assertArrayHasKey('event_id', $ticket, 'Ticket must have event_id');
        $this->assertArrayHasKey('hash', $ticket, 'Ticket must have hash');
        $this->assertArrayHasKey('created_on', $ticket, 'Ticket must have created_on');
        $this->assertArrayHasKey('created_by', $ticket, 'Ticket must have created_by');
        $this->assertArrayHasKey('modified_on', $ticket, 'Ticket must have modified_on');
        $this->assertArrayHasKey('modified_by', $ticket, 'Ticket must have modified_by');
        $this->assertArrayHasKey('metadata', $ticket, 'Ticket must have metadata');

        $metadata = json_decode($ticket['metadata'], true);
        $this->assertArrayHasKey('__version', $metadata, 'Metadata must have version');
        $this->assertGreaterThanOrEqual(1, $metadata['__version'], 'Version must be 1 or higher');
    }

    public function testCreateTicket()
    {
        global $app;
        echo 'Testing createTicket' . "\n";
        // instantiate controller
        $action = new Ticket($app->getContainer());

        $sample = ['first_name'=>'Fifth','last_name'=>'Test','email'=>'example@example.com','type'=>1,'status'=>1,'event_id'=>1];

        // We need a request and response object to invoke the action

        $_POST = $sample;
        $environment = Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/ticket',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded'
        ]);
        
        $request = Request::createFromEnvironment($environment);

        //$method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles
        $body = new RequestBody();
        $body->write(http_build_query($sample));
        $headers = Headers::createFromEnvironment($environment);
        $request = new Request(
            $request->getMethod(), $request->getUri(), $headers,
            Cookies::parseHeader($headers->get('Cookie', [])),
            $request->getServerParams(), $body, $request->getUploadedFiles()
        );
        
        $response = new Response();

        // run the controller action and test it
        $response = $action->createTicket($request, $response, []);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertSame($data['status'], 'success', 'Status should be success');
        $this->assertSame($data['code'], 200, 'Code should be 200 (int)');
        $this->assertArrayHasKey('data', $data, 'JSON response should have `data`-key');
        $this->assertCount(1, $data['data'], 'Data should have 1 element');

        $this->assertArrayHasKey('id', $data['data'], 'Data must have id');
        $this->assertEquals(5, $data['data']['id'], 'ID must be 5');

        $environment = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/ticket'
        ]);
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // run the controller action and test it
        $response = $action->getTickets($request, $response, []);

        $data = json_decode((string)$response->getBody(), true);

        $this->assertSame($data['status'], 'success', 'Status should be success');
        $this->assertSame($data['code'], 200, 'Code should be 200 (int)');
        $this->assertArrayHasKey('data', $data, 'JSON response should have `data`-key');
        $this->assertCount(2, $data['data'], 'Data should have 2 elements');
        $this->assertEquals(5, $data['data']['count'], 'Ticket count should be 5');
    }

    public function testUpdateTicket()
    {
        global $app;
        echo 'Testing updateTicket' . "\n";
        $random = rand(1,4);
        echo '- entry #' . ($random) . "\n";

        // instantiate controller
        $action = new Ticket($app->getContainer());

        $sample = ['first_name'=>'Updated','last_name'=>'Name','status'=>2];

        // We need a request and response object to invoke the action

        //$_POST = $sample;
        $environment = Environment::mock([
            'REQUEST_METHOD' => 'PUT',
            'REQUEST_URI' => '/ticket/' . $random,
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded'
        ]);

        $request = Request::createFromEnvironment($environment);

        //$method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles
        $body = new RequestBody();
        $body->write(http_build_query($sample));
        $headers = Headers::createFromEnvironment($environment);
        $request = new Request(
            $request->getMethod(), $request->getUri(), $headers,
            Cookies::parseHeader($headers->get('Cookie', [])),
            $request->getServerParams(), $body, $request->getUploadedFiles()
        );

        $response = new Response();

        // run the controller action and test it
        $response = $action->updateTicket($request, $response, ['id' => $random]);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertSame($data['status'], 'success', 'Status should be success');
        $this->assertSame($data['code'], 200, 'Code should be 200 (int)');
        $this->assertArrayHasKey('data', $data, 'JSON response should have `data`-key');
        $this->assertCount(13, $data['data'], 'Data should have 13 elements');

        $ticket = $data['data'];
        $this->assertArrayHasKey('id', $ticket, 'Ticket must have id');
        $this->assertArrayHasKey('first_name', $ticket, 'Ticket must have first_name');
        $this->assertEquals('Updated', $ticket['first_name'], 'Ticket must have updated first name');
        $this->assertArrayHasKey('last_name', $ticket, 'Ticket must have last_name');
        $this->assertEquals('Name', $ticket['last_name'], 'Ticket must have updated last name');
        $this->assertArrayHasKey('email', $ticket, 'Ticket must have email');
        $this->assertArrayHasKey('status', $ticket, 'Ticket must have status');
        $this->assertEquals(2, $ticket['status'], 'Ticket must have updated status');
        $this->assertArrayHasKey('type', $ticket, 'Ticket must have type');
        $this->assertArrayHasKey('event_id', $ticket, 'Ticket must have event_id');
        $this->assertArrayHasKey('hash', $ticket, 'Ticket must have hash');
        $this->assertArrayHasKey('created_on', $ticket, 'Ticket must have created_on');
        $this->assertArrayHasKey('created_by', $ticket, 'Ticket must have created_by');
        $this->assertArrayHasKey('modified_on', $ticket, 'Ticket must have modified_on');
        $this->assertArrayHasKey('modified_by', $ticket, 'Ticket must have modified_by');
        $this->assertArrayHasKey('metadata', $ticket, 'Ticket must have metadata');

        $metadata = json_decode($ticket['metadata'], true);
        $this->assertArrayHasKey('__version', $metadata, 'Metadata must have version');
        $this->assertGreaterThanOrEqual(1, $metadata['__version'], 'Version must be 1 or higher');
    }

    public function testDeleteTicket()
    {
        global $app;
        echo 'Testing deleteTicket' . "\n";
        $random = rand(1,4);
        echo '- entry #' . ($random) . "\n";

        // instantiate controller
        $action = new Ticket($app->getContainer());

        // We need a request and response object to invoke the action

        //$_POST = $sample;
        $environment = Environment::mock([
            'REQUEST_METHOD' => 'DELETE',
            'REQUEST_URI' => '/ticket/' . $random
        ]);
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // run the controller action and test it
        $response = $action->deleteTicket($request, $response, ['id' => $random]);

        $ticket = json_decode((string) $response->getBody(), true);

        $this->assertSame($ticket['status'], 'success', 'Status should be success');
        $this->assertSame($ticket['code'], 200, 'Code should be 200 (int)');
        $this->assertArrayHasKey('data', $ticket, 'JSON response should have `data`-key');
        $this->assertCount(13, $ticket['data'], 'Data should have 13 elements');
        $this->assertArrayHasKey('id', $ticket['data'], 'Ticket must have id');

        $environment = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/ticket'
        ]);
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // run the controller action and test it
        $response = $action->getTickets($request, $response, []);

        $data = json_decode((string)$response->getBody(), true);

        $this->assertSame($data['status'], 'success', 'Status should be success');
        $this->assertSame($data['code'], 200, 'Code should be 200 (int)');
        $this->assertArrayHasKey('data', $data, 'JSON response should have `data`-key');
        $this->assertCount(2, $data['data'], 'Data should have 2 elements');
        $this->assertEquals(3, $data['data']['count'], 'Ticket count should be 3');
    }

    public function tearDown()
    {
        echo 'Closing database connection and cleaning up' . "\n";
        R::close();
        unlink(CF_DB_FILENAME);
    }
}
