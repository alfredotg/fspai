<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

trait ApiModelList
{
    use ApiTrait;

    function modelList(Request $request, Builder $builder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'integer|between:1,500',
            'order' => Rule::in('asc', 'desc'),
            'after_id' => 'integer',
        ]);

        if($validator->fails()) 
            return $this->badRequest($validator->errors());

        $order = $request->get('order') ?? 'asc';
        $limit = $request->get('limit') ?? 10;
        $after_id = $request->get('after_id') ?? -1;

        if($after_id > 0)
        {
            $model = (clone $builder)->find($after_id);
            if(!$model)
                return response()->json([], 400);
            $builder->where(function($query) use($model, $order) {
                $query->where('name', $order == 'asc' ? '>' : '<', $model->name);
                $query->orWhereRaw('name = ? AND id > ?', [$model->name, $model->id]);
            });
        }

        $builder = $builder->orderBy('name', $order)->orderBy('id', 'asc')->limit($limit);
        return response()->json($builder->get()->toArray(), 200);
    } 
}
