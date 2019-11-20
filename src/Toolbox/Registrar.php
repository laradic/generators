<?php

namespace Laradic\Generators\Toolbox;

class Registrar
{
    protected $signature;
    protected $signatures;
    protected $language = 'php'; // 'php'|'twig'

    public function signature($signature)
    {
        if ( ! $this->signature) {
            $this->signature = [];
        }
        $this->signature[] = $signature;
        return $this;
    }

    public function signatures(RegistrarSignature $signature = null)
    {
        if ( ! $this->signatures) {
            $this->signatures = [];
        }
        $complexSignature   = new RegistrarSignature();
        $this->signatures[] = $complexSignature;
        return $complexSignature;
    }

    public function language($language)
    {
        $this->language = $language;
        return $this;
    }
}