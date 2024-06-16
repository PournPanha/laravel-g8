<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
         'user_id','title', 'content', 'image', 'video', 'tags'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }
    /**
     * List all posts or filter based on request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function list(Request $request)
    {
        $query = self::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }
        Log::info($query->toSql(), $query->getBindings());

        return $query->get();
    }

    /**
     * Store or update a post.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function store(Request $request, $id = null)
    {
        $data = $request->only('title', 'content', 'auth_id', 'tags');
        $validatedData = [
            'title' => $data['title'] ?? '',
            'content' => $data['content'] ?? '',
            'auth_id' => $data['auth_id'] ?? null,
            'tags' => $data['tags'] ?? '',
        ];

        return self::updateOrCreate(['id' => $id], $validatedData);
    }
}

