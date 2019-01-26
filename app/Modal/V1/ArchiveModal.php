<?php

namespace App\Modal\V1;
use Illuminate\Database\Eloquent\Model;

class ArchiveModal extends Model{
    //数据库表名
    protected $table = 'archive';

    //数据库字段
    protected $fillable = ['name', 'category', 'sex', 'avatarid', 'birth', 'userid'];
    
}