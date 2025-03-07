<?php

namespace App\Enums;

enum ErrorCode :int
{
    case INVALID_SBD = 1002;
    case EXAM_RESULT_NOT_FOUND = 1003;
    public function message(): string
    {
        return match($this) {
            self::INVALID_SBD => 'Số báo danh không hợp lệ! Phải là số và chỉ có 8 ký tự.',
            self::EXAM_RESULT_NOT_FOUND => 'Không tìm thấy kết quả cho số báo danh này.',
        };
    }
}
