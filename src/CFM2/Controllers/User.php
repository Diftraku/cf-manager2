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

    }
}