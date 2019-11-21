<?php

namespace App\Traits;

use App\Models\Request;
use Illuminate\Support\Str;

trait RequestRelation
{
    /**
     * @return mixed
     */
    public function requests()
    {
        return $this->morphToMany(Request::class, 'assignee', 'request_assignees', 'assignee_id', 'request_id');
    }

    /**
     * @return mixed
     */
    public function pendingRequests()
    {
        return $this->requests()->whereIn('requests.status', Request::PendingStatuses);
    }

    /**
     * @return mixed
     */
    public function solvedRequests()
    {
        return $this->requests()->whereIn('requests.status', Request::SolvedStatuses);
    }

    /**
     * @param $q
     * @return mixed
     */
    public function scopeAllRequestStatusCount($q)
    {
        $withCount = [];
        foreach (Request::Status as $value) {
            $withCount[] = 'requests' . str_replace('_', '', Str::title($value));
        }

        return $q->withCount($withCount);
    }

    /**
     * @return array
     */
    public function getStatusRelationCounts()
    {
        $statusCounts = [];
        $attributes = $this->getAttributes();

        $isExistOne = false;
        foreach (Request::Status as $value) {
            $attribute = 'requests_' . $value . '_count';
            if (key_exists($attribute, $attributes)) {
                $isExistOne = true;
            }
            $statusCounts[$attribute] = $attributes[$attribute] ?? 0;
        }

        if (! $isExistOne) {
            return [];
        }

        if (key_exists('requests_count', $attributes)) {
            $statusCounts['requests_count'] = $this->requests_count;
        } else {
            $statusCounts['requests_count'] = array_sum($statusCounts);
        }


        if (key_exists('solved_requests_count', $attributes)) {
            $statusCounts['solved_requests_count'] = $this->solved_requests_count;
        }

        if (key_exists('pending_requests_count', $attributes)) {
            $statusCounts['pending_requests_count'] = $this->pending_requests_count;
        }

        return $statusCounts;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'requests')) {
            $status = substr($name, strlen('requests'));
            $statusFullName = Request::class . '::Status' . $status;
            if (defined($statusFullName)) {
                return $this->requests()->where('requests.status', constant($statusFullName));
            }
        }
        return parent::__call($name, $arguments);
    }
}
