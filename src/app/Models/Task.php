<?php

namespace App\Models;

use App\Http\Requests\FilterTasksRequest;
use App\TaskStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $casts = [
        'due_date' => 'datetime',
        'status' => TaskStatus::class,
        'tags' => 'json'
    ];

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'priority',
        'tags',
    ];

    protected $appends = [
        'status_label',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute()
    {
        return $this->status->label();
    }

    public function scopeWithStatus(Builder $query, TaskStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeInboxFor(Builder $query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForProject(Builder $query, Project $project)
    {
        return $query->where('project_id', $project->id);
    }

    public function scopeForUser(Builder $query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeTwoMinuteFor(Builder $query, User $user)
    {
        return $this->inboxFor($user)
            ->whereJsonContains('tags', ['value' => 'two-minute']);
    }

    public function scopeByFilter(Builder $query, FilterTasksRequest $request)
    {
        return $query->when($request->status, function ($query) use ($request) {
            return $query->where('status', $request->status);
        })
            ->when($request->search, function ($query) use ($request) {
                return $query->where('title', 'like', '%' . $request->search . '%');
            })
            ->when($request->tags, function ($query) use ($request) {
                return $query->whereJsonContains('tags', $request->tags);
            });
    }

    public function getCalendarAttribute()
    {
        return (object) [
            'id' => $this->id,
            'start' => $this->due_date,
            'end' => $this->due_date,
            'title' => $this->title,
            'color' => $this->project?->color,
        ];
    }

    public function getBoardAttribute()
    {
        return (object) [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'tags' => $this->tags,
        ];
    }
}
