<?php 
namespace App\Enums;


enum TransactionStatus : string {
    case SUCCUSS = "success";
    case FAILED = "failed";

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function description(): string
    {
        return match($this) 
        {
            self::SUCCUSS => 'Success transaction is a completed successful transaction ',
            self::FAILED => 'Failed transaction is a transaction is that failed for a reason (eg. Insufficient Balance)',
        };
    }

}
