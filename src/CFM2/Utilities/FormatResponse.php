<?php
/**
 * Created by IntelliJ IDEA.
 * User: diftraku
 * Date: 04/05/16
 * Time: 17:37
 */

namespace CFM2\Utilities;

use JsonSerializable;

class FormatResponse implements JsonSerializable
{
    protected $data = [];
    protected $code = 0;
    protected $message = '';
    protected $status = '';

    /**
     * FormatResponse constructor.
     * @param array $data
     * @param int $code
     * @param string $message
     * @param string $status
     */
    public function __construct(array $data = [], $code = 200, $message = '', $status = 'success')
    {
        $this->data = $data;
        $this->code = $code;
        $this->message = $message;
        $this->status = $status;
        return $this;
    }

    /**
     * format
     * Returns a 'formatted' array with values
     * @return array
     */
    public function format() {
        $return = [];
        if ($this->getCode() >= 400 && $this->getCode() < 500) {
            $this->setStatus('error');
        }
        if ($this->getCode() >= 500 && $this->getCode() < 600) {
            $this->setStatus('failure');
        }
        $return['status'] = $this->getStatus();
        $return['code'] = $this->getCode();
        if (!empty($this->getMessage())) {
            $return['message'] = $this->getMessage();
        }
        if (!empty($this->getData())) {
            $return['data'] = $this->getData();
        }
        return $return;
    }

    /**
     * jsonSerialize
     * Returns data to be serialized into JSON
     * @return array
     */
    public function jsonSerialize() {
        return $this->format();
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}