<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAPACommentary extends Model
{
    use HasFactory;

    protected $table = 'capa_commentary';
    protected $fillable = ['capa_id', 'user_id', 'comment', 'type'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
?>