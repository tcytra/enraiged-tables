<?php

namespace Enraiged\Tables\Builders\Traits;

use Enraiged\Tables\Collections\TableRequestCollection;

trait HttpRequest
{
    /** @var  TableRequestCollection  The collected http request. */
    protected TableRequestCollection $request;

    /** @var  string  The url or route for the table data fetch request. */
    protected array|string $fetch;

    /**
     *  Return the request instance.
     *
     *  @return \Enraiged\Tables\Collections\TableRequestCollection
     */
    public function request(): TableRequestCollection
    {
        return $this->request;
    }
}
