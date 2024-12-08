<?php

namespace Modules\Tasks\Repositories;

use Exception;
use Carbon\Carbon;
use Modules\Tasks\Entities\Task;
use App\Repositories\BaseRepository;
use Modules\Tasks\Repositories\Contracts\TasksRepositoryInterface;

class TasksRepository extends BaseRepository implements TasksRepositoryInterface
{
    protected $model;

    /**
     * @param Task $model
     * @return void
     */
    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $request
     * @return array
     */
    public function index($request): array
    {
        $rows = $this->model
                        ->when(!empty($request['status']), function($query) use ($request) {
                            $query->where('status', $request['status']);
                        })
                        ->get();

        $rows = $rows->map(function ($task) {
            return [
                'id'            => $task->id,
                'encryptId'     => encrypt($task->id),
                'title'         => $task->title,
                'description'   => $task->description,
                'status'        => $task->status,
                'category'      => $task->category->name
            ];
        });
    
        return [
            'rows' => $rows->toArray() ?? [],
        ];
    }

    /**
     * @param array $request
     * @param string|null $id
     * @return mixed
     */
    public function createOrUpdate($request, $id=NULL): mixed
    {
        try {
            $row                = (isset($id)) ? $this->model::find(decrypt($id)) : new $this->model;

            $row->title         = $request['title'];
            $row->description   = $request['description'];
            $row->status        = $request['status'];
            $row->category_id   = $request['categoryId'];
            $row->save();

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param mixed $task
     * @return array
     */
    public function find($task): array
    {
        $row = $this->model
                        ->whereNULL('deleted_at')
                        ->Where('id', decrypt($task))
                        ->first();

        return [
            'id'            => $row->id,
            'encryptId'     => encrypt($row->id),
            'title'         => $row->title,
            'description'   => $row->description,
            'status'        => $row->status,
            'categoryId'    => $row->category_id,
        ];
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function destroy($id): mixed
    {
        try {
            $this->model->where('id', decrypt($id))->update(['deleted_at' => Carbon::now()]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Summary of restore
     * @param mixed $id
     * @return mixed
     */
    public function restore($id): mixed
    {
        try {
            $this->model
                    ->when(!empty($id), function($query) use ($id) {
                        if (is_numeric($id)) {
                            $query->whereId($id);
                        } else {
                            $query->Where('id', decrypt($id));
                        }
                    })
                    ->restore();
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
