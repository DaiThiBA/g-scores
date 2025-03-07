<?php

namespace App\Helpers;

class ApiResponse
{
    private int $code;
    private string $message;
    private $result;

    // public function __construct(int $code, string $message, $result = null)
    // {
    //     $this->code = $code;
    //     $this->message = $message;
    //     $this->result = $result;
    // }

  
    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result): void
    {
        $this->result = $result;
    }

    public function toJson(): string
    {
        $data = [
            'code'    => $this->code,
            'message' => $this->message,
        ];
    
        if ($this->result !== null) {
            $data['result'] = $this->result;
        }
    
        return json_encode($data);
    }
}
