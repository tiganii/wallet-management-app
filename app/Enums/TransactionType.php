<?php 
namespace App\Enums;


enum TransactionType : string {
    case CREDIT = "credit";
    case DEBIT = "debit";

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function description(): string
    {
        return match($this) 
        {
            self::CREDIT => 'Credit transaction is a transaction used to credit wallet (eg. add fund, transfer to wallet, ...etc)',
            self::DEBIT => 'debit transaction is a transaction used to debit from wallet (eg. withdraw fund, transfer from wallet, ...etc)',
        };
    }

}
