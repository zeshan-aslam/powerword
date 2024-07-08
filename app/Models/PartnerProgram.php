<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProgram extends Model
{
    protected $table ="partners_program";
    protected $with =["text"];

    public function text()
    {
        return $this->hasOne(PartnerTextOld::class,'text_programid','program_id');
    }
}
