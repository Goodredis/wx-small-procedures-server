<?php

namespace App\Repositories;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\BaseRepository;

abstract class AbstractEloquentRepository implements BaseRepository
{
    /**
     * Name of the Model with absolute namespace
     *
     * @var string
     */
    protected $modelName;

    /**
     * Instance that extends Illuminate\Database\Eloquent\Model
     *
     * @var Model
     */
    protected $model;

    /**
     * get logged in user
     *
     * @var User $loggedInUser
     */
    protected $loggedInUser;

    /**
     * Constructor
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        //$this->loggedInUser = $this->getLoggedInUser();
    }

    /**
     * Get Model instance
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria)
    {
        return $this->model->where($criteria)->first();
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], array $operatorCriteria = [])
    {
        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15; // it's needed for pagination
        $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1; //默认为第一页

        $columns = ['*'];
        if(!empty($searchCriteria['columns'])) {
            $columns = explode(',', $searchCriteria['columns']);
            unset($searchCriteria['columns']);;
        }

        $orderby = '';
        if(!empty($searchCriteria['orderby'])) {
            $orderby = trim($searchCriteria['orderby']);
            unset($searchCriteria['orderby']);
        }

        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria, $operatorCriteria) {

            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria, $operatorCriteria);
        });

        $queryBuilder = $this->applyOrderCriteriaInQueryBuilder($queryBuilder, $orderby);

        return $queryBuilder->paginate($limit, $columns, 'page', $page);
    }


    /**
     * Apply condition on query builder based on search criteria
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder($queryBuilder, array $searchCriteria = [], array $operatorCriteria = [])
    {

        foreach ($searchCriteria as $key => $value) {

            //skip pagination related query params
            if (in_array($key, ['page', 'per_page'])) {
                continue;
            }

            //we can pass multiple params for a filter with commas
            $allValues = explode(',', $value);
            $betValues = explode('~', $value);

            if (!empty($operatorCriteria[$key]) && $operatorCriteria[$key] == 'raw') {
                $queryBuilder->whereRaw($value);
            } elseif (count($allValues) > 1) {
                $queryBuilder->whereIn($key, $allValues);
            } elseif (count($betValues) > 1 && $operatorCriteria[$key] == 'between') {
                $first = current($betValues);
                $second = end($betValues);
                if (empty($first)) {
                    $queryBuilder->where($key, '<=', $second);
                } elseif (empty($second)) {
                    $queryBuilder->where($key, '>=', $first);
                } else {
                    $queryBuilder->whereBetween($key, $betValues);
                }
            } else {
                $operator = array_key_exists($key, $operatorCriteria) ? $operatorCriteria[$key] : '=';
                $queryBuilder->where($key, $operator, $value);
            }
        }

        return $queryBuilder;
    }

    /**
     * @inheritdoc
     */
    protected function applyOrderCriteriaInQueryBuilder($queryBuilder, $orderby) 
    {
        if (empty($orderby)) {
            return $queryBuilder;
        }

        $sections = explode(',', trim($orderby));
        foreach ($sections as $section) {
            $section = trim($section);
            if ($section) {
                $tmp = explode(' ', $section);
                $queryBuilder = $queryBuilder->orderBy(trim($tmp[0]), isset($tmp[1]) ? $tmp[1] : 'ASC');
            }
        }
        return $queryBuilder;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data)
    {
        // generate uid
        if(!$this->model->incrementing) $data['id'] = Uuid::uuid4();
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {

            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }

        // update the model
        $model->save();

        // get updated model from database
        $model = $this->findOne($model->id);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function findIn($key, array $values)
    {
        return $this->model->whereIn($key, $values)->get();
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model){
        return $model->delete();
    }

    /**
     * 批量删除
     * @param array $ids，注意id必须是数组，即使只有一个元素也得是数组格式
    */
    public function destroy($ids){
        return $this->model->destroy($ids);
    }

    /**
     * get loggedIn user
     *
     * @return User
     */
    protected function getLoggedInUser()
    {
        $user = \Auth::user();

        if ($user instanceof User) {
            return $user;
        } else {
            return new User();
        }
    }

}
