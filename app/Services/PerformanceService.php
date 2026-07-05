<?php

namespace App\Services;

use App\Models\Performance;
use Illuminate\Http\Request;

class PerformanceService
{

    public function relations(): array
    {
        return [
            'earnings',
            'bonuses',
            'penalties',
            'deductions',
            'split',
            'user',
            'studio',
            'platforms',
        ];
    }

    public function list(Request $request)
    {

        $query = Performance::query();


        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );


            $query->where(function($q) use ($search){

                $q->where(
                    'first_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'last_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'nickname',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'email',
                    'like',
                    "%{$search}%"
                );

            });

        }

        if ($request->filled('active')) {

            $query->where(
                'active',
                filter_var(
                    $request->active,
                    FILTER_VALIDATE_BOOLEAN
                )
            );

        }


        $allowedSorts = [
            'created_at',
            'hours_streamed'
        ];


        $sort = $request->get(
            'sortBy',
            'created_at'
        );


        if (!in_array($sort,$allowedSorts)) {

            $sort='created_at';

        }

        return $query

            ->with(
                $this->relations()
            )

            ->orderBy(
                $sort,
                $request->get(
                    'order',
                    'desc'
                )
            )

            ->paginate(
                (int)$request->get(
                    'limit',
                    10
                )
            );

    }

    public function find($id)
    {
        return Performance::with(
            $this->relations()
        )
        ->findOrFail($id);
    }

    public function create(array $data)
    {

        $data['active'] =
            $data['active'] ?? true;


        $data['hours_streamed'] =
            $data['hours_streamed'] ?? 0;



        return Performance::create($data)
            ->load(
                $this->relations()
            );

    }

    public function update(
        $id,
        array $data
    )
    {

        $performance =
            Performance::findOrFail($id);


        $performance->update($data);


        return $performance
            ->fresh()
            ->load(
                $this->relations()
            );

    }

    public function delete($id)
    {

        $performance =
            Performance::findOrFail($id);


        $performance->delete();

    }

    public function leaderboard(
        int $limit = 20
    )
    {

        return Performance::query()

            ->select([
                'id',
                'first_name',
                'last_name',
                'nickname',
                'profile_photo',
                'hours_streamed',
                'active',
            ])

            ->where(
                'active',
                true
            )

            ->orderByDesc(
                'hours_streamed'
            )

            ->limit($limit)

            ->get()

            ->map(function($performance){

                $performance->ranking_score = 0;

                return $performance;

            });

    }

    public function storeRules(): array
    {

        return [

            'studio_id'=>'nullable|integer',

            'user_id'=>'nullable|integer',

            'first_name'=>'required|string|max:255',

            'last_name'=>'required|string|max:255',

            'nickname'=>'nullable|string|max:255',

            'email'=>'required|email|unique:performances,email',

            'phone'=>'nullable|string|max:255',

            'country'=>'nullable|string|max:255',

            'city'=>'nullable|string|max:255',

            'address'=>'nullable|string',

            'birth_date'=>'required|date',

            'profile_photo'=>'nullable|string',

            'active'=>'nullable|boolean',

            'hours_streamed'=>'nullable|integer',

        ];

    }

    public function updateRules(): array
    {

        return [

            'first_name'=>'nullable|string|max:255',

            'last_name'=>'nullable|string|max:255',

            'nickname'=>'nullable|string|max:255',

            'email'=>'nullable|email',

            'phone'=>'nullable|string|max:255',

            'country'=>'nullable|string|max:255',

            'city'=>'nullable|string|max:255',

            'address'=>'nullable|string',

            'birth_date'=>'nullable|date',

            'profile_photo'=>'nullable|string',

            'active'=>'nullable|boolean',

            'hours_streamed'=>'nullable|integer',

        ];

    }

}