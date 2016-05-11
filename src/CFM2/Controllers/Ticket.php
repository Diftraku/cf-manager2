<?php
/**
 * cf-manager2
 * @package cf-manager2
 * @copyright Copyright (c) 2016, Diftraku
 * @author diftraku
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CFM2\Controllers;

use CFM2\Utilities\FormatResponse;
use CFM2\Utilities\PDFTemplate;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use RedBeanPHP\RedException\SQL as RedExceptionSQL;
use \R;
use TCPDF2DBarcode;

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
     * @return Response
     */
    public function getTickets( Request $request, Response $response, $args = [] ) {
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
        if (!in_array($order_by, ['last_name', 'first_name', 'email', 'city', 'country', 'created_on', 'modified_on'])) {
            $order_by = 'created_on';
        }

        // @TODO Filtering tickets (ie. search)
        // Blatantly copy what Shimmie does?
        // @TODO Make this into a constant
        $filter = 'status != 0';

        // Showtime!
        try {
            $ticketCount = R::count('ticket', $filter);
            $query = sprintf('%s ORDER BY `%s` %s LIMIT %d, %d ', $filter, $order_by, $order_direction, $offset, $limit);
            $tickets = R::find('ticket', $query);
            return $response->withJson(new FormatResponse(['tickets' => $tickets, 'count' => $ticketCount]));
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['offset' => $offset, 'limit' => $limit, 'filter' => $filter, 'order_by' => $order_by, 'order_direction' => $order_direction];
            $this->ci->get('logger')->error(sprintf('getTickets: %s', $message), $params);
            return $response->withStatus(500)->withJson(new FormatResponse([], 500, $message));
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
     * @return Response
     */
    public function getTicket( Request $request, Response $response, $args = [] ) {
        $id = $args['id'];

        // Showtime!
        try {
            if (!is_null($id)) {
                $id = intval($id);
                $ticket = R::findOne('ticket', 'WHERE id = ?', [$id]);
                if (!is_null($ticket)) {
                    $this->ci->get('logger')->debug(sprintf('getTicket: %s', 'Retrieved ticket'), ['id' => $id]);
                    return $response->withJson(new FormatResponse($ticket->export()));
                }
                else {
                    $message = 'Ticket not found';
                    $this->ci->get('logger')->notice(sprintf('getTicket: %s', $message), ['id' => $id]);
                    return $response->withStatus(404)->withJson(new FormatResponse([], 404, $message));
                }
            }
            else {
                return $response->withStatus(400)->withJson(new FormatResponse([], 400, 'Required parameter `id` missing'));
            }
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id];
            $this->ci->get('logger')->error(sprintf('getTicket%s: %s', $message), $params);
            return $response->withStatus(500)->withJson(new FormatResponse([], 500, $message));
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * getTicketPD
     * Get a single ticket PDF by parameter (ID)
     * @method GET
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getTicketPDF( Request $request, Response $response, $args = [] ) {
        $id = $args['id'];

        // Showtime!
        try {
            if (!is_null($id)) {
                $id = intval($id);
                $ticket = R::findOne('ticket', 'WHERE id = ?', [$id]);
                if (!is_null($ticket)) {
                    //$ticket->export();
                    $pdf = new PDFTemplate();
                    $pdf->SetFont('dejavusansmono', '', 12);
                    $pdf->SetTitle('Ticket for ' . $ticket->first_name . ' ' . $ticket->last_name);

                    $pdf->AddPage();
                    $pdf->MultiCell(
                        90,
                        15,
                        $ticket->first_name . ' ' . $ticket->last_name . "\n" .
                        $ticket->address . "\n" .
                        $ticket->postal_code . ' ' . $ticket->city . "\n" .
                        $ticket->country . "\n" . "\n" .
                        $ticket->email,
                        0,
                        'L',
                        false,
                        0
                    );
                    $more = '';
                    /*list($shirt1, $shirt2) = \CrystalFair\Metadata\TicketTypes::getShirts($ticket->type);
                    if ($shirt1) {
                        $more .= 'T-Shirt:  ' . $ticket->shirt1 . "\n";
                    }
                    if ($shirt2) {
                        $more .= 'Hoodie:   ' . $ticket->shirt2;
                    }
                    $pdf->MultiCell(
                        90,
                        15,
                        'Type:     ' . \CrystalFair\Metadata\TicketTypes::getLabel($ticket->type) . "\n" .
                        'Quantity: ' . $ticket->quantity . "\n" .
                        $more,
                        0,
                        'L',
                        false,
                        1
                    );*/
                    $pdf->Ln(90);
                    $pdf->write2DBarcode($ticket->hash, 'QRCODE,H', 75, 70, 60, 60);
                    $pdf->SetFont('dejavusansmono', 'B', 32);
                    $pdf->Cell(0, 20, implode(' ', str_split($ticket->check)), 0, 1, 'C');

                    $pdf->setFooterFont(array('dejavusanscondensed', '', 12));
                    $pdf->SetFont('dejavusansmono', '', 12);
                    //$pdf->Footer();

                    /*$ab = substr($ticket->hash, 0, 2);
                    $cd = substr($ticket->hash, 2, 2);
                    $file = CF_DIR_CACHE . '/' . $ab . '/' . $cd . '/' . $ticket->hash . '.pdf';
                    if (!file_exists(dirname($file))) {
                        mkdir(dirname($file), 0755, true);
                    }*/
                    //$data = $pdf->Output('', 'S');
                    $this->ci->get('logger')->debug(sprintf('getTicketPDF: %s', 'Retrieved ticket'), ['id' => $id]);
                    $response->getBody()->write($pdf->Output('', 'S'));
                    return $response->withHeader('Content-type', 'application/pdf')->withHeader('Content-Disposition', 'inline')->withHeader('Content-Transfer-Encoding', 'binary');
                }
                else {
                    $message = 'Ticket not found';
                    $response->getBody()->write('<html><head><title>'.$message.'</title></head><body><h1>'.$message.'</h1></body></html>');
                    $this->ci->get('logger')->notice(sprintf('getTicketPDF: %s', $message), ['id' => $id]);
                    return $response->withStatus(404);
                }
            }
            else {
                return $response->withStatus(400)->withJson(new FormatResponse([], 400, 'Required parameter `id` missing'));
            }
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id];
            $this->ci->get('logger')->error(sprintf('getTicketPDF%s: %s', $message), $params);
            return $response->withStatus(500)->withJson(new FormatResponse([], 500, $message));
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * getTicketQR
     * Get a single ticket's QR code by parameter (ID)
     * @method GET
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getTicketQR( Request $request, Response $response, $args = [] ) {
        $id = $args['id'];

        // Showtime!
        try {
            if (!is_null($id)) {
                $id = intval($id);
                $ticket = R::findOne('ticket', 'WHERE id = ?', [$id]);
                if (!is_null($ticket)) {
                    $hash = $ticket->export()['hash'];
                    $qr = new TCPDF2DBarcode($hash, 'QRCODE,H');
                    $response->write($qr->getBarcodePngData(6, 6));
                    $this->ci->get('logger')->debug(sprintf('getTicketQR: %s', 'Retrieved ticket'), ['id' => $id]);
                    return $response->withHeader('Content-type', 'image/png');
                }
                else {
                    $message = 'Ticket not found';
                    //$response->withStatus(404)->withJson(new FormatResponse([], 404, $message));
                    $body = $response->getBody();
                    $body->write('<html><head><title>'.$message.'</title></head><body><h1>'.$message.'</h1></body></html>');
                    $this->ci->get('logger')->notice(sprintf('getTicketPDF: %s', $message), ['id' => $id]);
                    return $response->withStatus(404);
                }
            }
            else {
                return $response->withStatus(400)->withJson(new FormatResponse([], 400, 'Required parameter `id` missing'));
            }
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id];
            $this->ci->get('logger')->error(sprintf('getTicketPDF%s: %s', $message), $params);
            return $response->withStatus(500)->withJson(new FormatResponse([], 500, $message));
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
     * @return Response
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
                $ticket = R::findOne('ticket', 'WHERE id = ?', [$id]);
                if (!is_null($ticket)) {
                    $ticket->import($params);
                    R::store($ticket);
                    $this->ci->get('logger')->info(sprintf('updateTicket: %s %s', 'Updated ticket with ID ', $id), $params);
                    return $response->withJson(new FormatResponse($ticket->export()));
                }
                else {
                    $message = 'Ticket not found';
                    $this->ci->get('logger')->notice(sprintf('updateTicket: %s', $message), ['id' => $id]);
                    return $response->withStatus(404)->withJson(new FormatResponse([], 404, $message));
                }
            }
            else {
                return $response->withStatus(400)->withJson(new FormatResponse([], 400, 'Required parameter `id` missing'));
            }
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id, 'params' => $params];
            $this->ci->get('logger')->error(sprintf('updateTicket: %s', $message), $params);
            return $response->withStatus(500)->withJson(new FormatResponse([], 500, $message));
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
     * @return Response
     */
    public function updateTickets( Request $request, Response $response, $args = [] ) {
        return $response->withStatus(501)->withJson(new FormatResponse([], 501, 'Not implemented'));
    }


    /**
     * createTicket
     * Create a ticket with metadata
     * @method POST
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function createTicket( Request $request, Response $response, $args = [] ) {
        // Grab parameter from the request
        // @TODO Add validation
        $params = $request->getParsedBody();

        // Showtime!
        try {
            $ticket = R::dispense('ticket');
            $ticket->import($params);
            $id = R::store($ticket);
            $this->ci->get('logger')->info(sprintf('createTicket%s: %s %s', json_encode($params), 'Created new ticket with ID ', $id));
            return $response->withJson(new FormatResponse(['id' => $id]));
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['params' => $params];
            $this->ci->get('logger')->error(sprintf('createTicket: %s', $message), $params);
            return $response->withStatus(500)->withJson(new FormatResponse([], 500, $message));
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
     * @return Response
     */
    public function createTickets( Request $request, Response $response, $args = [] ) {
        return $response->withStatus(501)->withJson(new FormatResponse([], 501, 'Not implemented'));
    }

    /**
     * deleteTicket
     * Delete ticket
     * @method DELETE
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function deleteTicket( Request $request, Response $response, $args = [] ) {
        // Grab parameter from the request
        // @TODO Add validation
        $id = $args['id'];

        // Showtime!
        try {
            if (!is_null($id)) {
                $id = intval($id);
                $ticket = R::findOne('ticket', 'WHERE id = ?', [$id]);
                if (!is_null($ticket)) {
                    // @TODO Implement actual deletion somewhere else, hide the ticket on the UI
                    //R::trash($ticket);
                    $ticket->import(['status' => 0]);
                    R::store($ticket);
                    $this->ci->get('logger')->info(sprintf('deleteTicket: %s %s', 'Deleted ticket with ID ', $id));
                    return $response->withJson(new FormatResponse($ticket->export()));
                }
                else {
                    $message = 'Ticket not found';
                    $this->ci->get('logger')->notice(sprintf('deleteTicket: %s', $message), ['id' => $id]);
                    return $response->withStatus(404)->withJson(new FormatResponse([], 404, $message));
                }
            }
            else {
                return $response->withStatus(400)->withJson(new FormatResponse([], 400, 'Required parameter `id` missing'));
            }
        }
        catch (RedExceptionSQL $e) {
            $message = sprintf('Backend failure: (%s) %s', $e->getSQLState(), $e->getMessage());
            $params = ['id' => $id];
            $this->ci->get('logger')->error(sprintf('deleteTicket: %s', $message), $params);
            return $response->withStatus(500)->withJson(new FormatResponse([], 500, $message));
        }
        finally {
            // Flush the toilet
            R::close();
        }
    }

    /**
     * deleteTickets
     * Delete multiple tickets
     * @method DELETE
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function deleteTickets( Request $request, Response $response, $args = [] ) {
        return $response->withStatus(501)->withJson(new FormatResponse([], 501, 'Not implemented'));
    }

}