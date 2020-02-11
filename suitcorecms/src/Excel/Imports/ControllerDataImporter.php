<?php

namespace Suitcorecms\Excel\Imports;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ControllerDataImporter implements ToCollection, WithMultipleSheets, SkipsUnknownSheets, WithHeadingRow
{
    use Importable;

    protected $controller;
    protected $resource;
    protected $sheet;
    protected $fields = [];
    protected $fieldList;
    protected static $beforeProcessing;
    protected static $insertExceptionHandler;
    protected static $updateExceptionHandler;

    public function __construct($controller, $sheet = null)
    {
        $this->setupController($controller);
        $this->sheet = $sheet;
        HeadingRowFormatter::default('none');
    }

    public function setupController($controller)
    {
        $this->controller = $controller;
        $resource = $controller->prepResource('create');
        $this->resource = $resource;
        $this->fieldList = null;
        $this->fields = [];
    }

    public function sheets(): array
    {
        return [
            $this->sheet ?? 0 => $this,
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        info("Sheet {$sheetName} not found.");
    }

    protected function getFieldList()
    {
        if ($this->fieldList) {
            return $this->fieldList;
        }

        $fields = collect($this->resource->fields());
        $fields->where('type', '=', 'dropdownmultigroup')->each(function ($field) use ($fields) {
            if ($groups = $field['groups'] ?? false) {
                foreach ($groups as $title => $group) {
                    foreach ($group as $title2 => $groupField) {
                        $this->resource->addFields([
                            $field['title'].' '.$title.' '.$title2 => $groupField,
                        ]);
                    }
                }
            }
        });

        return $this->fieldList = $this->resource->fields(null, true);
    }

    protected function getFieldNameOf($key)
    {
        $fields = $this->getFieldList();
        foreach ($fields as $field) {
            if ($field['name'] == $key) {
                return $field['title'];
            }
        }

        return false;
    }

    protected function getEvaluatedRelationModel($model, $value)
    {
        $field = $model->getCaptionField();
        $class = get_class($model);
        if (method_exists($model, 'getTranslateableAttributes')) {
            if (in_array($field, $model->getTranslateableAttributes())) {
                return $class::joinTranslation()->where($field, $value)->first();
            }
        }

        return $class::where($field, $value)->first();
    }

    public function getRelated($relation, $value, $multiple)
    {
        $model = $this->resource;
        $relations = is_string($relation) ? explode('.', $relation) : $relation;
        foreach ($relations as $relation) {
            $model = $model->{$relation}();
            if ($model instanceof Relation) {
                $model = $model->getRelated();
            }
        }
        if ($multiple) {
            $evaluated = collect([]);
            collect(explode(',', $value))->each(function ($val) use ($evaluated, $model) {
                $evaluated->push($this->getEvaluatedRelationModel($model, trim($val)));
            });

            return $evaluated->pluck('id')->toArray();
        }

        return $this->getEvaluatedRelationModel($model, trim($value))->id ?? null;
    }

    protected function getFieldValue($field, $value)
    {
        $value = trim($value);

        switch ($field['type'] ?? 'text') {
            case 'boolean':
                if (in_array(strtolower((string) $value), [1, 'true', 'yes'])) {
                    return true;
                }

                return false;
                break;

            case 'images':
                return \Facades\Suitcorecms\Excel\Imports\Fields\Images::getImagesFieldValue($field, $value);
                break;

            default:
                if (empty($value)) {
                    return null;
                }

                return $value;
                break;
        }
    }

    protected function getOriginalData($key, $value)
    {
        $keys = null;

        if (($this->fields[$key] ?? null) !== null) {
            $keys = $this->fields[$key];
        } else {
            $fields = $this->resource->fields(null, true);
            foreach ($fields as $field) {
                if ($field['title'] == $key) {
                    $keys = $this->fields[$key] = $field;
                }
            }
        }
        if ($keys) {
            if ($relation = $keys['relation'] ?? false) {
                if ($related = $this->getRelated($relation, $value, $keys['multiple'] ?? false)) {
                    return [$keys['name'], $related];
                }

                return [$keys['name'], null];
            }

            if ($options = $keys['options'] ?? false) {
                if (is_callable($options)) {
                    $options = call_user_func($options);
                }

                return [$keys['name'], collect($options)->search($value)];
            }

            return [$keys['name'], $this->getFieldValue($keys, $value)];
        }

        return [null, null];
    }

    protected function flatQueryArray(array $input = [])
    {
        $flatten = [];
        foreach ($input as $key => $value) {
            if ($start = strpos($key, '[')) {
                $keys = explode('[', str_replace(']', '', rtrim($key, ']')));
                $newKey = array_shift($keys);
                $lastKey = count($keys) == 1 ? $keys[0] : implode('][', $keys).']';
                $newValue = $this->flatQueryArray([$lastKey => $value]);
                if (isset($flatten[$newKey])) {
                    $flatten[$newKey] = array_merge_recursive($flatten[$newKey], $newValue);
                } else {
                    $flatten[$newKey] = $newValue;
                }
            } else {
                $flatten[$key] = $value;
            }
        }

        return $flatten;
    }

    protected function request($data = [])
    {
        $input = [];
        foreach ($data as $key => $value) {
            if (is_numeric($key) || $value === null) {
                continue;
            }

            list($inputKey, $inputValue) = $this->getOriginalData($key, $value);
            if ($inputKey) {
                $input[$inputKey] = $inputValue;
            }
        }

        $input = $this->flatQueryArray($input);

        $request = new \Illuminate\Http\Request();
        $request->replace($input);

        return $request;
    }

    protected function keyData($data)
    {
        $keyName = $this->resource->getKeyName();

        if ($key = $this->getFieldNameOf($keyName)) {
            return $data[$key];
        }

        if ($data[$keyName] ?? false) {
            return $data[$keyName];
        }

        if ($data[$change = strtolower(str_replace('_', ' ', $keyName))] ?? false) {
            return $data[$change];
        }

        if ($data[$change = ucwords(str_replace('_', ' ', $keyName))] ?? false) {
            return $data[$change];
        }

        if ($data[$change = strtoupper(str_replace('_', ' ', $keyName))] ?? false) {
            return $data[$change];
        }

        return false;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $caption = $this->getFieldNameOf($this->resource->getCaptionField());
        $collection->map(function ($row) use ($caption) {
            $data = $row->toArray();
            if (is_callable(static::$beforeProcessing)) {
                call_user_func_array(static::$beforeProcessing, [$data, $this]);
            }

            if ($key = $this->keyData($data)) {
                $this->resource = $this->controller->prepResource('update', $key);
                if (method_exists($this->controller, 'importFields')) {
                    $this->resource->addFields($this->controller->importFields($this->resource->getResourceable(), 'update'));
                }
                $requestData = $this->request($data);

                try {
                    $this->resource->update($requestData);
                } catch (\Exception $e) {
                    if (is_callable(static::$updateExceptionHandler)) {
                        call_user_func_array(static::$updateExceptionHandler, [$e, $data, $requestData]);
                    } else {
                        call_user_func_array([$this, 'defaultUpdateExceptionHandler'], [$e, $data, $requestData]);
                    }
                }
            } elseif ($data[$caption] ?? false) {
                $this->resource = $this->controller->prepResource('create');
                if (method_exists($this->controller, 'importFields')) {
                    $this->resource->addFields($this->controller->importFields($this->resource->getResourceable(), 'create'));
                }
                $requestData = $this->request($data);

                try {
                    $this->resource->create($requestData);
                } catch (\Exception $e) {
                    if (is_callable(static::$insertExceptionHandler)) {
                        call_user_func_array(static::$insertExceptionHandler, [$e, $data, $requestData]);
                    } else {
                        call_user_func_array([$this, 'defaultInsertExceptionHandler'], [$e, $data, $requestData]);
                    }
                }
            }
        });
    }

    public static function registerBeforeProcessing($callback)
    {
        static::$beforeProcessing = $callback;
    }

    public static function registerInsertExceptionHandle($handler)
    {
        static::$insertExceptionHandler = $handler;
    }

    public static function registerUpdateExceptionHandle($handler)
    {
        static::$updateExceptionHandler = $handler;
    }

    public function defaultInsertExceptionHandler(Exception $e, array $data = [], $requestData = null)
    {
        info($e->getMessage());
        if ($e instanceof ValidationException) {
            info($e->errors());
            info($requestData);

            return;
        }
        info('Failed to import data via insert to '.get_class($this->controller).' with data '.json_encode($data));
        info($requestData);
    }

    public function defaultUpdateExceptionHandler(Exception $e, array $data = [], $requestData = null)
    {
        info($e->getMessage());
        if ($e instanceof ValidationException) {
            info($e->errors());
            info($requestData);

            return;
        }
        info('Failed to import data via update to '.get_class($this->controller).' with data '.json_encode($data));
        info($requestData);
    }

    public static function process($controller, $file, $sheet = null)
    {
        (new static($controller, $sheet))->import($file);
    }
}
