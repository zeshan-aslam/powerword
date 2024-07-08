<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnersJoinPgm extends Model
{
 
    protected $table ="partners_joinpgm";
    protected $with =["program",'merchant'];

    public $timestamps = false;

    public function program()
    {
        return  $this->belongsTo(PartnerProgram::class,'joinpgm_programid','program_id');
    }

    public function merchant()
    {
        return $this->belongsTo(PartnerMerchant::class,'joinpgm_merchantid','merchant_id');
    }

}
