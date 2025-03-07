<?php

namespace App\Http\Controllers\API;

use App\Enums\ErrorCode;
use App\Helpers\ApiResponse;;
use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use Illuminate\Http\Response;

class ExamResultController extends Controller
{
    public function getScore($sbd)
    {
        // validate SBD
        if (!preg_match('/^\d{8}$/', $sbd)) {
            $apiResponse = new ApiResponse();
            // Sử dụng error code từ enum cho INVALID_SBD
            $apiResponse->setCode(ErrorCode::INVALID_SBD->value);
            $apiResponse->setMessage(ErrorCode::INVALID_SBD->message());
            // Không set result khi lỗi
            return $apiResponse->toJson();
        }

        // Tìm kiếm kết quả thi theo SBD trong database.
        $examResult = ExamResult::where('sbd', $sbd)->first();

        if (!$examResult) {
            $apiResponse = new ApiResponse();
            // EXAM_RESULT_NOT_FOUND
            $apiResponse->setCode(ErrorCode::INVALID_SBD->value);
            $apiResponse->setMessage(ErrorCode::EXAM_RESULT_NOT_FOUND->message());
            return $apiResponse->toJson();
        }

        // response thành công.
        $apiResponse = new ApiResponse();
        $apiResponse->setCode(Response::HTTP_OK);
        $apiResponse->setMessage('Success');
        $apiResponse->setResult($examResult);
        
        return $apiResponse->toJson();
    }

    /**
     * API thống kê theo môn học với 4 mức điểm.
     * Query parameter: subject (ví dụ: toan, ngu_van, ngoai_ngu, vat_li, hoa_hoc,...)
     */
   //Allowed memory size of 536870912   
   public function getSubjectReport(string $subject)
    {
        // Danh sách các cột hợp lệ để thống kê
        $allowedSubjects = [
            'toan', 'ngu_van', 'ngoai_ngu',
            'vat_li', 'hoa_hoc', 'sinh_hoc',
            'lich_su', 'dia_li', 'gdcd'
        ];
        
        if (!in_array($subject, $allowedSubjects)) {
            $apiResponse = new ApiResponse();
            $apiResponse->setCode(400);
            $apiResponse->setMessage("Subject '{$subject}' không hợp lệ.");
            return $apiResponse->toJson();
        }
        
        // Sử dụng cursor để xử lý dữ liệu từng phần theo cột cần thống kê
        $cursor = ExamResult::whereNotNull($subject)
            ->cursor();
        
        // Khởi tạo biến thống kê cho 4 mức điểm
        $tong_so_hs   = 0;
        $diem_tren_8 = 0;
        $diem_6_8    = 0;
        $diem_4_6    = 0;
        $diem_duoi_4 = 0;
        
        $found = false;  // Để kiểm tra có bản ghi nào hay không
        
        // Duyệt qua từng bản ghi, tính toán các mức điểm
        foreach ($cursor as $record) {
            $found = true;
            $score = $record->$subject;
            
            if ($score >= 8) {
                $diem_tren_8++;
            } elseif ($score >= 6) {
                $diem_6_8++;
            } elseif ($score >= 4) {
                $diem_4_6++;
            } else {
                $diem_duoi_4++;
            }
        }
        
        if (!$found) {
            $apiResponse = new ApiResponse();
            $apiResponse->setCode(404);
            $apiResponse->setMessage("Không tìm thấy dữ liệu cho môn {$subject}.");
            return $apiResponse->toJson();
        }
        
        $tong_so_hs   = $diem_tren_8 + $diem_6_8 + $diem_4_6 + $diem_duoi_4; 
        // Gom dữ liệu thống kê 
        $data = [
            'tong_so_hs'   => $tong_so_hs,
            'diem_tren_8' => $diem_tren_8,
            'diem_6_8'    => $diem_6_8,
            'diem_4_6'    => $diem_4_6,
            'diem_duoi_4' => $diem_duoi_4,
        ];
        
        $apiResponse = new ApiResponse();
        $apiResponse->setCode(Response::HTTP_OK);
        $apiResponse->setMessage('Success');
        $apiResponse->setResult($data);
        
        return $apiResponse->toJson();
    }

    /**
     * Liệt kê top 10 thí sinh khối A (Toán, Vật lý, Hóa học) có tổng điểm cao nhất.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTop10GroupA()
    {
        // Lấy top 10 thí sinh có tổng điểm (toán + vật lý + hóa học) cao nhất.
        // Lưu ý: Nếu một trong các môn có giá trị null thì bạn có thể sử dụng COALESCE trong SQL.
        $top10 = ExamResult::select('*')
            ->selectRaw('(COALESCE(toan,0) + COALESCE(vat_li,0) + COALESCE(hoa_hoc,0)) as total_score')
            ->whereNotNull('toan')
            ->whereNotNull('vat_li')
            ->whereNotNull('hoa_hoc')
            ->orderByDesc('total_score')
            ->limit(10)
            ->get();
        
        if ($top10->isEmpty()) {
            $apiResponse = new ApiResponse();
            $apiResponse->setCode(404);
            $apiResponse->setMessage("Không tìm thấy dữ liệu thí sinh khối A.");
            return $apiResponse->toJson();
        }
        
        $apiResponse = new ApiResponse();
        $apiResponse->setCode(Response::HTTP_OK);
        $apiResponse->setMessage('Success');
        $apiResponse->setResult($top10);
        
        return $apiResponse->toJson();
    }
}
