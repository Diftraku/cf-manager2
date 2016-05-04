<?php
/**
 * Created by IntelliJ IDEA.
 * User: diftraku
 * Date: 02/05/16
 * Time: 18:32
 */

namespace CFM2\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use \R;

class User
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
     * loginUser
     * Process user login
     * @method POST
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function loginUser( Request $request, Response $response, $args = [] ) {
        $response->withStatus(501)->withJson(['message' => 'Not implemented']);
    }

    /**
     * logoutUser
     * Process user logout
     * @method POST
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function logoutUser( Request $request, Response $response, $args = [] ) {
        $response->withStatus(501)->withJson(['message' => 'Not implemented']);
    }

    /**
     * getUsers
     * Retrieve list of users from the database
     * @method GET
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function getUsers( Request $request, Response $response, $args = [] ) {
        // Grab parameters from the request
        list($offset, $limit, $filter, $order_by) = [
            intval($request->getQueryParam('offset', 0)),
            intval($request->getQueryParam('limit', 20)),
            $request->getQueryParam('filter', ''),
            $request->getQueryParam('order_by', 'created_on')
        ];

        // Get sort direction
        $order_direction = (strstr($order_by, '-') !== FALSE) ? 'ASC' : 'DESC';
        $order_by = str_replace('-', '', $order_by);

        // Sanity checking for offset and limit
        $offset = ($offset < 0) ? 0 : $offset;
        $limit = ($limit <= 0) ? 20 : $limit;
        $limit = ($limit > 60) ? 60 : $limit;

        // Verify the column to order by
        if (!in_array($order_by, ['name', 'username', 'email', 'role', 'last_access', 'modified_on', 'created_on'])) {
            $order_by = 'created';
        }

        // @TODO Filtering users (ie. search)
        // Blatantly copy what Shimmie does?

        // Showtime!
        try {
            $userCount = R::count('user');
            $users = R::findAndExport('user', 'LIMIT ? OFFSET ? ORDER BY ? ?', [$limit, $offset, $order_by, $order_direction]);
            $response->withJson(['users' => $users, 'count' => $userCount]);
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['offset' => $offset, 'limit' => $limit, 'filter' => $filter, 'order_by' => $order_by, 'order_direction' => $order_direction];
            $this->ci->get('logger')->error(sprintf('getUsers: %s', $message), $params);
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * getUser
     * Return user details
     * @method GET
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function getUser( Request $request, Response $response, $args = [] ) {
        $id = $args['id'];

        // Showtime!
        try {
            if (!is_null($id)) {
                $id = intval($id);
                $user = R::findOne('user', 'WHERE id = ?', [$id])->export();
                $response->withJson($user);
            }
            else {
                $response->withStatus(400)->withJson(['message' => 'Required parameter `id` missing']);
            }
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id];
            $this->ci->get('logger')->error(sprintf('getUser: %s', $message), $params);
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * updateUser
     * Update user details
     * @method PUT
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function updateUser( Request $request, Response $response, $args = [] ) {
        // Grab parameter from the request
        // @TODO Add validation
        $params = $request->getParsedBody();
        $id = $args['id'];

        // Showtime!
        try {
            if (!is_null($id)) {
                $id = intval($id);
                $user = R::findOne('user', 'WHERE id = ?', [$id]);
                $user->import($params);
                R::store($user);
                $this->ci->get('logger')->info(sprintf('updateUser: %s %s', 'Updated user with ID ', $id), $params);
                $response->withJson($user);
            }
            else {
                $response->withStatus(400)->withJson(['message' => 'Required parameter `id` missing']);
            }
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id, 'params' => $params];
            $this->ci->get('logger')->error(sprintf('updateUser: %s', $message), $params);
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * createUser
     * Create a new user
     * @method POST
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function createUser( Request $request, Response $response, $args = [] ) {
        // Grab parameter from the request
        // @TODO Add validation
        $params = $request->getParsedBody();

        // Showtime!
        try {
            $ticket = R::dispense('user');
            $ticket->import($params);
            $id = R::store($ticket);
            $this->ci->get('logger')->info(sprintf('createUser: %s %s', 'Created new user with ID ', $id), $params);
            // @TODO Standardize the response format (payload,message,code etc.)
            $response->withJson(['status' => 'success', 'id' => $id]);
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['params' => $params];
            $this->ci->get('logger')->error(sprintf('createUser: %s', $message), $params);
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }
}