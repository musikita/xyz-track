<?php

namespace Suitcorecms\Resources\Contracts;

interface Resourceable
{
    public function getName();

    public function getCaption();

    public function datatablesQuery();
}
