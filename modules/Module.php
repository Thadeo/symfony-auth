<?php
namespace Module;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class Module extends Bundle {

    public function getPath(): string
    {
        // Return the module dir
        return \dirname(__DIR__).'/modules';
    }
}