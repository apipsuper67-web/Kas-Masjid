<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'address', 'bank_account', 'bank_name', 'account_holder', 'logo_path'])]
class MosqueSetting extends Model {}
