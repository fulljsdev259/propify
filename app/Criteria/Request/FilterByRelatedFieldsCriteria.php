<?php

namespace App\Criteria\Request;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByRelatedFieldsCriteria
 * @package Prettus\Repository\Criteria
 */
class FilterByRelatedFieldsCriteria implements CriteriaInterface
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $categoryId = $this->request->category_id;
        if ($categoryId) {
            $categoryIds = Arr::wrap($categoryId);
            $model->whereIn('category_id', $categoryIds);
        }

        $residentId = $this->request->get('resident_id', null);
        if ($residentId) {
            $model->where('resident_id', $residentId);
        }

        $unitId = $this->request->get('unit_id', null);
        if ($unitId) {
            $model->whereHas('relation', function ($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        }

        $myRequests = $this->request->get('my_request');
        $providerIds = [];
        $managerIds = [];
        if ($myRequests) {
            $user = Auth::user();
            $user->load('propertyManager', 'serviceProvider');
            if ($user->propertyManager) {
                $managerIds[] = $user->propertyManager->id;
            } elseif ($user->serviceProvider) {
                $providerIds[] = $user->serviceProvider->id;
            }
        }

        $providerId = $this->request->get('service_provider_id', null) ?? $this->request->get('service_id', null);

        if ($providerId) {
            $providerIds[] = $providerId;
        }

        if ($providerIds) {
            $model->whereHas('service_providers', function ($q) use ($providerIds) {
                $q->whereIn('service_providers.id', $providerIds);
            });
        }

        // @TODO need filter to property manager or user also rename
        $managerId = $this->request->get('property_manager_id', null);

        if ($managerId) {
            $managerIds[] = $managerId;
        }
        if ($managerIds) {
            $model->whereHas('property_managers', function ($q) use ($managerIds) {
                $q->whereIn('property_managers.id', $managerIds);
            });
        }

        $buildingId = $this->request->get('building_id', null);
        if ($buildingId) {
            $model->whereHas('relation', function ($query) use ($buildingId) {
                $query->where('building_id', $buildingId);
            });
        }

        $quarterId = $this->request->get('quarter_id', null);
        if ($quarterId) {
            $model->whereHas('relation', function ($query) use ($quarterId) {
                $query->whereHas('building', function ($query) use ($quarterId) {
                    $query->where('quarter_id', $quarterId);
                });
            });
        }

        return $model;
    }
}
