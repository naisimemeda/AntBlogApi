<?php

namespace App\Console\Commands;

//use Illuminate\Console\Command;
use Illuminate\Foundation\Console\RequestMakeCommand as Command;

class RequestMakeCommand extends Command
{

    protected function getStub()
    {
        return __DIR__.'/stubs/request.stub';
    }

}
