<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\User;
use App\Models\Shift;

class DashboardService
{
    public function __construct(
        private StatisticsService $statistics,
        private FinancialSummaryService $financialSummary,
        private ShiftService $shiftService
    ) {}

    public function getData(User $user): array
    {
        $dashboard = match (true) {
            $user->hasRole('Super Admin') => $this->superAdminData(),
            $user->hasRole('Admin')       => $this->studioData($user, 'admin'),
            $user->hasRole('Monitor')     => $this->studioData($user, 'monitor'),
            $user->hasRole('Performance') => $this->performanceData($user),
            default => ['view'=>'unknown','models'=>[]],
        };

        return [
            'dashboard'=>$dashboard,
            'finance'=>$this->financeData(),
            'operations'=>$this->operationData(),
        ];
    }

    private function performanceRelations(): array
    {
        return [
            'user:id,name,email',
            'platforms:id,name,type,conversion_rate,multiplier',
            'earnings','bonuses','penalties','deductions','split',
        ];
    }

    private function financialRelations(): array
    {
        return ['earnings','bonuses','penalties','deductions','split'];
    }

    private function superAdminData(): array
    {
        $performances = Performance::query()->with($this->performanceRelations())->get();
        return [
            'view'=>'super_admin',
            'total_models'=>$performances->count(),
            'ranking'=>$performances->sortByDesc('ranking_score')->take(10)->values(),
            'models'=>$performances->map(fn(Performance $p)=>$this->formatPerformance($p)),
            'active_performances'=>$this->activePerformances(),
        ];
    }

    private function studioData(User $user,string $view): array
    {
        $performances = Performance::query()
            ->where('studio_id',$user->studio_id)
            ->with($this->performanceRelations())
            ->get();

        return [
            'view'=>$view,
            'total_models'=>$performances->count(),
            'ranking'=>$performances->sortByDesc('ranking_score')->take(10)->values(),
            'models'=>$performances->map(fn(Performance $p)=>$this->formatPerformance($p,$view)),
            'active_performances'=>$this->activePerformances(),
        ];
    }

    private function performanceData(User $user): array
    {
        $performance = $user->performance()->with($this->performanceRelations())->first();
        if(!$performance) return ['view'=>'performance','message'=>'Performance no asociada'];
        return array_merge(['view'=>'performance'],$this->formatPerformance($performance));
    }

    private function formatPerformance(Performance $performance,?string $view=null): array
    {
        $stats=$this->statistics->performanceStats($performance);
        $summary=$this->financialSummary->summary($performance);

        return [
            'id'=>$performance->id,
            'first_name'=>$performance->first_name,
            'last_name'=>$performance->last_name,
            'name'=>trim($performance->first_name.' '.$performance->last_name),
            'nickname'=>$performance->nickname,
            'ranking'=>$performance->ranking_score,
            'hours'=>$performance->hours_streamed,
            'platforms'=>$performance->platforms,
            'financial'=>$summary,
            'statistics'=>$view==='monitor'
                ? [
                    'today'=>[
                        'gross_usd'=>$stats['today']['gross_usd']??0,
                        'model_usd'=>$stats['today']['model_usd']??0,
                    ],
                    'weekly'=>[
                        'gross_usd'=>$stats['weekly']['gross_usd']??0,
                        'model_usd'=>$stats['weekly']['model_usd']??0,
                    ],
                ]
                : $stats,
        ];
    }

    private function financeData(): array
    {
        $performances=Performance::with($this->financialRelations())->get();
        $earnings=$bonuses=$penalties=$deductions=$net=$modelShare=$studioShare=$pending=0;

        foreach($performances as $performance){
            $summary=$this->financialSummary->summary($performance);
            $earnings+=$summary['gross_usd'];
            $bonuses+=$summary['bonus_usd'];
            $penalties+=$summary['penalty_usd'];
            $deductions+=$summary['deduction_usd'];
            $net+=$summary['net_usd'];
            $modelShare+=$summary['model_share_usd'];
            $studioShare+=$summary['studio_share_usd'];
            $pending+=$performance->earnings()->where('status','pending')->sum('gross_usd');
        }

        return [
            'totals'=>[
                'earnings'=>round($earnings,2),
                'bonuses'=>round($bonuses,2),
                'penalties'=>round($penalties,2),
                'deductions'=>round($deductions,2),
            ],
            'net_balance'=>round($net,2),
            'distribution'=>[
                'models'=>round($modelShare,2),
                'studio'=>round($studioShare,2),
            ],
            'installments'=>[
                'active'=>0,
                'pending_amount'=>round($pending,2),
            ],
        ];
    }

    private function operationData(): array
    {
        $shifts=$this->shiftService->active();

        return [
            'total_active_shifts'=>$shifts->count(),
            'active_models'=>$shifts->where('status',Shift::STATUS_ACTIVE)->count(),
            'paused_models'=>$shifts->where('status',Shift::STATUS_PAUSED)->count(),
            'active_shifts'=>$shifts
                ->filter(fn(Shift $shift)=>$shift->performance!==null)
                ->map(fn(Shift $shift)=>[
                    'id'=>$shift->id,
                    'status'=>$shift->status,
                    'started_at'=>$shift->started_at,
                    'ended_at'=>$shift->ended_at,
                    'duration_minutes'=>$shift->durationMinutes(),
                    'performance'=>[
                        'id'=>$shift->performance->id,
                        'name'=>trim($shift->performance->first_name.' '.$shift->performance->last_name),
                        'nickname'=>$shift->performance->nickname,
                        'platforms'=>$shift->performance->platforms
                            ->map(fn($platform)=>[
                                'id'=>$platform->id,
                                'name'=>$platform->name,
                                'type'=>$platform->type,
                            ])
                            ->values()
                            ->toArray(),
                        'financial'=>$this->financialSummary->summary($shift->performance),
                    ],
                    'studio'=>$shift->studio
                        ? ['id'=>$shift->studio->id,'name'=>$shift->studio->name]
                        : null,
                    'actions'=>[
                        'can_pause'=>$shift->isActive(),
                        'can_resume'=>$shift->isPaused(),
                        'can_finish'=>!$shift->isFinished(),
                    ],
                ])
                ->values()
                ->toArray(),
        ];
    }

    private function activePerformances(): array
    {
        return Shift::query()
            ->whereIn('status',[Shift::STATUS_ACTIVE,Shift::STATUS_PAUSED])
            ->with('performance')
            ->get()
            ->map(fn(Shift $shift)=>[
                'id'=>$shift->performance->id,
                'name'=>trim($shift->performance->first_name.' '.$shift->performance->last_name),
                'nickname'=>$shift->performance->nickname,
                'status'=>$shift->status,
            ])
            ->values()
            ->toArray();
    }
}
