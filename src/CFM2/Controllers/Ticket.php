<?php
/**
 * cf-manager2
 * @package cf-manager2
 * @copyright Copyright (c) 2016, Diftraku
 * @author diftraku
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CFM2\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use RedBeanPHP\RedException\SQL as RedExceptionSQL;

class Ticket
{
    /**
     * Slim Container
     * @var ContainerInterface
     */
    protected $ci;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }

    /**
     * getTickets
     * Retrieve tickets from the database
     * @method GET
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function getTickets( Request $request, Response $response, $args = [] ) {
        // Grab parameters from the request
        list($offset, $limit, $filter, $order_by) = [
            intval($request->getQueryParam('offset', 0)),
            intval($request->getQueryParam('limit', 20)),
            $request->getQueryParam('filter', ''),
            $request->getQueryParam('order_by', 'created')
        ];

        // Get sort direction
        $order_direction = (strstr($order_by, '-') !== FALSE) ? 'ASC' : 'DESC';
        $order_by = str_replace('-', '', $order_by);

        // Sanity checking for offset and limit
        $offset = ($offset < 0) ? 0 : $offset;
        $limit = ($limit <= 0) ? 20 : $limit;
        $limit = ($limit > 60) ? 60 : $limit;

        // Verify the column to order by
        if (!in_array($order_by, ['last_name', 'first_name', 'email', 'city', 'country', 'created'])) {
            $order_by = 'created';
        }

        // @TODO Filtering tickets (ie. search)
        // Blatantly copy what Shimmie does?

        // Showtime!
        try {
            $ticketCount = R::count('tickets');
            $tickets = R::findAndExport('tickets', 'LIMIT ? OFFSET ? ORDER BY ? ?', [$limit, $offset, $order_by, $order_direction]);
            $response->withJson(['tickets' => $tickets, 'count' => $ticketCount]);
        }
        catch (RedExceptionSQL $e) {
            $message = printf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['offset' => $offset, 'limit' => $limit, 'filter' => $filter, 'order_by' => $order_by, 'order_direction' => $order_direction];
            $this->ci->get('logger')->error(printf('getTickets%s: %s', json_encode($params), $message));
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * getTicket
     * Get a single ticket by parameter (ID)
     * @method GET
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function getTicket( Request $request, Response $response, $args = [] ) {
        $id = $args['id'];

        // Showtime!
        try {
            if (!is_null($id)) {
                $id = intval($id);
                $ticket = R::findOne('tickets', 'WHERE id = ?', [$id])->export();
                $response->withJson($ticket);
            }
            else {
                $response->withStatus(400)->withJson(['message' => 'Required parameter `id` missing']);
            }
        }
        catch (RedExceptionSQL $e) {
            $message = printf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id];
            $this->ci->get('logger')->error(printf('getTicket%s: %s', json_encode($params), $message));
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * updateTicket
     * Update ticket's metadata
     * @method PUT
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function updateTicket( Request $request, Response $response, $args = [] ) {
        // Grab parameter from the request
        // @TODO Add validation
        $params = $request->getParsedBody();
        $id = $args['id'];

        // Showtime!
        try {
            if (!is_null($id)) {
                $id = intval($id);
                $ticket = R::findOne('tickets', 'WHERE id = ?', [$id]);
                $ticket->import($params);
                R::store($ticket);
                $response->withJson($ticket);
            }
            else {
                $response->withStatus(400)->withJson(['message' => 'Required parameter `id` missing']);
            }
        }
        catch (RedExceptionSQL $e) {
            $message = printf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id, 'params' => $params];
            $this->ci->get('logger')->error(printf('updateTicket%s: %s', json_encode($params), $message));
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * updateTickets
     * Update multiple tickets
     * @method PUT
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function updateTickets( Request $request, Response $response, $args = [] ) {
        $response->withStatus(501)->withJson(['message' => 'Not implemented']);
    }


    /**
     * createTicket
     * Create a ticket with metadata
     * @method POST
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function createTicket( Request $request, Response $response, $args = [] ) {
        // Grab parameter from the request
        // @TODO Add validation
        $params = $request->getParsedBody();

        // Showtime!
        try {
            $ticket = R::dispense('tickets');
            $ticket->import($params);
            $id = R::store($ticket);
            $this->ci->get('logger')->info(printf('createTicket%s: %s %s', json_encode($params), 'Created new ticket with ID ', $id));
            // @TODO Standardize the response format (payload,message,code etc.)
            $response->withJson(['status' => 'success', 'id' => $id]);
        }
        catch (RedExceptionSQL $e) {
            $message = printf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['params' => $params];
            $this->ci->get('logger')->error(printf('createTicket%s: %s', json_encode($params), $message));
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * createTickets
     * Create multiple tickets with metadata
     * @method POST
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function createTickets( Request $request, Response $response, $args = [] ) {
        $response->withStatus(501)->withJson(['message' => 'Not implemented']);
    }
}