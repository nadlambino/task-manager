<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Status\CreateRequest;
use App\Http\Requests\Api\Status\SortRequest;
use App\Http\Requests\Api\Status\UpdateRequest;
use App\Models\Status;
use App\QueryBuilders\Filters\TrashedFilter;
use App\QueryBuilders\Status\Filters\SearchFilter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        try {
            $statuses = QueryBuilder::for($request->user()->statuses())
                ->allowedFilters([
                    AllowedFilter::custom('search', new SearchFilter),
                    AllowedFilter::custom('trashed', new TrashedFilter),
                ])
                ->defaultSort('order')
                ->allowedSorts(['order', 'created_at', 'name'])
                ->get();

            return $this->success('Statuses retrieved successfully.', $statuses);
        } catch (Exception) {
            return $this->error('Something went wrong while retrieving the statuses. Please try again later.');
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            if ($request->user()->statuses->count() >= 5) {
                return $this->error('You have reached the maximum number of statuses allowed.');
            }

            $status = $request->user()->statuses()->create($request->validated());

            return $this->success('Status was successfully created.', $status);
        } catch (Exception) {
            return $this->error('Something went wrong while creating the status. Please try again later.');
        }
    }

    public function update(UpdateRequest $request, Status $status)
    {
        try {
            Gate::authorize('update', $status);

            $status->update($request->validated());

            return $this->success('Status was successfully updated.', $status);
        } catch (Exception) {
            return $this->error('Something went wrong while updating the status. Please try again later.');
        }
    }

    public function sort(SortRequest $request, Status $status)
    {
        try {
            $newOrder = $request->validated('new_order');
            $oldOrder = $request->validated('old_order');

            if ($oldOrder < $newOrder) {
                Status::where('order', '>=', $oldOrder)
                    ->where('order', '<=', $newOrder)
                    ->decrement('order');
            } else {
                Status::where('order', '<=', $oldOrder)
                    ->where('order', '>=', $newOrder)
                    ->increment('order');
            }

            $status->order = $newOrder;
            $status->save();

            return $this->success('Statuses were successfully sorted.', $status);
        } catch (Exception) {
            return $this->error('Something went wrong while sorting the statuses. Please try again later.');
        }
    }

    public function destroy(Status $status)
    {
        try {
            Gate::authorize('delete', $status);

            $status->delete();

            return $this->success('Status was successfully deleted.');
        } catch (Exception) {
            return $this->error('Something went wrong while deleting the status. Please try again later.');
        }
    }

    public function restore(int $id)
    {
        try {
            $status = Status::onlyTrashed()->findOrFail($id);

            Gate::authorize('restore', $status);

            $status->restore();

            return $this->success('Status was successfully restored.');
        } catch (Exception) {
            return $this->error('Something went wrong while restoring the status. Please try again later.');
        }
    }
}