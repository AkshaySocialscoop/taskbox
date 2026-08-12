<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


use App\Models\Traits\BelongsToCompany;

class Task extends Model

{
    use BelongsToCompany;

    use HasFactory;



     protected $fillable = [

        'title',

        'description',

        'assigned_to',

        'created_by',

        'priority',

        'status',

        'due_date',

        'attachment',

        'comment',

    ];



    public function assignedUser()

    {

        return $this->belongsTo(User::class, 'assigned_to');

    }



    public function creator()

    {

        return $this->belongsTo(User::class, 'created_by');

    }



    public function getPriorityColorAttribute()

    {

        return match($this->priority) {

            'red' => '#dc3545',

            'yellow' => '#ffc107',

            'green' => '#28a745',

            default => '#6c757d'

        };

    }



    public function getPriorityLabelAttribute()

    {

        return match($this->priority) {

            'red' => 'Urgent',

            'yellow' => 'Less Urgent',

            'green' => 'Not Urgent',

            default => 'Unknown'

        };

    }



     protected $casts = [

        'due_date' => 'datetime', // or 'datetime' if time exists

    ];

}

