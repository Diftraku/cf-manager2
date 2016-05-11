<?php
/**
 * Created by IntelliJ IDEA.
 * User: diftraku
 * Date: 11/05/16
 * Time: 23:01
 */

namespace CFM2\Utilities;


class FormatResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testError() {
        $fr = new FormatResponse([], 418);
        $data = json_encode($fr);
        $this->assertEquals('{"status":"error","code":418}', $data, 'HTTP 418 with no message');

        $fr = new FormatResponse([], 418, "I'm a teapot");
        $data = json_encode($fr);
        $this->assertEquals('{"status":"error","code":418,"message":"I\'m a teapot"}', $data, 'HTTP 418 with message');

        $fr = new FormatResponse(['key' => 'value'], 418);
        $data = json_encode($fr);
        $this->assertEquals('{"status":"error","code":418,"data":{"key":"value"}}', $data, 'HTTP 418 with data, no message');

        $fr = new FormatResponse(['key' => 'value'], 418, "I'm a teapot");
        $data = json_encode($fr);
        $this->assertEquals('{"status":"error","code":418,"message":"I\'m a teapot","data":{"key":"value"}}', $data, 'HTTP 418 with message and data');
    }

    public function testSuccess() {
        $fr = new FormatResponse([], 200);
        $data = json_encode($fr);
        $this->assertEquals('{"status":"success","code":200}', $data, 'HTTP 200 with no data, no message');

        $fr = new FormatResponse([], 200, 'Found');
        $data = json_encode($fr);
        $this->assertEquals('{"status":"success","code":200,"message":"Found"}', $data, 'HTTP 200 with message, no data');

        $fr = new FormatResponse(['key' => 'value'], 200);
        $data = json_encode($fr);
        $this->assertEquals('{"status":"success","code":200,"data":{"key":"value"}}', $data, 'HTTP 200 with data, no message');

        $fr = new FormatResponse(['key' => 'value'], 200, "Found");
        $data = json_encode($fr);
        $this->assertEquals('{"status":"success","code":200,"message":"Found","data":{"key":"value"}}', $data, 'HTTP 200 with message and data');
    }

    public function testFailure() {
        $fr = new FormatResponse([], 500);
        $data = json_encode($fr);
        $this->assertEquals('{"status":"failure","code":500}', $data, 'HTTP 500 with no data, no message');

        $fr = new FormatResponse([], 500, 'Internal Server Error');
        $data = json_encode($fr);
        $this->assertEquals('{"status":"failure","code":500,"message":"Internal Server Error"}', $data, 'HTTP 500 with message, no data');

        $fr = new FormatResponse(['key' => 'value'], 500);
        $data = json_encode($fr);
        $this->assertEquals('{"status":"failure","code":500,"data":{"key":"value"}}', $data, 'HTTP 500 with data, no message');

        $fr = new FormatResponse(['key' => 'value'], 500, "Internal Server Error");
        $data = json_encode($fr);
        $this->assertEquals('{"status":"failure","code":500,"message":"Internal Server Error","data":{"key":"value"}}', $data, 'HTTP 500 with message and data');
    }

    public function testMutability() {
        $fr = new FormatResponse([]);
        $fr->setStatus('herp');
        $fr->setCode(999);
        $fr->setData(['key' => 'value']);
        $fr->setMessage('Derp');
        $data = json_encode($fr);
        $this->assertEquals('{"status":"herp","code":999,"message":"Derp","data":{"key":"value"}}', $data, 'Mutability test');
    }
}
