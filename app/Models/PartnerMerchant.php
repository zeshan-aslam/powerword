<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerMerchant extends Model
{
    protected $table ='partners_merchant';
    protected $guarded=[];
    public $timestamps = false;
    protected $primaryKey ='merchant_id';
    public function program()
    {
        return $this->hasOne(PartnerProgram::class,'program_merchantid');
    }
}
