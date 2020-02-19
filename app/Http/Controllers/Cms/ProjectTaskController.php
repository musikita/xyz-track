<?php

namespace App\Http\Controllers\Cms;

use App\Models\Division;
use App\Models\Project;
use App\Models\Task;
use Suitcorecms\ControllerTagging\ControllerTaggingContract;
use Suitcorecms\ControllerTagging\ControllerTaggingTrait;

class ProjectTaskController extends Controller implements ControllerTaggingContract
{
    use ControllerTaggingTrait;

    protected $tagging_id = 'project_id';

    protected $baseRedirectRoute = 'cms.project.show';

    public function baseTag()
    {
        return app(Project::class)->newQueryWithoutRelationships();
    }

    public function taskJs()
    {
        return <<<'JavaScript'
            $('#input-task').on('select2:select', function () {
                console.log($(this).val());
            });
JavaScript;
    }
    public function fields($method)
    {
        return $this->taggingFields([
            'Divisi' => [
                'name' => 'division_id',
                'type' => 'select2',
                'options' => [$this, 'getDivisionOptions'],
            ],
            'Segment' => [
                'name' => 'code',
                'type' => 'text',
            ],
            'Pekerjaan' => [
                'name' => 'task',
                'type' => 'select2',
                'options' => [$this, 'getTaskOptions'],
                'javascript' => [$this, 'taskJs'],
            ],
            'Code' => [
                'name' => 'code',
                'type' => 'text',
            ],
            'Nama' => [
                'name' => 'name',
                'type' => 'text',
            ],
            'Satuan' => [
                'name' => 'unit',
                'type' => 'text',
            ],
            'Biaya per Satuan' => [
                'name' => 'cost_per_unit',
                'type' => 'number',
                'attributes' => [
                    'step' => '.01',
                    'min' => '0',
                ],
            ],
            'Nilai' => [
                'name' => 'value',
                'type' => 'number',
                'attributes' => [
                    'step' => '.01',
                    'min' => '0',
                ],
            ],
            'Total Biaya' => [
                'name' => 'total_cost',
                'type' => 'number',
                'attributes' => [
                    'readonly' => true
                ],
            ],
        ]);
    }

    public function getDivisionOptions()
    {
        return Division::get()->pluck('name', 'id')->toArray();
    }

    public function getTaskOptions()
    {
        return Task::get()->pluck('name', 'id')->toArray();
    }
}
