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
            $message = printf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id];
            $this->ci->get('logger')->error(printf('getUser%s: %s', json_encode($params), $message));
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
                $this->ci->get('logger')->info(printf('updateUser%s: %s %s', json_encode($params), 'Updated user with ID ', $id));
                $response->withJson($user);
            }
            else {
                $response->withStatus(400)->withJson(['message' => 'Required parameter `id` missing']);
            }
        }
        catch (RedExceptionSQL $e) {
            $message = printf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id, 'params' => $params];
            $this->ci->get('logger')->error(printf('updateUser%s: %s', json_encode($params), $message));
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
            $this->ci->get('logger')->info(printf('createUser%s: %s %s', json_encode($params), 'Created new user with ID ', $id));
            // @TODO Standardize the response format (payload,message,code etc.)
            $response->withJson(['status' => 'success', 'id' => $id]);
        }
        catch (RedExceptionSQL $e) {
            $message = printf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['params' => $params];
            $this->ci->get('logger')->error(printf('createUser%s: %s', json_encode($params), $message));
            $response->withStatus(500)->withJson(['message' => $message]);
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }
}