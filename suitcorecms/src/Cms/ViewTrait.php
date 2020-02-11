<?php

namespace Suitcorecms\Cms;

trait ViewTrait
{
    protected $viewOfIndex = 'suitcorecms::crud.datatable';
    protected $viewOfCreate = 'suitcorecms::crud.form';
    protected $viewOfUpdate = 'suitcorecms::crud.form';
    protected $viewOfShow = 'suitcorecms::crud.show';

    protected function view(array $datas = [])
    {
        return view('suitcorecms::index', $datas);
    }
}
